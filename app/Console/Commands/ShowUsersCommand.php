<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show all users with their roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = \Illuminate\Support\Facades\DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.id', 'users.name', 'users.email', 'roles.id as role_id', 'roles.name as role_name')
            ->get();
            
        $this->info('Usuarios con sus roles:');
        
        foreach ($users as $user) {
            $this->line("{$user->id}. {$user->name} ({$user->email}) - Role ID: {$user->role_id}, Role: {$user->role_name}");
        }
        
        return Command::SUCCESS;
    }
}
