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
            <h4 class="mb-sm-0">Order</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0);">Order</a></li>
                </ol>
            </div>

        </div>
    </div>
</div>



<div class="row">
    <div class="col-xxl-12">
        <div class="card ">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Order</h4>
                <div class="flex-shrink-0">
                    <a class="btn btn-sm btn-primary" href="{{route('order.create')}}">Create New</a>
                    <button class="btn btn-sm btn-secondary" id="export">Export</button>
                </div>
            </div><!-- end card header -->
            <div class="card-body ">
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> My Balance</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-muted fs-14 mb-0" balance-date>

                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="0.00" balance-value>0.00</span></h4>
                                        <a href="#" class="text-decoration-underline"></a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="bx bx-wallet text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                </div>
                <form method="GET" id="filtersForm" class="filtersForm">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="text" class="form-control form-control-sm flatpickr" placeholder="Start Date" name="start_date">
                        </div>
                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="text" class="form-control form-control-sm flatpickr" placeholder="End Date" name="end_date">
                        </div>
                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select form-select-sm" name="type">
                                <option value="" selected>Select</option>
                                <option value="delivery">Delivery</option>
                                <option value="pickup">Pickup</option>
                                <option value="Dining">Dining</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select form-select-sm" name="status">
                                <option value="" selected>Select</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <button class="btn btn-primary btn-sm" type="submit">Filter</button>
                            <button class="btn btn-warning btn-sm" type="reset" onclick="window.location.reload();">Discard</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive" style="min-height: 100vh">
                    <table id="thisTable" class="table table-bordered table-nowrap align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Type</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Table No.</th>
                                <th scope="col">Payment Status</th>
                                <th scope="col">Status</th>
                                <th scope="col">Price</th>
                                <th scope="col">Created By</th>
                                <th scope="col">Created At</th>
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
        ajax: {
            url: '{{ route('order.data') }}',
            data: function (d) {
                    const formData = $('#filtersForm').serializeArray();
                    formData.forEach(field => {
                        d[field.name] = field.value;
                    });
                }
        },
        "order": [[ 0, "desc" ]],
        "columns": [
            { "data": "id" },
            { "data": "type" },
            {
                "data": "customer.first_name",
                "render": function (data, type, row) {
                    if(row.customer) {
                        return `
                            ${row.customer.first_name + ' ' + row.customer.last_name}
                        `
                    }
                    return ``
                }
            },
            {
                "data": "table_number"
            },
            {
                "data": "payment_status"
            },
            {
                "data": "status"
            },
            {
                "data": "total_price"
            },
            {
                "data": "user.first_name",
                "render": function (data, type, row) {
                    if(row.user) {
                        return `
                        ${row.user.first_name + ' ' + row.user.last_name}
                    `
                    }
                    return ``
                }
            },
            {
                "data": "timestamp"
            },
            {
                "data" : "action",
                "render": function (data, type, row) {
                    return `
                        ${row.status === 'pending' ? `
                        <a class="status-btn text-success" href="javascript:;" data-id="${data}" data-status="completed" target="_blank">
                                    <i class="ri-chat-check-line ri-lg"></i>
                                </a>
                                <a class="status-btn text-danger" href="javascript:;" data-id="${data}" data-status="cancelled" target="_blank">
                                    <i class="ri-chat-delete-line ri-lg"></i>
                                </a>
                                <a class="receipt-btn text-dark" href="javascript:;" data-id="${data}" target="_blank">
                                    <i class=" ri-printer-line ri-lg"></i>
                                    </a>
                        <a class="edit-btn" href="javascript:;" data-id="${data}" target="_blank">
                                    <i class="ri-edit-2-line ri-lg"></i>
                                </a>` : ``}
                                @if(Auth::user()->role === 'superadmin')
                                <a class=" text-danger delete-btn" href="javascript:;void" data-id="${data}">
                                    <i class="ri-delete-bin-2-line ri-lg"></i>
                                </a>
                                @endif
                    `;
                }
            }
        ],
    });

        $(document).ready(function () {
            function loadBalance () {
                const start_date = $("#filtersForm input[name=start_date]").val();
                const end_date = $("#filtersForm input[name=end_date]").val();
                $.ajax({
                    type: "GET",
                    url: "{{url('')}}/order/current/balance/"+start_date+"/"+end_date,
                    beforeSend: function () {
                        $("[balance-value]").html(parseFloat("0.00").toFixed(2))
                    }
                }).done(function (response) {
                    $("[balance-value]").html(response)
                })
            }
            $(document).on('click', '#export', function () {
                const start_date = $("#filtersForm input[name=start_date]").val();
                const end_date = $("#filtersForm input[name=end_date]").val();
                const type = $("#filtersForm select[name=type]").val();
                const status = $("#filtersForm select[name=status]").val();
                window.location.href = `{{url("/orders/export")}}?start_date=${start_date}&end_date=${end_date}&type=${type}&status=${status}`
            })
            $(document).on('submit', '#filtersForm', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                table.draw();
                setTimeout(() => { loadBalance (); }, 3000);
            })

            $(document).on('click', '.receipt-btn', function () {
                const id = $(this).data('id');
                window.location.href= '{{url('')}}/order/receipt/'+id
            })
            $(document).on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                window.location.href= '{{url('order')}}/'+id+'/edit'
            });
            $(document).on('click', '.delete-btn', function () {
                const dataId = $(this).attr('data-id')
                const c = confirm("Are you really want to delete this order?")
                if(c) {
                    $.ajax({
                        type: "POST",
                        url: '{{ url('order') }}/' + dataId,
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        }
                    }).done(function (response) {
                        table.ajax.reload(null, false);
                    })
                }
            })

            $(document).on('click', '.status-btn', function () {
                const dataId = $(this).attr('data-id');
                const dataStatus = $(this).attr('data-status');
                const c = confirm("Are you really want to "+dataStatus+" this order?");
                if (c) {
                    window.location.href = '{{ url('') }}/order/'+dataId+'/update/'+dataStatus
                }
            });

            setTimeout(() => { loadBalance (); }, 3000);
        });
</script>
@stop
