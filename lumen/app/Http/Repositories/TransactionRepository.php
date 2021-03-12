<?php

namespace App\Http\Repositories;

use App\Http\Services\AuthorizationTransationServices;
use App\Jobs\ProcessNotification;
use App\Models\Transaction;

class TransactionRepository
{
    public function __construct()
    {
        $this->model = app(Transaction::class);
        $this->repositoryWallet = app(WalletRepository::class);
    }

    /**
     * deposit
     *
     * @param  mixed $request
     * @param  int $userId
     * @return void
     */
    public function deposit($request, $userId)
    {
        try {
            $request['payee'] = $userId;
            $request['type'] = $this->model::TYPE_DEPOSIT;
            $deposit = $this->model::create($request);
            $this->repositoryWallet->updateBalance($this->model::TYPE_DEPOSIT, $request['amount'], $userId);
            return $deposit;
        } catch (\Exception $e) {
            Log::error('Error when try make withdraw.', ['message' => $e->getMessage()]);
        }
        return false;
    }

    /**
     * withdraw
     *
     * @param  mixed $request
     * @param  int $userId
     * @return void
     */
    public function withdraw($request, $userId)
    {
        try {
            $request['payee'] = $userId;
            $request['type'] = $this->model::TYPE_WITHDRAW;
            $withdraw = $this->model::create($request);
            $this->repositoryWallet->updateBalance($this->model::TYPE_WITHDRAW, $request['amount'], $userId);
            return $withdraw;
        } catch (\Exception $e) {
            Log::error('Error when try make withdraw.', ['message' => $e->getMessage()]);
        }
        return false;
    }

    /**
     * transfer
     *
     * @param  mixed $request
     * @param  int $payerId
     * @param  int $payeeId
     * @return void
     */
    public function transfer($request, $payerId, $payeeId)
    {
        try {
            $request['payer'] = $payerId;
            $request['payee'] = $payeeId;
            $request['type'] = $this->model::TYPE_TRANSFER;

            if (!$this->verifyAuthorizationToTransfer(
                $payerId,
                $request['amount'],
                $this->model::TYPE_TRANSFER
            )) {
                return false;
            }

            $transfer = $this->model::create($request);
            if ($transfer) {
                $this->repositoryWallet->updateBalance($this->model::TYPE_WITHDRAW, $request['amount'], $payerId);
                $this->repositoryWallet->updateBalance($this->model::TYPE_DEPOSIT, $request['amount'], $payeeId);
                dispatch(new ProcessNotification($payeeId, 'transfer'));
                return $transfer;
            }
        } catch (\Exception $e) {
            Log::error('Error when try make transfer.', ['message' => $e->getMessage()]);
        }
        return false;
    }

    /**
     * chargeback
     *
     * @param  int $transferId
     * @return void
     */
    public function chargeback($transferId)
    {
        try {
            $transfer = $this->model::find($transferId);

            if ($transfer['type'] == $this->model::TYPE_CHARGEBACK) {
                return false;
            }

            $payeeId = $transfer['payee'];
            $payerId = $transfer['payer'];

            if (!$this->verifyAuthorizationToTransfer($this->model::TYPE_TRANSFER, $payerId)) {
                return false;
            }


            $transfer->update(['type' => $this->model::TYPE_CHARGEBACK]);
            if ($transfer) {
                $this->repositoryWallet->updateBalance($this->model::TYPE_DEPOSIT, $transfer['amount'], $payerId);
                $this->repositoryWallet->updateBalance($this->model::TYPE_WITHDRAW, $transfer['amount'], $payeeId);
                dispatch(new ProcessNotification($payeeId, 'chargeback'));
                return $transfer;
            }
        } catch (\Exception $e) {
            Log::error('Error when try make chargeback.', ['message' => $e->getMessage()]);
        }
        return false;
    }

    /**
     * verifyAuthorizationToTransfer
     *
     * @param  int $type
     * @param  int $payerId
     * @param  float $amount
     * @return void
     */
    private function verifyAuthorizationToTransfer($type, $payerId, $amount = null)
    {
        $authorizationTransationServices = new AuthorizationTransationServices();
        if ($authorizationTransationServices->send($payerId, $amount, $type)) {
            return true;
        }

        return false;
    }
}
