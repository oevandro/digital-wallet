<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Wallet;

class TransactionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_make_deposit()
    {
        $radom_amount = rand(1, 9999);
        $user = User::factory()->create();
        $this->actingAs($user)->get('/api/profile');
        Wallet::factory()->create(['user_id' => $user->id]);
        $response = $this->call('POST', '/api/deposit', ['amount' => $radom_amount]);
        $this->assertEquals(200, $response->status());
    }

    public function test_can_make_withdraw()
    {
        $amount = 999;
        $user = User::factory()->create();
        $this->actingAs($user)->get('/api/profile');
        Wallet::factory()->create(['user_id' => $user->id, 'amount' => $amount]);
        $response = $this->call('POST', '/api/withdraw', ['amount' => $amount]);
        $this->assertEquals(200, $response->status());
    }

    public function test_can_make_transfer()
    {
        $amount = 999;
        $userPayer = User::factory()->create(['type'=> User::TYPE_COMMON]);
        $this->actingAs($userPayer)->get('/api/profile');
        $userPayee = User::factory()->create();

        //wallet payer
        Wallet::factory()->create(['user_id' => $userPayer->id, 'amount' => $amount]);
        //Wallet payee
        Wallet::factory()->create(['user_id' => $userPayee->id, 'amount' => $amount]);

        $response = $this->call('POST', '/api/transfer', ['amount' => $amount, 'payee' => $userPayee->id]);
        $this->assertEquals(200, $response->status());
    }

    public function test_cant_make_transfer_user_shopp()
    {
        $amount = 999;
        $userPayer = User::factory()->create(['type'=> User::TYPE_SHOPP]);
        $this->actingAs($userPayer)->get('/api/profile');
        $userPayee = User::factory()->create();

        //wallet payer
        Wallet::factory()->create(['user_id' => $userPayer->id, 'amount' => $amount]);
        //Wallet payee
        Wallet::factory()->create(['user_id' => $userPayee->id, 'amount' => $amount]);

        $response = $this->call('POST', '/api/transfer', ['amount' => $amount, 'payee' => $userPayee->id]);
        $this->assertEquals(400, $response->status());
    }

    public function test_can_make_chargeback()
    {
        $amount = 999;
        $userPayer = User::factory()->create(['type'=> User::TYPE_COMMON]);
        $this->actingAs($userPayer)->get('/api/profile');
        $userPayee = User::factory()->create();

        //wallet payer
        Wallet::factory()->create(['user_id' => $userPayer->id, 'amount' => $amount]);
        //Wallet payee
        Wallet::factory()->create(['user_id' => $userPayee->id, 'amount' => $amount]);

        $responseTransfer = $this->call('POST', '/api/transfer', ['amount' => $amount, 'payee' => $userPayee->id]);

        $response = $this->call('POST', '/api/chargeback', ['transfer_id'  => $responseTransfer['id']]);
        $this->assertEquals(200, $response->status());
    }
}
