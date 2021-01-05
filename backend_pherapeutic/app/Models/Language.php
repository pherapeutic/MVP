<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title'
    ];

    public function saveNewLanguage($inputArr){
        return self::create($inputArr);
    }

    /**
     * Created By Parmod Kumar
     * Created At 02-11-2020
     * @param NULL
     * @return Array of all Languages
     */
    public function getAllLanguages(){
        return self::orderBy('title', 'ASC')->get();
    }

    /**
     * Created By Parmod Kumar
     * Created At 22-10-2020
     * @param Languages id
     * @return Languages object
     */
    public function getLanguageById($id){
        return self::where('id', $id)->first();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id , fields array
     * @return updated
     */
    public function updateLanguage($id, $inputArr){
        return self::where('id', $id)->update($inputArr);
    }

    public static function getLanguagesDropdownArr(){
        return self::orderBy('title', 'asc')->pluck('title', 'id')->toArray();
    }

    public function getResponseArr(){
        $returnArr = [
            'id' => $this->id,
            'title' => $this->title
        ];
        return $returnArr;
    }
}
