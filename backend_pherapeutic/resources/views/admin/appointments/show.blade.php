@extends('layouts.admin')

@section('title') Appointment Details @endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4> Appointment Details</h4>
            </div>
            <div class="card-body">

                <div class="row">

                    <div class="col-6">
                        <div class="p-2">
                            <label><b>Appointment Details</b> </label>
                        </div>
                        <div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>Appointment Date: </th>
                                        <td class="p-2">{{$appointmentObj->created_at ? \Carbon\Carbon::parse($appointmentObj->created_at)->format('d M Y') : 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th>End Date: </th>
                                        <td class="p-2">{{$appointmentObj->ended_at ? \Carbon\Carbon::parse($appointmentObj->ended_at)->format('d M Y') : 'N/A'}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2">
                            <label><b>Review & Rating</b> </label>
                        </div>
                        <div>
                            <table class="table">
                                <tbody>
                                    @forelse($appointmentObj->rating as $rating)
                                    <tr>
                                        <th>Rating: </th>
                                        <td class="p-2">{{$rating->rating ? $rating->rating : 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th>Review: </th>
                                        <td class="p-2">{{$rating->comment ? $rating->comment : 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th>Review Date: </th>
                                        <td class="p-2">
                                            {{$rating->created_at ? \Carbon\Carbon::parse($rating->created_at)->format('d M Y') : 'N/A'}}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td class="p-2">
                                            No data found
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2">
                            <label><b>Feedback Note</b> </label>
                        </div>
                        <div>
                            <table class="table">
                                <tbody>
                                    @forelse($appointmentObj->feedbackNotes as $note)
                                    <tr>
                                        <th>Note: </th>
                                        <td class="p-2">
                                            {{$note->feedback_note ? $note->feedback_note : 'N/A'}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Feedback By: </th>
                                        <td class="p-2">
                                            {{$note->feedback_by ? $note->feedbackBy->FullName : 'N/A'}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Feedback Date: </th>
                                        <td class="p-2">
                                            {{$note->created_at ? \Carbon\Carbon::parse($note->created_at)->format('d M Y') : 'N/A'}}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td class="p-2">
                                            No data found
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                      <div class="col-6">
                        <div class="p-2">
                            <label><b>Questions and Answers</b> </label>
                        </div>
                        <div>
                            <table class="table">
                                <tbody>
                                @if(count($questions))
                                    @foreach($questions as $key=>$question)
                                    <tr>
                                        <th>Question: </th>
                                        <td class="p-2">
                                            {{$question->getQuestion ? $question->getQuestion->title : 'N/A'}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Answer: </th>
                                        <td class="p-2">
                                            {{$question->getAnswer ? $question->getAnswer->title : 'N/A'}}
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                 <tr>
                                        <td class="p-2">
                                            No data found
                                        </td>
                                </tr>
                                @endif

                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 text-center mt-5">
                        <a class="btn btn-dark" href="{{URL::previous()}}" style="color:white;">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page_script')
    {{--<script type="text/javascript">
        //Change status function
        $(document) .on('change', ".change_status", function(){

            swal({
                title: "Do you want to change shipment status?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                buttons: {
                    cancel : 'No, cancel it!',
                    confirm : 'Yes, I am sure!'
                },
            })

            .then((willDo) => {
                if (willDo) {
                    var shipmentId = $(this).attr('data-id');
                    var status_id = $(this).val();
                    $.ajax({
                        type: "POST",
                        url: site_url +"/shipment/change-status/"+shipmentId,
                        data: "status_id="+status_id,
                        success: function( data ) {
                            if(data.statusCode >= 200){
                                toastr.success(data.message);
                            }
                            if(data.statusCode >= 422){
                                toastr.error(data.message);
                            }
                        },
                        error: function (data) {
                            var prevStatusId = $(this).attr('data-sid');
                            $(this).val(prevStatusId);
                            return false;
                            toastr.error('Error:', data);
                        }
                    });
                }else{
                    var prevStatusId = $(this).attr('data-sid');
                    $(this).val(prevStatusId);
                    return false;
                }
            });
        });
    </script>--}}
@endpush
@endsection