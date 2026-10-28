<?php

namespace App\Console\Commands;

use App\Models\TagihanSpp;
use Illuminate\Console\Command;

class UpdateOverdueTagihan extends Command
{
    protected $signature   = 'tagihan:update-overdue';
    protected $description = 'Tandai tagihan UNPAID yang sudah lewat jatuh tempo menjadi OVERDUE (BR-07, FR-10).';

    public function handle(): int
    {
        $today = now()->toDateString();

        $updated = TagihanSpp::where('status', 'UNPAID')
            ->where('tanggal_jatuh_tempo', '<', $today)
            ->update(['status' => 'OVERDUE']);

        $this->info("$updated tagihan telah ditandai OVERDUE.");
        return Command::SUCCESS;
    }
}
