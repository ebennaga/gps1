<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagePtp extends Model
{
    protected $fillable
        = [
            'ship_id',
            'history_ship_id',
            'last_sent_destination',
            'subject',
            'filename_chr',
            'content'
        ];
}