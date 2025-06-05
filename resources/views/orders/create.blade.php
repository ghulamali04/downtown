@extends('base')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-transparent">
            <h4 class="mb-sm-0">Create Order</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('order.index')}}">Order</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Create</h4>
            </div><!-- end card header -->
            <div class="card-body pt- 0">
                <form method="POST" action="{{ route('order.store') }}">@csrf
                    <div class="row">
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-control select2 @error('type') is-invalid @enderror" name="type">
                                <option value="delivery" {{old('type') == 'delivery' || !old('type') ? 'selected' : ''}}>Delivery</option>
                                <option value="pickup" {{old('type') == 'pickup' ? 'selected' : ''}}>Pickup</option>
                                <option value="dining" {{old('type') == 'dining' ? 'selected' : ''}}>Dining</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Customer <span id="add-new-customer" style="cursor: pointer"><i class="ri-add-circle-line ri-xl"></i></span></label>
                            <select class="form-control select2 @error('customer') is-invalid @enderror" name="customer" id="customer-select">
                                @if(@old('customer'))
                                <option value="{{old('customer')}}">{{@explode('___', old('customer'))[1]}}</option>
                                @endif
                            </select>
                            @error('customer')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Table Number</label>
                            <input type="text" class="form-control @error('table_number') is-invalid @enderror" name="table_number" value="{{old('table_number')}}" placeholder="Table Number">
                            @error('table_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 col-sm-12 col-12 mb-3">
                            <label class="form-label">Instructions</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" name="instructions">{{old('instructions')}}</textarea>
                            @error('instructions')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row items-repeater">
                            <div class="col-md-12" data-repeater-list="menuItems">
                                <div class="row" data-repeater-item>
                                    <div class="col-sm-3 mb-3">
                                        <select class="form-select select2 menu-item-category" name="category" data-name="category" data-id="0">
                                            <option value="" selected>Select Category</option>
                                            @foreach ($menuCateogryItems as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <select class="form-select select2 menu-item-variant" name="variant" data-name="variant" data-id="0">
                                            <option value="" selected>Select Variant</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <div class="input-step">
                                            <input type="hidden" class="cur-qty" name="qty" value="1">
                                            <button type="button" class="minus">–</button>
                                            <input type="number" class="qty" value="1" min="1" max="100" readonly>
                                            <button type="button" class="plus">+</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <button type="button" class="btn btn-sm btn-danger mt-2" data-repeater-delete><i class="ri-delete-bin-2-line"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <button type="button" class="btn btn-sm btn-primary" data-repeater-create>Add Order Item</button>
                                </div>
                            </div>
                            @if ($errors->has('menuItems.*.item'))
                                @if (count($errors->get('menuItems.*.item')) > 0)
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="invalid-feedback d-block">Please enter menu items for the order.</div>
                                        </div>
                                    </div>
                                @endif
                            @endif

                        </div>

                    </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                    <button class="btn btn-warning" type="reset" onclick="window.location.reload();">Discard</button>
                </div>
            </div>
            </form>
        </div><!-- end card body -->
    </div><!-- end card -->
</div><!-- end col -->

</div>


<form method="POST" id="FormAddCustomer">@csrf
    <div class="modal" id="modalAddCustomer" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Add New Customer</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" placeholder="First Name">
                    <div class="invalid-feedback d-block" error-name="first_name"></div>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" placeholder="Last Name">
                    <div class="invalid-feedback d-block" error-name="last_name"></div>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="1" placeholder="Address"></textarea>
                    <div class="invalid-feedback d-block" error-name="address"></div>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" name="email" placeholder="Email">
                    <div class="invalid-feedback d-block" error-name="email"></div>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="phone_number" placeholder="Phone Number">
                    <div class="invalid-feedback d-block" error-name="phone_number"></div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </div>
        </div>
      </div>
  </form>
@stop

@section('plugin_scripts')
<script src="{{asset('/')}}assets/js/pages/form-input-spin.init.js"></script>
<script src="{{asset('/')}}assets/libs/repeater/jquery.repeater.js"></script>
<script>
    function uniqueId() {
        return 'id' + Math.random().toString(36).substr(2, 9);
    }
    $(document).ready(function () {
        $(".items-repeater").repeater({
            initEmpty: false,
            show: function () {
                const $row = $(this);
                $row.slideDown();
                const index = uniqueId()

                $row.find("select.menu-item-category").attr('data-id', index)
                $row.find("select.menu-item-category").select2()

                $row.find("select.menu-item").attr('data-id', index)
                $row.find("select.menu-item").select2()

                $row.find("select.menu-item-variant").attr('data-id', index)
                $row.find("select.menu-item-variant").select2()
                function isData() { var t = document.getElementsByClassName("plus"), e = document.getElementsByClassName("minus"), n = document.getElementsByClassName("product"); t && Array.from(t).forEach(function (t) { t.addEventListener("click", function (e) { parseInt(t.previousElementSibling.value) < e.target.previousElementSibling.getAttribute("max") && (e.target.previousElementSibling.value++, n) && Array.from(n).forEach(function (t) { updateQuantity(e.target) }) }) }), e && Array.from(e).forEach(function (t) { t.addEventListener("click", function (e) { parseInt(t.nextElementSibling.value) > e.target.nextElementSibling.getAttribute("min") && (e.target.nextElementSibling.value--, n) && Array.from(n).forEach(function (t) { updateQuantity(e.target) }) }) }) } isData();
                $row.find('.cur-qty').val(1)
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            },
            isFirstItemUndeletable: true,
        });

        $(document).on('click', '.input-step .plus, .input-step .minus', function () {
            const qty = $(this).parent().find('.qty').val()
            $(this).parent().find('.cur-qty').val(qty)
        });

        $("#add-new-customer").on('click', function () {
            $("#FormAddCustomer")[0].reset()
            $("#modalAddCustomer").modal('show')
        })

        $('#customer-select').select2({
        ajax: {
            url: '/customer/live',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data.map(function(customer) {
                        return {
                            id: customer.id,
                            text: customer.first_name + ' ' + customer.last_name + ' ' + customer.phone_number
                        };
                    }),
                    pagination: {
                        more: data.current_page < data.last_page
                    }
                };
            },
            cache: true
        },
        placeholder: 'Search for a customer',
        minimumInputLength: 0,
        data: []
    });

    $("#FormAddCustomer").on('submit', function (e) {
            e.preventDefault()
            e.stopImmediatePropagation()

            const form = document.getElementById('FormAddCustomer')
            const formData = new FormData(form)
            $.ajax({
                type: "POST",
                url: "{{url('/customer')}}",
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                headers: {
                    'Accept': 'application/json'
                }
            }).done(function (response) {
                if (response.id && response.name) {
                    var newOption = new Option(response.first_name + ' ' + response.last_name + ' ' + response.phone_number, response.id, true, true);
                    $('#customer-select').append(newOption).trigger('change');
                }
                $("#modalAddCustomer").modal('hide')
            }).fail(function (response) {
                $(`#FormAddCustomer .form-control`).removeClass('is-invalid')
                $(`#FormAddCustomer [error-name]`).html('')
                const errors = response.responseJSON.errors
                Object.keys(errors).forEach(field => {
                    $(`#FormAddCustomer .form-control[name=${field}]`).addClass('is-invalid')
                    $(`#FormAddCustomer [error-name="${field}"]`).html(errors[field][0])
                })
            })
        })

        function getMenuItem(id) {
            return  $.ajax({
                type: "GET",
                url: "{{url('/openapi/menu/items')}}?menu_category_id=" + id,
                headers: {
                    'Accept': 'application/json'
                }
            })
        }

        function getMenuItemVariant(id) {
            return $.ajax({
                type: "GET",
                url: "{{url('/openapi/menu/variants')}}?menu_item_id=" + id,
                headers: {
                    'Accept': 'application/json'
                }
            })
        }


        $(document).on('change', '.select2', function () {
            const id = $(this).val()
            const index = $(this).data('id')
            const name = $(this).data('name')
            if (name === 'category') {
                getMenuItemVariant(id).done(function (response) {
                    const el = $(`.menu-item-variant[data-id=${index}]`)
                    el.select2('destroy')
                    let html = `<option value="">Select Variant</option>`
                    response.forEach(option => {
                        html += `<option value="${option.id}">${option.item.name} ${option.name} (${option.current_price})</option>`
                    })
                    el.html(html)
                    el.select2()
                })
            }
            // if(name == 'category') {
            //     getMenuItem(id).done(function (response) {
            //         const el = $(`.menu-item[data-id=${index}]`)
            //         el.select2('destroy')
            //         let html = `<option value="">Select Menu Item</option>`
            //         response.forEach(option => {
            //             html += `<option value="${option.id}">${option.name} (${option.current_price})</option>`
            //         })
            //         el.html(html)
            //         el.select2()
            //     })
            // }
            // if(name == 'item') {
            //     getMenuItemVariant(id).done(function (response) {
            //         const el = $(`.menu-item-variant[data-id=${index}]`)
            //         el.select2('destroy')
            //         let html = `<option value="">Select Variant</option>`
            //         response.forEach(option => {
            //             html += `<option value="${option.id}">${option.name} (${option.current_price})</option>`
            //         })
            //         el.html(html)
            //         el.select2()
            //     })
            // }
        })

    });
</script>
@endsection
