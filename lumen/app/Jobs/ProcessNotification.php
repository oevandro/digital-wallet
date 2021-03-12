<?php

namespace App\Jobs;

use App\Http\Services\NotificationServices;

class ProcessNotification extends Job
{
    protected $user_id;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $message)
    {
        $this->user_id = $user_id;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notificationServices = new NotificationServices();
        $notificationServices->send($this->user_id, $this->message);
    }
}
