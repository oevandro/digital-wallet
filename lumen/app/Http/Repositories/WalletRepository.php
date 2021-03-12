<?php

namespace App\Http\Repositories;

use App\Models\Wallet;
use App\Models\Transaction;

class WalletRepository
{
    public function __construct()
    {
        $this->model = app(Wallet::class);
        $this->modelTransaction = app(Transaction::class);
    }

    /**
     * where
     *
     * @param  array $data
     * @return object
     */
    public function where($data = [])
    {
        return $this->model->where($data)->get();
    }

    /**
     * updateBalance
     *
     * @param  string $type
     * @param  float $amount
     * @param  int $userId
     * @return bool
     */
    public function updateBalance($type, $amount, $userId)
    {
        try {
            $wallet = $this->model->where(['user_id' => $userId]);

            if ($type == $this->modelTransaction::TYPE_DEPOSIT) {
                $increment = $wallet->increment('amount', $amount);
                if ($increment) {
                    return true;
                }
            } elseif ($type == $this->modelTransaction::TYPE_WITHDRAW) {
                $decrement = $wallet->decrement('amount', $amount);
                if ($decrement) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error when try update wallet balance.', ['message' => $e->getMessage()]);
        }
        return false;
    }
}
