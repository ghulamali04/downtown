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
            <h4 class="mb-sm-0">Inventory Record</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0);">Inventory Record</a></li>
                </ol>
            </div>

        </div>
    </div>
</div>



<div class="row">
    <div class="col-xxl-12">
        <div class="card ">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Inventory Record</h4>
                <div class="flex-shrink-0">
                    <a class="btn btn-primary" href="{{route('tracking.create')}}">Create New</a>
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
                                            {{date("Y-m-d")}}
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
                    <input type="hidden" name="item_id" value="{{@$_GET['item_id']}}">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Date</label>
                            <input type="text" class="form-control form-control-sm flatpickr" value="{{ date("Y-m-d") }}" placeholder="Date" name="date">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <button class="btn btn-primary" type="submit">Filter</button>
                            <button class="btn btn-warning" type="reset" onclick="window.location.reload();">Discard</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive" style="min-height: 100vh">
                    <table id="thisTable" class="table table-bordered table-nowrap align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Item</th>
                                <th scope="col">Category</th>
                                <th scope="col">Type</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Description</th>
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
            url: '{{ route('tracking.data') }}',
            data: function (d) {
                const formData = $('#filtersForm').serializeArray();
                formData.forEach(field => {
                    d[field.name] = field.value;
                });
            }
        },
        "order": [[ 0, "desc" ]],
        "columns": [
            { "data": "date" },
            { "data": "inventory_item.name" },
            { "data": "category" },
            { "data": "type" },
            {
                "data": "amount",
                "render": function (data, type, row) {
                    return `${data} ${row.inventory_item.unit}`
                }
            },
            { "data": "description" },
            {
                "data" : "action",
                "render": function (data, type, row) {
                    return `
                       {{-- <a class="edit-btn" href="javascript:;" data-id="${data}">
                                    <i class="ri-edit-2-line ri-2xl"></i>
                                </a> --}}
                                ${row.type != 'purchase' ? `<a class=" text-danger delete-btn" href="javascript:;void" data-id="${data}">
                                    <i class="ri-delete-bin-2-line ri-2xl"></i>
                                </a>` : ''}
                    `;
                }
            }
        ],
    });

        $(document).ready(function () {
            function loadBalance(date) {
                $.ajax({
                    type: "GET",
                    url: "{{url('/inventory/tracking/current/balance')}}/"+date+"?item_id={{Request::get('item_id')}}",
                    beforeSend: function () {
                        $("[balance-date]").html(date)
                        $("[balance-value]").html(parseFloat("0.00").toFixed(2))
                    }
                }).done(function (response) {
                    $("[balance-value]").html(response)
                })
            }
            $(document).on('submit', '#filtersForm', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                loadBalance($("#filtersForm input[name=date]").val());
                table.draw()
            })

            $(document).on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                window.location.href= '{{url('inventory/tracking')}}/'+id+'/edit'
            });
            $(document).on('click', '.delete-btn', function () {
                const dataId = $(this).attr('data-id')
                const c = confirm("Are you really want to delete this customer?")
                if(c) {
                    $.ajax({
                        type: "POST",
                        url: '{{ url('inventory/tracking') }}/' + dataId,
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        }
                    }).done(function (response) {
                        table.ajax.reload(null, false);
                        loadBalance($("#filtersForm input[name=date]").val());
                    })
                }
            })

            setTimeout(() => {
                loadBalance('{{date("Y-m-d")}}');
            }, 3000)
        });
</script>
@stop
