<?php
   
namespace App\Http\Controllers\Api\User;
   
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\AboutUs;
use Validator;
use App\Http\Resources\AboutUs as AboutUsResource;
   
class AboutUsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAboutUs()
    {
        $aboutus = AboutUs::all();
        //dd($faqs);
        return $this->sendResponse(AboutUsResource::collection($aboutus), 'About Us retrieved successfully.');
    }
    
}