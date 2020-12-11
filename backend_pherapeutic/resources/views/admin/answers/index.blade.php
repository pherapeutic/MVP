{{-- Extends layout --}}
@extends('layouts.admin.app')

{{-- Content --}}
@section('content')

    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-6 pb-0">
            <div class="card-title">
                <h3 class="card-label">Answers
                    <!-- <div class="text-muted pt-2 font-size-sm">Datatable initialized from HTML table</div> -->
                </h3>
            </div>
            <div class="card-toolbar">
                <!--begin::Button-->
                <a href="{{route('answers.create')}}" class="btn btn-primary font-weight-bolder">
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
                </span>New Answer</a>
                <!--end::Button-->
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover" id="answers_datatable">
                <thead>
                <tr>
                    <th>Answers ID</th>
                    <th>Title</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>

    </div>

@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="{{ asset('assets/plugins/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endsection

{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}
    <script src="{{ asset('js/sweetalert.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/datatables.bundle.js') }}" type="text/javascript"></script>
    {{-- page scripts --}}
    <script src="{{ asset('assets/plugins/basic/basic.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
    <script>
        var base_url ="{{url('/')}}";
        function getDataTable(){
            $('#answers_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url:  base_url + "/admin/answers",
                    type: 'GET',
                },
                columns: [
                    {data: 'id', name: 'id', orderable: false, searchable: false},
                    { data: 'title', name: 'title' },
                    { data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[1, 'desc']]
            });
        }

        $(document).on('click', '.delete-datatable-record', function(e){
            let url  = base_url + "/admin/answers/" + $(this).attr('data-id');
            let tableId = 'answers_datatable';
            deleteDataTableRecord(url, tableId);
        });

        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            getDataTable();
        });

    </script>
@endsection
