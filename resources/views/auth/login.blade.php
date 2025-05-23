<x-auth-layout>
    <!-- Outer Row -->
    <div class="row justify-content-center d-flex align-items-center min-vh-100">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="border-0 shadow-lg card o-hidden">
                <div class="p-0 card-body">
                    <!-- Nested Row within Card Body -->
                    <div class="row justify-content-center align-items-center">
                        <div class="col-lg-6">
                            <img src="{{ asset('Logo_Dapoer.jpg') }}" alt="" class="w-100">
                        </div>
                        <div class="col-lg-6 justify-content-center align-items-center">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="mb-4 text-gray-900 h4">Welcome Back!</h1>
                                </div>
                                <form class="user" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input id="email" type="email"
                                            class="form-control form-control-user @error('email') is-invalid @enderror"
                                            aria-describedby="emailHelp" placeholder="Enter Email Address..."
                                            name="email" value="{{ old('email') }}" required autocomplete="email"
                                            autofocus>

                                        @error('email')
                                            <span class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input id="password" type="password"
                                            class="form-control form-control-user @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="current-password" placeholder="Enter Password...">

                                        @error('password')
                                            <span class="invalid-feedback d-block">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <button type="Submit" class="btn btn-info btn-user btn-block">
                                        Login
                                    </button>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="">Forgot Password?</a>
                                </div>
                                <div class="text-center">
                                    <a class="small" href="{{ route('register') }}">Create an Account!</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
</x-auth-layout>
