<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'tb_message';

    protected $primaryKey = 'message_id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'date_sent' => 'datetime',
        ];
    }
}
