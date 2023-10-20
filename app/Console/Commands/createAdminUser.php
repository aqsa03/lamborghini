<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\Fortify\CreateNewUser;

class createAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create_admin {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new User with admin role';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $input = [
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => $this->argument('password'),
            'password_confirmation' => $this->argument('password'),
            'is_writer' => 1,
            'is_editor' => 1,
            'is_admin' => 1,
        ];

        try{
            $userCreate = new CreateNewUser();
            $newUser = $userCreate->create($input);
            if (empty($newUser)) {
                $this->error("Error while creating a new user!");
            } else {
                $this->info('User created successfully.');
            }
        } catch(\Exception $e){
            $this->error("Error while creating a new user! ".$e->getMessage());
        }
    }
}
