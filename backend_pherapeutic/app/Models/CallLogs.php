<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallLogs extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'caller_id',
        'user_id',
        'therapist_id',
        'call_status',
        'payment_status',
        'duration',
        'ended_at',
    ];

    /**
     * Created By Ak Tiwari
     * Created At 28-12-2020
     * @param user caller id
     * @return user object
     */
    public function getCallLogByCallerId($id){
        return self::where('caller_id', $id)->first();
    }

    public function getAllTherapistCallLog($id){
        return self::where('therapist_id', $id)->get();
    }

    public function getAllClientCallLog($id){
        return self::where('user_id', $id)->get();
    }

    public function generateCallerId(){
        $callerId = mt_rand(100000,999999);
        $count = self::where('caller_id', $callerId)->count();
        if($count > 0){
            $this->generateCallerId();
        }
        return $callerId;
    }

    public function getNotEndedCall($userId){
        return self::where('user_id', $userId)->where('ended_at', null)->first();
    }
    
    public function therapist()
    {
        return $this->belongsTo('App\Models\User', 'therapist_id', 'id');
    }
          
    public function ratings()
    {
        return $this->hasOne('App\Models\Rating');
    }
        
}
