<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
     protected $fillable = [
        'title',
        'description',
        'images',
        'author_name',
        'author_avatar',
        'author_username',
        'tags'
    ];
}
