<?php

namespace Communicator\Modules\Notification;

use Illuminate\Support\Facades\Notification;

class NotificationManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Send a notification to the given user(s).
     *
     * @param mixed $notifiables
     * @param mixed $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        Notification::send($notifiables, $notification);
    }
}
