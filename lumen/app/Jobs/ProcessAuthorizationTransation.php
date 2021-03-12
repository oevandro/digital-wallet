<?php

namespace App\Jobs;

use App\Http\Services\AuthorizationTransationServices;

class ProcessAuthorizationTransation extends Job
{
    protected $user_id;
    protected $amount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $amount)
    {
        $this->user_id = $user_id;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notificationServices = new AuthorizationTransationServices();
        $notificationServices->send($type, $this->user_id, $this->amount);
    }
}
