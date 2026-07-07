<?php

namespace Communicator\Modules\Mail;

use Illuminate\Support\Facades\Mail;

class MailManager
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
     * Send an email.
     *
     * @param mixed $to
     * @param string|object $templateOrMailable
     * @param array $data
     * @return void
     */
    public function send($to, $templateOrMailable, array $data = [])
    {
        // If it's already a mailable, send it directly
        if (is_object($templateOrMailable)) {
            return Mail::to($to)->send($templateOrMailable);
        }

        // Otherwise, assume it's a template name or view
        // In a full implementation, you'd integrate with TemplateManager
        // For basic usage:
        return Mail::send($templateOrMailable, $data, function($message) use ($to) {
            $message->to(is_string($to) ? $to : (isset($to->email) ? $to->email : $to));
            // Add subject mapping based on template or config
        });
    }

    /**
     * Internal method used by OTP module to send OTP via mail.
     */
    public function sendOtp($to, $otp)
    {
        // For simplicity, using a raw email. 
        // In reality, this would use a Mailable or a Template.
        return Mail::raw("Your OTP is: {$otp}", function($message) use ($to) {
            $message->to(is_string($to) ? $to : (isset($to->email) ? $to->email : $to))
                    ->subject('Your One Time Password');
        });
    }
}
