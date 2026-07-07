<?php

namespace Communicator;

use Illuminate\Support\ServiceProvider;
use Communicator\Console\Commands\InstallCommand;
use Communicator\Console\Commands\PublishCommand;
use Communicator\Modules\OTP\OtpManager;
use Communicator\Modules\SMS\SmsManager;
use Communicator\Modules\Mail\MailManager;
use Communicator\Modules\Notification\NotificationManager;
use Communicator\Modules\Template\TemplateManager;
use Communicator\Modules\WhatsApp\WhatsAppManager;
use Communicator\Modules\Push\PushManager;

class CommunicatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/communication.php', 'communication');

        $this->app->singleton('communication', function ($app) {
            return new CommunicationManager($app);
        });

        // Register modules conditionally based on configuration
        if (config('communication.modules.otp', false)) {
            $this->app->singleton('communication.otp', function ($app) {
                return new OtpManager($app);
            });
        }
        if (config('communication.modules.mail', false)) {
            $this->app->singleton('communication.mail', function ($app) {
                return new MailManager($app);
            });
        }
        if (config('communication.modules.sms', false)) {
            $this->app->singleton('communication.sms', function ($app) {
                return new SmsManager($app);
            });
        }
        if (config('communication.modules.notification', false)) {
            $this->app->singleton('communication.notification', function ($app) {
                return new NotificationManager($app);
            });
        }
        if (config('communication.modules.push', false)) {
            $this->app->singleton('communication.push', function ($app) {
                return new PushManager($app);
            });
        }
        if (config('communication.modules.whatsapp', false)) {
            $this->app->singleton('communication.whatsapp', function ($app) {
                return new WhatsAppManager($app);
            });
        }
        if (config('communication.modules.template', false)) {
            $this->app->singleton('communication.template', function ($app) {
                return new TemplateManager($app);
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/communication.php' => config_path('communication.php'),
            ], 'communication-config');

            $this->commands([
                InstallCommand::class,
                PublishCommand::class,
                \Communicator\Console\Commands\MakeDriverCommand::class,
            ]);
            
            // Publish specific module migrations if enabled
            if (config('communication.modules.otp', false)) {
                $this->publishes([
                    __DIR__ . '/Modules/OTP/Migrations/' => database_path('migrations')
                ], 'communication-otp-migrations');
            }
        }
    }
}
