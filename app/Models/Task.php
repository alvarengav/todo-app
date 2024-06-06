<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_important',
        'is_completed',
        'due_date',
        'user_id',
        'group_id',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(TaskGroup::class, 'group_id');
    }
}
