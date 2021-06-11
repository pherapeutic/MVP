@extends('layouts.admin')

@section('title') Payment History @endsection

@section('content')

    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-6 pb-0">
            <div class="card-title">
                <h3 class="card-label">Payment History
                    <div class="text-muted pt-2 font-size-sm">
                        <!--  -->
                    </div>
                </h3>
            </div>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-hover" id="payments_datatable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Therapist</th>
                    <th>Amount</th>
                    <th>Transaction Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>

        </div>

    </div>

@endsection

@push('page_script')
    <script>
        var base_url ="{{url('/')}}";
        function getDataTable(){
            $('#payments_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url:  base_url + "/admin/payments",
                    type: 'GET',
                },
                columns: [
                    {data: 'id', name: 'id', orderable: false, searchable: false},
                    { data: 'user_name', name: 'user_name', orderable: true, searchable: true},
                    { data: 'therapist_name', name: 'therapist_name', orderable: true, searchable: true},
                    { data: 'amount', name: 'amount', orderable: false, searchable: false},
                    { data: 'created_at', name: 'created_at', orderable: false, searchable: false},
                    { data: 'status', name: 'status', orderable: true, searchable: true},
                    { data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[1, 'desc']]
            });
        }

        $(document).on('click', '.delete-datatable-record', function(e){
            let url  = base_url + "/admin/payments/" + $(this).attr('data-id');
            let tableId = 'payments_datatable';
            deleteDataTableRecord(url, tableId);
        });

        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            getDataTable();
        });
		
		// paidAmount/{payment_id}/{charge_id}
		 $(document).on('click', '.refundAmount', function(e){
            let url  = base_url + "/admin/refundPayment/" + $(this).attr('data-id')+"/"+$(this).attr('data-transfer');
            let tableId = 'payments_datatable';
			
            RefundTransferDataTableRecord(url, tableId,'refund');
        });

		$(document).on('click', '.paidAmount', function(e){
            let url  = base_url + "/admin/paidAmount/" + $(this).attr('data-id')+"/"+$(this).attr('data-transfer');
            let tableId = 'payments_datatable';
	
            RefundTransferDataTableRecord(url, tableId,'transfer');
        });

    </script>
@endpush
