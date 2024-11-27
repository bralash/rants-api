<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = ['name','role','bio','profile_image'];

    public function socialMediaLinks() {
        return $this->hasMany(SocialMediaLink::class);
    }
}
