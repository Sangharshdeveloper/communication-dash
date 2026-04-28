<?php

namespace App\Console\Commands;

use App\Models\MagicLoginToken;
use Illuminate\Console\Command;

class PurgeExpiredTokens extends Command
{
    protected $signature   = 'tokens:purge-expired {--dry-run : Show count without deleting}';
    protected $description = 'Purge expired and used magic login tokens from the database';

    public function handle(): int
    {
        $query = MagicLoginToken::where(function ($q) {
            $q->where('expires_at', '<', now()->subDay())  // Expired > 24h ago
              ->orWhere(function ($q2) {
                  $q2->where('is_used', true)
                     ->where('used_at', '<', now()->subDay()); // Used > 24h ago
              });
        });

        $count = $query->count();

        if ($this->option('dry-run')) {
            $this->info("Would delete {$count} expired/used tokens (dry run).");
            return self::SUCCESS;
        }

        $query->delete();

        $this->info("Purged {$count} expired/used magic login tokens.");

        return self::SUCCESS;
    }
}
