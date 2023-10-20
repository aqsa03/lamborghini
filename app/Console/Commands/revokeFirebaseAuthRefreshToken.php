<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class revokeFirebaseAuthRefreshToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member:revokeAuthToken {uid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke Firebase Auth refresh Token for a specific Firebase Auth User';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $auth = app('firebase.auth');

        try {
            $auth->revokeRefreshTokens($this->argument('uid'));
            $this->info('Refresh token revoked for user '.$this->argument('uid'));
        } catch(\Exception $e){
            $this->error("Error revoking Firebase refresh token; ".$e->getMessage());
        }
    }
}
