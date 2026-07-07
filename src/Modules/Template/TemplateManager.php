<?php

namespace Communicator\Modules\Template;

class TemplateManager
{
    protected $app;
    protected $templateName;
    protected $recipient;
    protected $data = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function load(string $templateName)
    {
        $this->templateName = $templateName;
        return $this;
    }

    public function to($recipient)
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function with(array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function send($channels = ['mail'])
    {
        // For demonstration, iterate over channels and dispatch the template
        $results = [];

        foreach ((array) $channels as $channel) {
            try {
                $channelManager = $this->app->make("communication.{$channel}");
                // In a real implementation, we'd compile the template here
                // e.g. View::make("communication::templates.{$channel}.{$this->templateName}", $this->data)->render()
                $content = "This is the compiled template {$this->templateName}";

                if (method_exists($channelManager, 'send')) {
                    $results[$channel] = $channelManager->send($this->recipient, $content);
                }
            } catch (\Exception $e) {
                $results[$channel] = false;
            }
        }

        return $results;
    }
}
