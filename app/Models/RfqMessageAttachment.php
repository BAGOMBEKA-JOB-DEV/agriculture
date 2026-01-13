<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqMessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_message_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    public function message()
    {
        return $this->belongsTo(RfqMessage::class, 'rfq_message_id');
    }
}
