<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FixDoubleHashedPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-passwords {--email=} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset passwords for users affected by double hashing bug';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('⚠️  This command will reset passwords for affected users.');
        $this->warn('Users will need to use password reset to regain access.');
        $this->newLine();

        if ($this->option('all')) {
            if (!$this->confirm('Reset passwords for ALL users?', false)) {
                $this->info('Operation cancelled.');
                return 0;
            }

            $users = User::all();
        } elseif ($email = $this->option('email')) {
            $users = User::where('email', $email)->get();
            
            if ($users->isEmpty()) {
                $this->error("User with email '{$email}' not found.");
                return 1;
            }
        } else {
            $this->error('Please specify --email=user@example.com or --all');
            return 1;
        }

        $this->info("Found {$users->count()} user(s) to process.");
        $this->newLine();

        $defaultPassword = 'TempPassword123!';
        
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            // Reset to temporary password
            $user->password = $defaultPassword;
            $user->save();
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('✓ Passwords have been reset successfully!');
        $this->newLine();
        $this->warn("Temporary password: {$defaultPassword}");
        $this->warn('Please inform users to:');
        $this->line('1. Login with the temporary password above');
        $this->line('2. Change their password immediately via Profile Settings');
        $this->newLine();

        return 0;
    }
}
