<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable=[
        'description',
        'completed',
        'user_id',
    ];
    protected $hidden = [
        'id',
    ];
}
