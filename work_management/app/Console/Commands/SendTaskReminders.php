<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-reminders {--days=1 : Number of days before due date to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi thông báo nhắc nhở cho các công việc sắp đến hạn';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $this->info("Đang tìm kiếm các công việc sắp đến hạn trong {$days} ngày tới...");

        // Tính ngày cần kiểm tra
        $targetDate = now()->addDays($days)->format('Y-m-d');

        // Lấy danh sách công việc sắp đến hạn và chưa hoàn thành
        $tasks = \App\Models\Task::where('due_date', $targetDate)
            ->where('status', '!=', 'completed')
            ->with('creator')
            ->get();

        $this->info("Tìm thấy {$tasks->count()} công việc sắp đến hạn.");

        $notificationCount = 0;

        foreach ($tasks as $task) {
            // Kiểm tra xem đã gửi thông báo cho công việc này chưa
            $existingNotification = \App\Models\Notification::where('task_id', $task->id)
                ->where('type', 'due_date_reminder')
                ->where('created_at', '>=', now()->subDays(1))
                ->exists();

            if ($existingNotification) {
                $this->info("Đã gửi thông báo cho công việc #{$task->id} trong 24 giờ qua. Bỏ qua.");
                continue;
            }

            // Tạo thông báo
            $notification = new \App\Models\Notification([
                'user_id' => $task->creator_id,
                'task_id' => $task->id,
                'type' => 'due_date_reminder',
                'title' => 'Nhắc nhở: Công việc sắp đến hạn',
                'message' => "Công việc '{$task->title}' sẽ đến hạn vào ngày {$task->due_date}.",
                'sent_at' => now()
            ]);

            $notification->save();
            $notificationCount++;

            // Gửi email nếu có địa chỉ email
            if ($task->creator && $task->creator->email) {
                try {
                    \Mail::raw("Công việc '{$task->title}' sẽ đến hạn vào ngày {$task->due_date}. Vui lòng hoàn thành công việc đúng hạn.", function ($message) use ($task) {
                        $message->to($task->creator->email)
                            ->subject('Nhắc nhở: Công việc sắp đến hạn');
                    });

                    $this->info("Đã gửi email nhắc nhở cho {$task->creator->email} về công việc #{$task->id}.");
                } catch (\Exception $e) {
                    $this->error("Lỗi khi gửi email cho {$task->creator->email}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Đã tạo {$notificationCount} thông báo nhắc nhở.");
    }
}
