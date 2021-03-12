<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\TransactionRepository;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class TransactionsController extends Controller
{
    public function __construct(TransactionRepository $repository)
    {
        $this->repository = $repository;
        $this->user = auth()->user();
    }

    /**
     * deposit
     *
     * @param  object $request
     * @return void
     */
    public function deposit(Request $request)
    {
        if(!$this->user)
            return response()->json(['message' => 'Not Authorized'], 401);

        $validatedData = $this->validateFields($request->all());
        $deposit = $this->repository->deposit($validatedData, $this->user->id);

        if ($deposit) {
            return response()->json(['message' => 'deposit'], 200);
        }

        return response()->json(['message' => 'error'], 405);
    }

    /**
     * withdraw
     *
     * @param  object $request
     * @return void
     */
    public function withdraw(Request $request)
    {
        if(!$this->user)
            return response()->json(['message' => 'Not Authorized'], 401);

        $validatedData = $this->validateFields($request->all());
        $withdraw = $this->repository->withdraw($validatedData, $this->user->id);

        if ($withdraw) {
            return response()->json(['message' => 'withdraw'], 200);
        }

        return response()->json(['message' => 'error'], 405);
    }

    /**
     * transfer
     *
     * @param  object $request
     * @return void
     */
    public function transfer(Request $request)
    {
        if(!$this->user)
            return response()->json(['message' => 'Not Authorized'], 401);

        if ($this->validateUserToTransfer($request->payer) == false) {
            return response()->make('Not allowed to transfer', 400);
        }

        $validatedData = $this->validateFields($request->all());
        $transfer = $this->repository->transfer($validatedData, $request->payer, $request->payee);
        return $transfer;

        if ($transfer) {
            return response()->json(['message' => 'transfer'], 200);
        }

        return response()->json(['message' => 'error'], 405);
    }

    /**
     * chargeback
     *
     * @param  object $request
     * @return void
     */
    public function chargeback(Request $request)
    {
        if(!$this->user)
            return response()->json(['message' => 'Not Authorized'], 401);

        $transfer = $this->repository->chargeback($request['transfer_id']);

        if ($transfer) {
            return response()->json(['message' => 'chargeback'], 200);
        }

        return response()->json(['message' => 'error'], 405);
    }

    /**
     * validateFields
     *
     * @param  object $request
     * @return void
     */
    private function validateFields($request)
    {
        $rules = ['amount' => 'required|integer'];
        $validation = Validator::make($request, $rules);

        if ($validation->fails()) {
            return response()->make((object) $validation->messages(), 400);
        }

        $validatedData = $validation->validated();
        return $validatedData;
    }

    /**
     * validateUserToTransfer
     *
     * @return void
     */
    private function validateUserToTransfer($payer)
    {
        $user = User::find($payer);
        if ($user['type'] == User::TYPE_SHOPP) {
            return false;
        }

        return true;
    }
}
