<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    protected $table = 'trains';

    protected $fillable = [
        'train_name',
        'price',
        'route',
        'image',
    ];
}
