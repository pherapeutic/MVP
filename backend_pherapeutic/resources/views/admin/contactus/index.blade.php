@extends('layouts.admin')

@section('title') Contact Us @endsection

@section('content')

    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-6 pb-0">
            <div class="card-title">
                <h3 class="card-label">Contact Us
                    <div class="text-muted pt-2 font-size-sm">
                        <!--  -->
                    </div>
                </h3>
            </div>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-hover" id="contactus_datatable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
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
            $('#contactus_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url:  base_url + "/admin/contactus",
                    type: 'GET',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'name', name: 'name', orderable: true, searchable: true},
                    { data: 'email', name: 'email', orderable: true, searchable: true},
                    { data: 'subject', name: 'subject', orderable: true, searchable: true},
                    { data: 'message', name: 'message', orderable: true, searchable: true},
                    { data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[1, 'desc']]
            });
        }

        $(document).on('click', '.delete-datatable-record', function(e){
            let url  = base_url + "/admin/contactus/" + $(this).attr('data-id');
            let tableId = 'contactus_datatable';
            deleteDataTableRecord(url, tableId);
        });

        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            getDataTable();
        });

    </script>
@endpush
