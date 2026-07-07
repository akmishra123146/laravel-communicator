<?php

namespace Communicator\Modules\WhatsApp\Contracts;

interface WhatsAppDriver
{
    /**
     * Send a WhatsApp message.
     *
     * @param string $to
     * @param string $message
     * @return mixed
     */
    public function send(string $to, string $message);
}
