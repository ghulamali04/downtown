@extends('base')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-transparent">
            <h4 class="mb-sm-0">Create Menu Item</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('menu.index')}}">Menu</a></li>
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
                <form method="POST" action="{{ route('menu.store') }}">@csrf
                    <div class="row">
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{old('name')}}" placeholder="Name">
                            @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                name="price" value="{{old('price')}}" placeholder="Price">
                            @error('price')
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
