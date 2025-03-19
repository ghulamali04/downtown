@extends('base')

@section('css')
<link href="{{asset('/')}}assets/libs/datatables-responsive/css/responsive.bootstrap4.css" rel="stylesheet">
<link href="{{asset('/')}}assets/libs/datatables-buttons/css/buttons.bootstrap4.css" rel="stylesheet">
<link href="{{asset('/')}}assets/libs/datatables-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link href="{{asset('/')}}assets/libs/datatables-scroller/css/scroller.bootstrap4.css">
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-transparent">
            <h4 class="mb-sm-0">Customer</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0);">Customer</a></li>
                </ol>
            </div>

        </div>
    </div>
</div>



<div class="row">
    <div class="col-xxl-12">
        <div class="card ">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Customer</h4>
                <div class="flex-shrink-0">
                    <a class="btn btn-sm btn-primary" href="{{route('customer.create')}}">Create New</a>
                </div>
            </div><!-- end card header -->
            <div class="card-body ">
                <div class="table-responsive" style="min-height: 100vh">
                    <table id="thisTable" class="table table-bordered table-nowrap align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone Number</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->

</div>
@stop

@section('scripts')
<script src="{{asset('/')}}assets/libs/datatables/jquery.dataTables.min.js"></script>
<script src="{{asset('/')}}assets/libs/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script src="{{asset('/')}}assets/libs/datatables-responsive/js/dataTables.responsive.js"></script>
<script src="{{asset('/')}}assets/libs/datatables-responsive/js/responsive.bootstrap4.js"></script>
<script src="{{asset('/')}}assets/libs/datatables-buttons/js/dataTables.buttons.js"></script>
<script src="{{asset('/')}}assets/libs/datatables-buttons/js/buttons.bootstrap4.js"></script>
<script src="{{asset('/')}}assets/libs/datatables-buttons/js/buttons.html5.js"></script>
<script src="{{asset('/')}}assets/libs/datatables-buttons/js/buttons.print.js"></script>
<script src="{{asset('/')}}assets/libs/datatables-buttons/js/buttons.colVis.js"></script>
<script src="{{asset('/')}}assets/libs/datatables-buttons/js/buttons.colVis.js"></script>
<script src="{{asset('/')}}assets/libs/datatables-scroller/js/dataTables.scroller.js"></script>
<script src="{{asset('/')}}assets/libs/datatables-scroller/js/scroller.bootstrap4.js"></script>

<script type="module">
    const table = $("#thisTable").DataTable({
        "paging": true,
        "ordering": true,
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        processing: true,
        serverSide: true,
        scrollY: 300,
        deferRender: true,
        scroller: true,
        "initComplete": function(settings, json) {
            $('[data-toggle="tooltip"]').tooltip({
                container: 'body',
                'html': true
            });
        },
        "drawCallback": function(settings) {
            $('[data-toggle="tooltip"]').tooltip({
                container: 'body',
                'html': true
            });
        },
        ajax: '{{ route('customer.data') }}',
        "order": [[ 0, "desc" ]],
        "columns": [
            { "data": "first_name" },
            { "data": "last_name" },
            { "data": "email" },
            { "data": "phone_number" },
            {
                "data" : "action",
                "render": function (data, type, row) {
                    return `
                        <a class="edit-btn" href="javascript:;" data-id="${data}" target="_blank">
                                    <i class="ri-edit-2-line ri-lg"></i>
                                </a>
                                <a class=" text-danger delete-btn" href="javascript:;void" data-id="${data}">
                                    <i class="ri-delete-bin-2-line ri-lg"></i>
                                </a>
                    `;
                }
            }
        ],
    });

        $(document).ready(function () {
            $(document).on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                window.location.href= '{{url('customer')}}/'+id+'/edit'
            });
            $(document).on('click', '.delete-btn', function () {
                const dataId = $(this).attr('data-id')
                const c = confirm("Are you really want to delete this customer?")
                if(c) {
                    $.ajax({
                        type: "POST",
                        url: '{{ url('customer') }}/' + dataId,
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        }
                    }).done(function (response) {
                        table.ajax.reload(null, false);
                    })
                }
            })
        });
</script>
@stop
