<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TermsandConditions extends Model
{

    protected $table = 'termsandconditions';
    protected $fillable = [
        'title', 'description'
    ];
}
