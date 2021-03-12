<?php

namespace App\Http\Controllers;

use App\Http\Repositories\WalletRepository;

class WalletController extends Controller
{
    public function __construct(WalletRepository $repository)
    {
        $this->repository = $repository;
        $this->user = auth()->user();
    }


    public function balance()
    {
        $wallet = $this->repository->where(['user_id' => $this->user->id]);

        if ($wallet) {
            return response()->json((int)$wallet[0]->amount, 200);
        }

        return response()->json(['message' => 'error'], 405);
    }
}
