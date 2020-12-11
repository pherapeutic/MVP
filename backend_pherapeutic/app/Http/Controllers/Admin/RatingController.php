<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ratings;
class RatingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, Ratings $ratings)
    {
        if ($request->ajax()) {
            $ratingsColl = $ratings->getAllFaqs();
            return datatables()->of($ratingsColl)
                ->addIndexColumn()
                ->addColumn('id', function ($ratings) {
                   return $ratings->id;
                })
                ->addColumn('rating', function ($ratings) {
                    return ($ratings->rating) ? ($ratings->rating) : 'N/A';
                })
                ->addColumn('comment', function ($ratings) {
                    return ($ratings->comment) ? ($ratings->comment) : 'N/A';
                })
                ->addColumn('action', function ($ratings) {
                    $btn = '';
                    $btn = '<a href="ratings/'.$ratings->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$ratings->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.ratings.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.ratings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Ratings $ratings)
    {
        //echo"<pre>";print_r($request->all());die;
        $inputArr = $request->except(['_token']);
        $ratingsObj = $ratings->saveNewRating($inputArr);
        if(!$ratingsObj){
            return redirect()->back()->with('error_message', 'Unable to create Faq. Please try again later.');
        }

        return redirect()->route('ratings.index')->with('success_message', 'Ratings account created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Ratings $ratings)
    {

        $ratings = $ratings->getRatingById($id);
        if(!$ratings){
            return redirect()->back()->with('error_message', 'Ratings does not exist');
        }

        return view('admin.ratings.edit', compact('ratings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //echo"<pre>";print_r($request->all());die;
        $ratings = new Ratings();
        $ratings = $ratings->getRatingById($id);
        if(!$ratings){
            return redirect()->back()->with('error_message', 'This rating does not exist');
        }

        $inputArr = $request->except(['_token', 'rating_id', '_method']);
        $hasUpdated = $ratings->updateRating($id, $inputArr);

        if($hasUpdated){
            return redirect()->route('faqs.index')->with('success_message', 'faq updated successfully.');
        }
        return redirect()->back()->with('error_message', 'Unable to update faqs. Please try again later.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Faqs $faqs)
    {
        $faqsObj = $faqs->getFaqById($id);

        if(!$faqsObj){
            return returnNotFoundResponse('This faq does not exist');
        }

        $hasDeleted = $faqsObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Faqs deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
