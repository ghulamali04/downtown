@extends('base')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-transparent">
            <h4 class="mb-sm-0">Edit Order</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('order.index')}}">Order</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Edit</h4>
            </div><!-- end card header -->
            <div class="card-body pt- 0">
                <form method="POST" action="{{ route('order.update', ['order' => $order->id]) }}">@csrf
                    @method('PUT')
                    <div class="row">
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-control select2 @error('type') is-invalid @enderror" name="type">
                                <option value="delivery" {{$order->type == 'delivery' || !old('type') ? 'selected' : ''}}>Delivery</option>
                                <option value="pickup" {{$order->type == 'pickup' ? 'selected' : ''}}>Pickup</option>
                                <option value="dining" {{$order->type == 'dining' ? 'selected' : ''}}>Dining</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Customer <span id="add-new-customer" style="cursor: pointer"><i class="ri-add-circle-line ri-xl"></i></span></label>
                            <select class="form-control select2 @error('customer') is-invalid @enderror" name="customer" id="customer-select">
                                @if(@$order->customer)
                                <option value="{{ $order->customer->id }}">{{ $order->customer->first_name . ' ' . $order->customer->last_name }}</option>
                                @endif
                            </select>
                            @error('customer')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Table Number</label>
                            <select class="form-select select2 @error('table_number') is-invalid @enderror" name="table_number">
                                <option value="">Select</option>
                                @for ($i = 1; $i <= 20; $i++)
                                    <option value="G{{$i}}" {{$order->table_number == 'G'.$i ? 'selected' : ''}}>G{{$i}}</option>
                                @endfor
                                @for ($i = 1; $i <= 20; $i++)
                                    <option value="F{{$i}}" {{$order->table_number == 'F'.$i ? 'selected' : ''}}>F{{$i}}</option>
                                @endfor
                                @for ($i = 1; $i <= 50; $i++)
                                    <option value="T{{$i}}" {{$order->table_number == 'T'.$i ? 'selected' : ''}}>T{{$i}}</option>
                                @endfor
                            </select>
                            @error('table_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="is_bar" value="1" id="IsBar" {{$order->is_bar == 1 ? 'checked' : ''}}>
                                <label class="form-check-label" for="Isbar"> Include Bar Items</label>
                              </div>
                        </div>

                        <div class="col-md-12 col-sm-12 col-12 mb-3">
                            <label class="form-label">Instructions</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" name="instructions">{{$order->instructions}}</textarea>
                            @error('instructions')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row items-repeater">
                            <div class="col-md-12" data-repeater-list="menuItems">
                                @if(count($order->items) > 0)
                                @foreach ($order->items as $oldItem)
                                <div class="row" data-repeater-item>
                                    <div class="col-sm-3 mb-3">
                                        <select class="form-select select2 menu-item-category" name="category" data-name="category" data-id="{{$loop->iteration}}">
                                            <option value="" selected>Select Category</option>
                                            @foreach ($menuCateogryItems as $item)
                                            <option value="{{$item->id}}" {{$item->id == @$oldItem->menu_category_id ? 'selected' : ''}}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <select class="form-select select2 menu-item-variant" name="variant" data-name="variant" data-id="{{$loop->iteration}}">
                                            <option value="" selected>Select Variant</option>
                                            <option value="{{@$oldItem->menu_item_variant_id}}" selected>{{ @$oldItem->menu_item_variant->name . '('.@$oldItem->menu_item_variant->current_price.')' }}</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <div class="input-step">
                                            <input type="hidden" class="cur-qty" name="qty" value="{{ $oldItem->qty }}">
                                            <button type="button" class="minus">–</button>
                                            <input type="number" class="qty" name="qty" value="{{ $oldItem->qty }}" min="1" max="100">
                                            <button type="button" class="plus">+</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <button type="button" class="btn btn-sm btn-danger mt-2" data-repeater-delete><i class="ri-delete-bin-2-line"></i></button>
                                    </div>
                                </div>
                                @endforeach
                                @else
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
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <button type="button" class="btn btn-primary" data-repeater-create>Add Order Item</button>
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
    // Cache for API responses to avoid repeated requests
    const apiCache = new Map();

    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function uniqueId() {
        return 'id' + Math.random().toString(36).substr(2, 9);
    }

    $(document).ready(function() {
        $("#add-new-customer").on('click', function() {
            $("#FormAddCustomer")[0].reset();
            $("#FormAddCustomer .form-control").removeClass('is-invalid');
            $("#FormAddCustomer [error-name]").html('');
            $("#modalAddCustomer").modal('show');
        });

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

        window.isData = function() {
        // Do nothing - we'll handle quantity controls through jQuery
        console.log('isData() function overridden to prevent duplicate handlers');
    };

    // Remove ALL event handlers from quantity controls to prevent duplicates
    $('.input-step .plus, .input-step .minus').off();
    $(document).off('click', '.input-step .plus');
    $(document).off('click', '.input-step .minus');
    $(document).off('click.qty');
    $(document).off('click.qty-control');

    // Remove vanilla JS event listeners that might have been added by isData()
    $('.input-step .plus, .input-step .minus').each(function() {
        this.removeEventListener('click', arguments.callee);
        // Clone and replace to remove all event listeners
        const newElement = this.cloneNode(true);
        this.parentNode.replaceChild(newElement, this);
    });

    // Use a single delegated event handler with a unique namespace
    $(document).on('click.quantity-handler', '.input-step .plus, .input-step .minus', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        const $button = $(this);
        const $input = $button.siblings('.qty');
        const $curQty = $button.siblings('.cur-qty');
        const currentVal = parseInt($input.val()) || 1;
        const min = parseInt($input.attr('min')) || 1;
        const max = parseInt($input.attr('max')) || 999;

        let newVal = currentVal;

        if ($button.hasClass('plus') && currentVal < max) {
            newVal = currentVal + 1;
            console.log("Current Inventory Val: " + currentVal);
        } else if ($button.hasClass('minus') && currentVal > min) {
            newVal = currentVal - 1;
        }

        $input.val(newVal);
        $curQty.val(newVal);
    });


        // Optimized repeater initialization
        $(".items-repeater").repeater({
            initEmpty: false,
            show: function() {
                const $row = $(this);
                const index = uniqueId();

                // Set data-id attributes first
                $row.find("select.menu-item-category").attr('data-id', index);
                $row.find("select.menu-item").attr('data-id', index);
                $row.find("select.menu-item-variant").attr('data-id', index);

                // Initialize select2 elements
                $row.find("select.menu-item-category, select.menu-item, select.menu-item-variant").select2({
                    width: '100%',
                    placeholder: function() {
                        return $(this).data('placeholder') || 'Select an option';
                    }
                });



                // Clean any existing event listeners from new quantity controls
            $row.find('.input-step .plus, .input-step .minus').each(function() {
                // Remove any vanilla JS event listeners
                const newElement = this.cloneNode(true);
                this.parentNode.replaceChild(newElement, this);
            });

            // Set default quantity
            $row.find('.cur-qty').val(1);
            $row.find('.qty').val(1);

            // Prevent isData() from being called on new elements
            $row.find('.input-step .plus, .input-step .minus').addClass('jquery-handled');

                // Animate row appearance
                $row.slideDown(200);
            },
            hide: function(deleteElement) {
                // Clean up select2 instances before removing
                // if ($(this).find('.select2').length > 0) {
                //     $(this).find('.select2').select2('destroy');
                // }
                $(this).slideUp(200, deleteElement);
            },
            isFirstItemUndeletable: true,
        });

        function getMenuItemVariant(categoryId) {
            const cacheKey = `variants_${categoryId}`;

            if (apiCache.has(cacheKey)) {
                return Promise.resolve(apiCache.get(cacheKey));
            }

            return $.ajax({
                type: "GET",
                url: "{{url('/openapi/menu/variants')}}",
                data: { menu_category_id: categoryId },
                timeout: 5000,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).done(function(response) {
                apiCache.set(cacheKey, response);
                // Clear cache after 5 minutes
                setTimeout(() => apiCache.delete(cacheKey), 300000);
            }).fail(function(xhr) {
                console.error('Failed to load menu variants:', xhr.statusText);
            });
        }

        function getMenuItem(categoryId) {
            const cacheKey = `items_${categoryId}`;

            if (apiCache.has(cacheKey)) {
                return Promise.resolve(apiCache.get(cacheKey));
            }

            return $.ajax({
                type: "GET",
                url: "{{url('/openapi/menu/items')}}",
                data: { menu_category_id: categoryId },
                timeout: 5000,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).done(function(response) {
                apiCache.set(cacheKey, response);
                // Clear cache after 5 minutes
                setTimeout(() => apiCache.delete(cacheKey), 300000);
            }).fail(function(xhr) {
                console.error('Failed to load menu items:', xhr.statusText);
            });
        }

        // Debounced change handler for better performance
        const handleCategoryChange = debounce(function(categoryId, index) {
            const $variantSelect = $(`.menu-item-variant[data-id="${index}"]`);

            if (!categoryId) {
                $variantSelect.html('<option value="">Select Variant</option>').select2();
                return;
            }

            // Show loading state
            $variantSelect.html('<option value="">Loading...</option>').select2();

            getMenuItemVariant(categoryId).then(function(response) {
                let html = '<option value="">Select Variant</option>';

                if (response && Array.isArray(response)) {
                    response.forEach(option => {
                        const itemName = option.item?.name || 'Unknown Item';
                        const variantName = option.name ? ` ${option.name}` : '';
                        const price = option.current_price || '0';
                        html += `<option value="${option.id}">${itemName}${variantName} (${price})</option>`;
                    });
                }

                $variantSelect.html(html).select2();
            }).catch(function() {
                $variantSelect.html('<option value="">Error loading variants</option>').select2();
            });
        }, 300);

        // Optimized change handler with event delegation
        $(document).on('change', 'select[data-name="category"]', function() {
            const categoryId = $(this).val();
            const index = $(this).data('id');
            if (index || index == 0) {
                handleCategoryChange(categoryId, index);
            }
        });

    });
</script>
@endsection
