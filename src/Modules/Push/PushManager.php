<?php

namespace Communicator\Modules\Push;

use Illuminate\Support\Manager;
use Communicator\Modules\Push\Contracts\PushDriver;

class PushManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('communication.defaults.push', 'firebase');
    }

    /**
     * Send a Push Notification.
     *
     * @param string $to
     * @param array $payload
     * @return mixed
     */
    public function send(string $to, array $payload)
    {
        return $this->driver()->send($to, $payload);
    }
}
