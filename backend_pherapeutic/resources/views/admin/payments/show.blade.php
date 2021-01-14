@extends('layouts.admin')

@section('title') Payment Details @endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4> Payment Details</h4>
            </div>
            <div class="card-body">

                <div class="row">

                    <div class="col-6">
                        <div class="p-2">
                            <label><b>Call Logs</b> </label>
                        </div>
                        <div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>Call Date: </th>
                                        <td class="p-2">{{$callLogObj->created_at ? \Carbon\Carbon::parse($callLogObj->created_at)->format('d M Y') : 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th>End Date: </th>
                                        <td class="p-2">{{$callLogObj->ended_at ? \Carbon\Carbon::parse($callLogObj->ended_at)->format('d M Y') : 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th>Status: </th>
                                        <td class="p-2">
                                            @if($callLogObj->call_status == '0')
                                                <span class="badge badge-secondary">Connecting</span>
                                            @elseif($callLogObj->call_status == '1')
                                                <span class="badge badge-info">In-progress</span>
                                            @elseif($callLogObj->call_status == '2')
                                                <span class="badge badge-success">Call Ended</span>
                                            @elseif($callLogObj->call_status == '3')
                                                <span class="badge badge-danger">Decline</span>
                                            @else
                                                N/A                        
                                            @endif                                           
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2">
                            <label><b>Payment Details</b> </label>
                        </div>
                        <div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>Charge Id: </th>
                                        <td class="p-2">{{$paymentObj->charge_id ? $paymentObj->charge_id : 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th>Transaction Id: </th>
                                        <td class="p-2">{{$paymentObj->txn_id ? $paymentObj->txn_id : 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th>Card Id: </th>
                                        <td class="p-2">
                                            {{$paymentObj->card_id ? ($paymentObj->card_id) : 'N/A'}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Amount: </th>
                                        <td class="p-2">
                                            {{$paymentObj->amount ? ('£ '.$paymentObj->amount) : 'N/A'}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2">
                            <label><b>Payment Transfer Details</b> </label>
                        </div>
                        <div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>Transfer Id: </th>
                                        <td class="p-2">
                                            {{$paymentObj->transfer_id ? $paymentObj->transfer_id : 'N/A'}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Transfer To Account: </th>
                                        <td class="p-2">
                                            {{$paymentObj->transfer_to_account ? $paymentObj->transfer_to_account : 'N/A'}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Transfer Amount: </th>
                                        <td class="p-2">
                                            {{$paymentObj->transfer_amount ? ('£ '.$paymentObj->transfer_amount) : 'N/A'}}
                                        </td>
                                    </tr>
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