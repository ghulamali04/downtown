@extends('base')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-transparent">
            <h4 class="mb-sm-0">Create Inventory Record</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('tracking.index')}}">Inventory Record</a></li>
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
                <form method="POST" action="{{ route('tracking.store') }}">@csrf
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Inventory Item</label>
                            <select class="form-select @error('inventory_item_id') is-invalid @enderror" name="inventory_item_id">
                                <option value="">Select</option>
                                @foreach ($items as $item)
                                <option value="{{$item->id}}" {{old('inventory_item_id') == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                @endforeach
                            </select>
                            @error('inventory_item_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Date</label>
                            <input type="text" class="form-control flatpickr @error('date') is-invalid @enderror"
                                name="date" value="{{old('date')}}" placeholder="Date">
                            @error('date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" name="type">
                                <option value="">Select</option>
                                <option value="purchase" {{old('type') == 'purchase' ? 'selected' : ''}}>Purchase</option>
                                <option value="used" {{old('type') == 'used' ? 'selected' : ''}}>Used</option>
                                <option value="returned" {{old('type') == 'returned' ? 'selected' : ''}}>Returned</option>
                                <option value="wasted" {{old('type') == 'wasted' ? 'selected' : ''}}>Wasted</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Amount</label>
                            <input type="text" class="form-control @error('amount') is-invalid @enderror" name="amount"
                                value="{{old('amount')}}" placeholder="Amount">
                            @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-12 col-sm-12 col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description"
                            placeholder="Description">{{old('description')}}</textarea>
                            @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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

@stop
