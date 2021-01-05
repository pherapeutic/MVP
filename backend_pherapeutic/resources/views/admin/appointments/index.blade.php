@extends('layouts.admin')

@section('title') Appointments list @endsection

@section('content')

    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-6 pb-0">
            <div class="card-title">
                <h3 class="card-label">Appointments
                    <div class="text-muted pt-2 font-size-sm">
                        <!--  -->
                    </div>
                </h3>
            </div>
            {{--<div class="card-toolbar">
                <!--begin::Button-->
                <a href="{{route('admin.appointments.create')}}" class="btn btn-primary font-weight-bolder">
                <span class="svg-icon svg-icon-md">
                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"/>
                            <circle fill="#000000" cx="9" cy="15" r="6"/>
                            <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3"/>
                        </g>
                    </svg>
                    <!--end::Svg Icon-->
                </span>New Record</a>
                <!--end::Button-->
            </div>--}}
        </div>

        <div class="card-body">

            <table class="table table-bordered table-hover" id="appointments_datatable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Therapist</th>
                    <th>Status</th>
                    <th>Is Trail</th>
                    <th>Ended At</th>
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
            $('#appointments_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url:  base_url + "/admin/appointments",
                    type: 'GET',
                },
                columns: [
                    {data: 'id', name: 'id', orderable: false, searchable: false},
                    { data: 'user_name', name: 'user_name', orderable: false, searchable: false},
                    { data: 'therapist_name', name: 'therapist_name', orderable: false, searchable: false},
                    { data: 'status', name: 'status'},
                    { data: 'is_trail', name: 'is_trail'},
                    { data: 'ended_at', name: 'ended_at'},
                    { data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[1, 'desc']]
            });
        }

        $(document).on('click', '.delete-datatable-record', function(e){
            let url  = base_url + "/admin/appointments/" + $(this).attr('data-id');
            let tableId = 'appointments_datatable';
            deleteDataTableRecord(url, tableId);
        });

        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            getDataTable();
        });

    </script>
@endpush
