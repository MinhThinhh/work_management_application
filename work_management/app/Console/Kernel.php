<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Dọn dẹp token hết hạn mỗi giờ
        $schedule->command('jwt:prune-expired-tokens')->hourly();

        // Gửi thông báo nhắc nhở công việc sắp đến hạn mỗi ngày lúc 8 giờ sáng
        $schedule->command('tasks:send-reminders --days=1')->dailyAt('08:00');

        // Gửi thông báo nhắc nhở công việc sắp đến hạn trong 3 ngày tới mỗi thứ 2 lúc 9 giờ sáng
        $schedule->command('tasks:send-reminders --days=3')->weeklyOn(1, '09:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
