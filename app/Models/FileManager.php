<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'mime_type',
        'size',
        'description',
    ];
}
