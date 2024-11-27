<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialMediaLink extends Model
{
    use HasFactory;

    protected $fillable = ['platform','url','team_member_id'];

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class);
    }
}
