<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Laravel\Passport\Token;

class PruneOldTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbseller:prune-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove todos os tokens revogados/expirados';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        $query = Token::where('revoked', true)->orWhere('expires_at', '<=', Carbon::now()->subDay());
        if ($query->count() <= 0) {
            $this->info('Sem access_tokens para remover.');
            return 0;
        }

        $this->warn("Removendo {$query->count()} access_tokens");
        $query->delete();
        $this->info('access_tokens removidos.');

        return 0;
    }
}
