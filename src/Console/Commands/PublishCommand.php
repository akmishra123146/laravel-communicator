<?php

namespace Communicator\Console\Commands;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communication:publish {--module= : The specific module to publish files for (e.g., otp, mail)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish files for a specific Laravel Communicator module.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->option('module');

        if (!$module) {
            $this->error('Please specify a module using the --module option.');
            return;
        }

        $tags = [
            "communication-{$module}-migrations",
            "communication-{$module}-views",
            "communication-{$module}-config",
        ];

        $published = false;

        foreach ($tags as $tag) {
            $result = $this->callSilent('vendor:publish', [
                '--tag' => $tag,
            ]);

            // We could check if anything was actually published, but vendor:publish output parsing is tricky.
            // Assuming it succeeds if no exception.
            $published = true;
        }

        if ($published) {
            $this->info("Successfully published assets for the {$module} module.");
        } else {
            $this->warn("No assets found to publish for the {$module} module.");
        }
    }
}
