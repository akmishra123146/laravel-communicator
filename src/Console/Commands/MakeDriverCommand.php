<?php

namespace Communicator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeDriverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communication:driver {name : The name of the driver (e.g. MySms)} {--module=sms : The module type (sms, whatsapp, push)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new custom driver for a communication module.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $module = Str::lower($this->option('module'));

        if (!in_array($module, ['sms', 'whatsapp', 'push'])) {
            $this->error("Invalid module type. Allowed types: sms, whatsapp, push");
            return;
        }

        $className = "{$name}Driver";
        
        // In a real application, we would create this in app/Communication/Drivers/
        // Since we are creating a package, we'll guide the user to place it in their App directory
        $path = app_path("Communication/Drivers/{$className}.php");
        
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $stub = $this->getStub($module, $className);
        file_put_contents($path, $stub);

        $this->info("Driver {$className} created successfully at {$path}.");
        $this->comment("Remember to register your driver using Communication::extend('{$module}', function () { return new \\App\\Communication\\Drivers\\{$className}(); });");
    }

    protected function getStub($module, $className)
    {
        $interfaceMap = [
            'sms' => '\\Communicator\\Modules\\SMS\\Contracts\\SmsDriver',
            'whatsapp' => '\\Communicator\\Modules\\WhatsApp\\Contracts\\WhatsAppDriver',
            'push' => '\\Communicator\\Modules\\Push\\Contracts\\PushDriver',
        ];

        $interface = $interfaceMap[$module];
        $interfaceShortName = class_basename($interface);

        $sendMethodStub = $module === 'push' 
            ? "public function send(string \$to, array \$payload)\n    {\n        // Implement push logic here\n    }" 
            : "public function send(string \$to, string \$message)\n    {\n        // Implement {$module} logic here\n    }";

        return <<<PHP
<?php

namespace App\Communication\Drivers;

use {$interface};

class {$className} implements {$interfaceShortName}
{
    /**
     * Create a new {$className} instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Setup any dependencies, e.g. read from config
    }

    /**
     * {$sendMethodStub}
     */
    {$sendMethodStub}
}
PHP;
    }
}
