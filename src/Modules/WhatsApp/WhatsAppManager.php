<?php

namespace Communicator\Modules\WhatsApp;

use Illuminate\Support\Manager;
use Communicator\Modules\WhatsApp\Contracts\WhatsAppDriver;

class WhatsAppManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('communication.defaults.whatsapp', 'meta');
    }

    /**
     * Send a WhatsApp message.
     *
     * @param string $to
     * @param string $message
     * @return mixed
     */
    public function send(string $to, string $message)
    {
        return $this->driver()->send($to, $message);
    }

    /**
     * Send an OTP via WhatsApp.
     */
    public function sendOtp(string $to, string $otp)
    {
        $message = "Your OTP is {$otp}";
        return $this->send($to, $message);
    }
}
