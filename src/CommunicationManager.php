<?php

namespace Communicator;

use Illuminate\Contracts\Foundation\Application;

class CommunicationManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new Communication manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the OTP module.
     *
     * @return \Communicator\Modules\OTP\OtpManager
     */
    public function otp()
    {
        return $this->app->make('communication.otp');
    }

    /**
     * Get the Mail module.
     *
     * @return \Communicator\Modules\Mail\MailManager
     */
    public function mail()
    {
        return $this->app->make('communication.mail');
    }

    /**
     * Get the SMS module.
     *
     * @return \Communicator\Modules\SMS\SmsManager
     */
    public function sms()
    {
        return $this->app->make('communication.sms');
    }

    /**
     * Get the Notification module.
     *
     * @return \Communicator\Modules\Notification\NotificationManager
     */
    public function notification()
    {
        return $this->app->make('communication.notification');
    }

    /**
     * Get the Template module.
     *
     * @return \Communicator\Modules\Template\TemplateManager
     */
    public function template(string $templateName = null)
    {
        $manager = $this->app->make('communication.template');
        if ($templateName) {
            return $manager->load($templateName);
        }
        return $manager;
    }

    /**
     * Get the WhatsApp module.
     *
     * @return \Communicator\Modules\WhatsApp\WhatsAppManager
     */
    public function whatsapp()
    {
        return $this->app->make('communication.whatsapp');
    }

    /**
     * Get the Push module.
     *
     * @return \Communicator\Modules\Push\PushManager
     */
    public function push()
    {
        return $this->app->make('communication.push');
    }

    /**
     * Get a specific channel (alias for modules).
     *
     * @param string $channel
     * @return mixed
     */
    public function channel(string $channel)
    {
        return $this->app->make("communication.{$channel}");
    }
}
