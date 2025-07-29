<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
