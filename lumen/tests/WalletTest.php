<?php

use App\Models\User;
use App\Models\Wallet;
use Laravel\Lumen\Testing\DatabaseTransactions;

class WalletTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_get_balance()
    {
        $radom_amount = rand(1, 9999);
        $user = User::factory()->create();

        $this->actingAs($user)->get('/api/profile');

        $wallet = Wallet::factory()->make(['user_id' =>$user->id, 'amount' => $radom_amount]);

        Wallet::create($wallet->toArray());

        $response = $this->call('GET', '/api/balance');
        $response_amount = $response->getContent();

        $this->assertEquals($wallet->amount, $response_amount);
    }
}
