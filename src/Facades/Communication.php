<?php

namespace Communicator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Communicator\Modules\OTP\OtpManager otp()
 * @method static \Communicator\Modules\Mail\MailManager mail()
 * @method static \Communicator\Modules\SMS\SmsManager sms()
 * @method static \Communicator\Modules\Notification\NotificationManager notification()
 * @method static \Communicator\Modules\Template\TemplateManager template()
 * @method static \Communicator\Modules\WhatsApp\WhatsAppManager whatsapp()
 * @method static \Communicator\Modules\Push\PushManager push()
 * @method static mixed channel(string $channel)
 * 
 * @see \Communicator\CommunicationManager
 */
class Communication extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'communication';
    }
}
