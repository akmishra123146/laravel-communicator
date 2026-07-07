<?php

namespace Communicator\Console\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communication:install 
                            {--otp : Install the OTP module}
                            {--mail : Install the Mail module}
                            {--sms : Install the SMS module}
                            {--push : Install the Push Notification module}
                            {--notification : Install the In-App Notification module}
                            {--whatsapp : Install the WhatsApp module}
                            {--template : Install the Template module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Laravel Communicator package and publish necessary files for chosen modules.';

    /**
     * The available modules.
     *
     * @var array
     */
    protected $availableModules = [
        'otp' => 'OTP',
        'mail' => 'Email',
        'sms' => 'SMS',
        'push' => 'Push Notification',
        'notification' => 'In-App Notification',
        'whatsapp' => 'WhatsApp',
        'template' => 'Templates',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info('Installing Laravel Communicator...');

        $selectedModules = [];

        // Check for options passed via command line
        foreach (array_keys($this->availableModules) as $module) {
            if ($this->option($module)) {
                $selectedModules[] = $module;
            }
        }

        // If no options were passed, prompt the user
        if (empty($selectedModules)) {
            $selectedModules = multiselect(
                label: 'Which modules do you want to enable?',
                options: $this->availableModules,
                default: ['otp', 'mail'],
                required: 'You must select at least one module to install.'
            );
        }

        if (empty($selectedModules)) {
            $this->error('No modules selected. Installation aborted.');
            return;
        }

        note('Enabling modules: ' . implode(', ', $selectedModules));

        $this->updateConfigurationFile($selectedModules);
        $this->publishFiles($selectedModules);

        info('Laravel Communicator installed successfully!');
    }

    /**
     * Update the configuration file with selected modules.
     */
    protected function updateConfigurationFile(array $modules)
    {
        // First publish config if it doesn't exist
        if (!file_exists(config_path('communication.php'))) {
            $this->call('vendor:publish', [
                '--tag' => 'communication-config',
                '--force' => true, // Ensure it's published if we're installing
            ]);
        }

        $configPath = config_path('communication.php');
        
        if (!file_exists($configPath)) {
            $this->error('Configuration file not found. Could not update active modules.');
            return;
        }

        $config = file_get_contents($configPath);

        foreach ($this->availableModules as $key => $name) {
            $status = in_array($key, $modules) ? 'true' : 'false';
            // Simple string replacement to update config (better approach would be array parsing but for this usecase regex/replacement works)
            $config = preg_replace("/'{$key}'\s*=>\s*(true|false),/", "'{$key}' => {$status},", $config);
        }

        file_put_contents($configPath, $config);
    }

    /**
     * Publish files for the selected modules.
     */
    protected function publishFiles(array $modules)
    {
        foreach ($modules as $module) {
            $this->callSilent('communication:publish', ['--module' => $module]);
            note("Published {$module} module files.");
        }
    }
}
