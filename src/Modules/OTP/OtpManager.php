<?php

namespace Communicator\Modules\OTP;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Communicator\Facades\Communication;

class OtpManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new OTP manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Send an OTP to the given recipient via the specified channel.
     *
     * @param array|string $recipient
     * @param string|null $channel
     * @return bool
     */
    public function send($recipient, $channel = null)
    {
        if (is_array($recipient)) {
            $channel = $recipient['channel'] ?? config('communication.otp.default_channel', 'mail');
            $to = $recipient['to'] ?? null;
        } else {
            $to = $recipient;
            $channel = $channel ?? config('communication.otp.default_channel', 'mail');
        }

        if (!$to) {
            throw new \InvalidArgumentException('Recipient must be specified.');
        }

        $otp = $this->generateOtp();
        $identifier = is_string($to) ? $to : (is_object($to) && method_exists($to, 'routeNotificationFor') ? $to->routeNotificationFor($channel) : json_encode($to));

        $this->storeOtp($identifier, $otp);

        // Send using the specified channel
        // E.g., Communication::channel('mail')->send(...)
        // Since we are mocking/building, we'll assume the channel handles OTP sending properly
        try {
            $channelManager = $this->app->make("communication.{$channel}");
            if (method_exists($channelManager, 'sendOtp')) {
                $channelManager->sendOtp($to, $otp);
            } else {
                // Fallback basic send
                $message = "Your OTP is {$otp}";
                $channelManager->send($to, $message);
            }
            return true;
        } catch (\Exception $e) {
            // Log failure
            return false;
        }
    }

    /**
     * Verify the given OTP for the identifier.
     *
     * @param string $identifier
     * @param string $otp
     * @return bool
     */
    public function verify(string $identifier, string $otp)
    {
        $table = config('communication.otp.table', 'communication_otps');
        $record = DB::table($table)
            ->where('identifier', $identifier)
            ->where('expires_at', '>', Carbon::now())
            ->where('verified', false)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$record) {
            return false;
        }

        if ($record->attempt_count >= config('communication.otp.attempt_limit', 5)) {
            return false;
        }

        $isValid = config('communication.otp.hash', true) 
            ? Hash::check($otp, $record->token) 
            : $otp === $record->token;

        if ($isValid) {
            DB::table($table)->where('id', $record->id)->update([
                'verified' => true,
                'updated_at' => Carbon::now(),
            ]);
            return true;
        }

        DB::table($table)->where('id', $record->id)->increment('attempt_count');
        
        return false;
    }

    /**
     * Generate a new OTP string.
     *
     * @return string
     */
    protected function generateOtp()
    {
        $length = config('communication.otp.length', 6);
        $type = config('communication.otp.type', 'numeric'); // numeric, alphanumeric

        if ($type === 'alphanumeric') {
            return Str::upper(Str::random($length));
        }

        // Default numeric
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return (string) random_int($min, $max);
    }

    /**
     * Store the OTP in the database.
     *
     * @param string $identifier
     * @param string $otp
     * @return void
     */
    protected function storeOtp($identifier, $otp)
    {
        $table = config('communication.otp.table', 'communication_otps');
        $hashed = config('communication.otp.hash', true) ? Hash::make($otp) : $otp;
        $expiryMinutes = config('communication.otp.expiry', 10);

        DB::table($table)->insert([
            'identifier' => $identifier,
            'token' => $hashed,
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes),
            'attempt_count' => 0,
            'verified' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
