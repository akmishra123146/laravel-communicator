<?php

namespace Communicator\Modules\Push\Contracts;

interface PushDriver
{
    /**
     * Send a Push Notification message.
     *
     * @param string $to
     * @param array $payload
     * @return mixed
     */
    public function send(string $to, array $payload);
}
