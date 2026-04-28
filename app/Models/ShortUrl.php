<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    protected $fillable = ['code', 'target_url', 'session_id', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime'];
}