@extends('base')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-transparent">
            <h4 class="mb-sm-0">Edit Customer</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('customer.index')}}">Customer</a></li>
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
                <form method="POST" action="{{ route('customer.update', ['customer' => $customer->id]) }}">@csrf
                    @method('PUT')
                    <div class="row">
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                name="first_name" value="{{$customer->first_name}}" placeholder="First Name">
                            @error('first_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                name="last_name" value="{{$customer->last_name}}" placeholder="Last Name">
                            @error('last_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" name="email"
                                value="{{$customer->email}}" placeholder="Email">
                            @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number"
                                value="{{$customer->phone_number}}" placeholder="Phone Number">
                            @error('phone_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-12 col-sm-12 col-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" name="address"
                            placeholder="Address">{{$customer->address}}</textarea>
                            @error('address')
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
