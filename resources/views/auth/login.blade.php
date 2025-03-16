@extends('auth/base')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4 card-bg-fill">

            <div class="card-body p-4">
                <div class="text-center mt-2">
                    <h5 class="text-primary">Welcome Back !</h5>
                    <p class="text-muted">Sign in to continue to downtown.</p>
                </div>
                <div class="p-2 mt-4">
                    <form action="{{route('login.store')}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter email">
                            @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="float-end">
                                <a href="{{route('password.request')}}" class="text-muted">Forgot password?</a>
                            </div>
                            <label class="form-label" for="password-input">Password</label>
                            <div class="position-relative auth-pass-inputgroup mb-3">
                                <input type="password" class="form-control pe-5 password-input @error('password')
                                is-invalid
                                @enderror" placeholder="Enter password" name="password" id="password-input">
                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                            </div>
                            @error('password')
                            <div class="invalid-feedback d-bloc">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                            <label class="form-check-label" for="auth-remember-check">Remember me</label>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-primary w-100" type="submit">Sign In</button>
                        </div>

                    </form>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->


    </div>
</div>
<!-- end row -->
@stop
