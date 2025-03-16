@extends('base')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-transparent">
            <h4 class="mb-sm-0">Edit User</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.index')}}">User</a></li>
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
                <form method="POST" action="{{ route('user.update', ['user' => $user->id]) }}">@csrf
                    @method('PUT')
                    <div class="row">
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                name="first_name" value="{{$user->first_name}}" placeholder="First Name">
                            @error('first_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                name="last_name" value="{{$user->last_name}}" placeholder="Last Name">
                            @error('last_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" name="email"
                                value="{{$user->email}}" placeholder="Email">
                            @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select select2 @error('role') is-invalid
                            @enderror" name="role">
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="kitchen" {{ $user->role == 'kitchen' ? 'selected' : '' }}>Kitchen</option>
                                <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="reception" {{ $user->role == 'reception' ? 'selected' : '' }}>Reception</option>
                            </select>
                            @error('role')
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


<div class="row">
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Change Password</h4>
            </div><!-- end card header -->
            <div class="card-body pt- 0">
                <form method="POST" action="{{ route('user.password.update', ['user' => $user->id]) }}">@csrf
                    @method('PUT')
                    <div class="row">
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" placeholder="***********">
                            @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" col-md-4 col-sm-6 col-12 mb-3">
                            <label class="form-label">Password Confirmation</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                name="password_confirmation" placeholder="***********">
                            @error('password_confirmation')
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
