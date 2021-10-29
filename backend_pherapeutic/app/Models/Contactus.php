<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contactus extends Model
{
    use HasFactory;
    protected $table = 'contactus';
    protected $fillable = [
        'name', 'email', 'subject', 'message'
    ];

    public function getAllContact(){
        return self::all();
    }

    public function getContactById($id){
        return self::where('id', $id)->first();
    }
}
