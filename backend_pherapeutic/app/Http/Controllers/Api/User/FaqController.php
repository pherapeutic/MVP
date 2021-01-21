<?php
   
namespace App\Http\Controllers\Api\User;
   
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Faq;
use Validator;
use App\Http\Resources\Faq as FaqResource;
   
class FaqController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faqs = Faq::all();
        //dd($faqs);
        return $this->sendResponse(FaqResource::collection($faqs), 'Faq retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {//dd(11);
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'questions' => 'required',
            'answers' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $faq = Faq::create($input);
   
        return $this->sendResponse(new FaqResource($faq), 'Faq created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //dd(11);
        $faq = Faq::find($id);
  
        if (is_null($faq)) {
            return $this->sendError('Faq not found.');
        }
   
        return $this->sendResponse(new FaqResource($faq), 'Faq retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Faq $faq)
    {
        //dd($faq);
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'questions' => 'required',
            'answers' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $faq->questions = $input['questions'];
        $faq->answers = $input['answers'];
        $faq->save();
   
        return $this->sendResponse(new FaqResource($faq), 'Faq updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();
   
        return $this->sendResponse([], 'Faq deleted successfully.');
    }
}