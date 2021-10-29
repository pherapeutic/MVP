<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'call_logs_id',
        'charge_id',
        'txn_id',
        'amount',
        'is_captured',
        'card_id',
    ];

    public function getAllPayments(){
        return self::all();
    }

    public function getPaymentById($id){
        return self::where('id', $id)->first();
    }
    
    public function callLogs()
    {
        return $this->belongsTo('App\Models\CallLogs', 'call_logs_id', 'id');
    }
    
}
