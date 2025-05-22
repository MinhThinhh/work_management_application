<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PruneExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:prune-expired-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa các token JWT đã hết hạn khỏi bảng blacklist_tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $this->info('Bắt đầu dọn dẹp token hết hạn...');

        // Đếm số lượng token trước khi xóa
        $totalBefore = \DB::table('blacklist_tokens')->count();
        $this->info("Tổng số token trong blacklist: {$totalBefore}");

        // Xóa các token đã hết hạn
        $deleted = \DB::table('blacklist_tokens')
            ->where('expires_at', '<', $now)
            ->delete();

        $this->info("Đã xóa {$deleted} token hết hạn.");

        // Đếm số lượng token còn lại
        $totalAfter = \DB::table('blacklist_tokens')->count();
        $this->info("Còn lại {$totalAfter} token trong blacklist.");

        $this->info('Hoàn thành dọn dẹp token hết hạn.');
    }
}
