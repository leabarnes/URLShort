<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkData extends Model
{
    protected $fillable = [
        'link_code', 'link_url', 'title', 'visits'
    ];
}
