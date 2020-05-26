<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    public function userGroups()
    {
        return $this->belongsToMany('App\User', 'website_user_groups', 'website_id', 'user_id')->withPivot(['group_id'])->as('pivot_website_user_groups');
    }
}
