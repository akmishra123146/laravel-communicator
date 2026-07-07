<?php

namespace Communicator\Modules\SMS\Contracts;

interface SmsDriver
{
    /**
     * Send an SMS message.
     *
     * @param string $to
     * @param string $message
     * @return mixed
     */
    public function send(string $to, string $message);
}
