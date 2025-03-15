<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jobdesk extends Model
{
    protected $fillable = ['title', 'description', 'role_id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}