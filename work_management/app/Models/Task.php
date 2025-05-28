<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'start_date', 'due_date', 'status', 'priority', 'creator_id'];

    /**
     * Get the user that created the task.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Kiểm tra và điều chỉnh ngày hết hạn nếu trước ngày bắt đầu
     * Nếu due_date < start_date thì gán due_date = start_date và trả về thông báo
     */
    public static function validateAndAdjustDates(&$data)
    {
        $message = null;

        if (isset($data['start_date']) && isset($data['due_date'])) {
            $startDate = Carbon::parse($data['start_date']);
            $dueDate = Carbon::parse($data['due_date']);

            // Nếu ngày hết hạn trước ngày bắt đầu, gán ngày hết hạn = ngày bắt đầu
            if ($dueDate->lt($startDate)) {
                $data['due_date'] = $data['start_date'];
                $message = 'Ngày hết hạn không được trước ngày bắt đầu! Đã tự động điều chỉnh ngày hết hạn thành ngày bắt đầu.';
            }
        }

        return $message;
    }
}
