<?php

namespace Communicator\Modules\SMS;

use Illuminate\Support\Manager;
use Communicator\Modules\SMS\Contracts\SmsDriver;

class SmsManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('communication.defaults.sms', 'twilio');
    }

    /**
     * Send an SMS using the default or specified driver.
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
     * Send an OTP via SMS (used by OTP Manager).
     */
    public function sendOtp(string $to, string $otp)
    {
        $message = "Your OTP is {$otp}";
        return $this->send($to, $message);
    }
}
