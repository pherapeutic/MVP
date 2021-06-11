@extends('layouts.admin')

@section('title') Therapist list @endsection

@section('content')

    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-6 pb-0">
            <div class="card-title">
                <h3 class="card-label">Therapists
                    <!-- <div class="text-muted pt-2 font-size-sm">Datatable initialized from HTML table</div> -->
                </h3>
            </div>
            <div class="card-toolbar">
                <!--begin::Button-->
                <a href="{{route('admin.therapist.create')}}" class="btn btn-primary font-weight-bolder">
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
                </span>Add New Therapist</a>
                <!--end::Button-->
            </div>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-hover" id="therapist_datatable">
                <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Languages</th>
                    <th>Specialism</th>
                    <th>Experience</th>
                    <th>Bono Work</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
               <!--  <tr>
                    <td>1</td>
                    <td>61715-075</td>
                    <td>China</td>
                    <td>Tieba</td>
                    <td nowrap></td>
                </tr> -->
                </tbody>
            </table>

        </div>

    </div>

@endsection

@push('page_script')
    <script>
        var base_url ="{{url('/')}}";
        function getTherapists(){
            $('#therapist_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url:  base_url + "/admin/user/therapist",
                    type: 'GET',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'languages', name: 'languages' },
                    { data: 'specialism', name: 'specialism' },
                    { data: 'experience', name: 'experience' },
                    { data: 'bono_work', name: 'bono_work' },
                    { data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        }

        $(document).on('click', '.delete-datatable-record', function(e){
            let url  = base_url + "/admin/user/therapist/" + $(this).attr('data-id');
            let tableId = 'therapist_datatable';
            deleteDataTableRecord(url, tableId);
        });

        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            getTherapists();
        });
    </script>
@endpush
