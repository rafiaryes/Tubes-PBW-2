<x-auth-layout>
    <!-- Outer Row -->
    <div class="row justify-content-center d-flex align-items-center min-vh-100">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row justify-content-center align-items-center">
                        <div class="col-lg-6">
                            <img src="{{ asset('Logo_KantinPintar.jpg') }}" alt="" class="w-100">
                        </div>
                        <div class="col-lg-6 justify-content-center align-items-center">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Register</h1>
                                </div>
                                <form class="user" method="POST" action="{{ route('register') }}">
                                    @csrf

                                    <div class="form-group">
                                        <input id="name" type="text" class="form-control form-control-user @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Enter name..." required autocomplete="name" autofocus>

                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <input id="email" type="email"
                                            class="form-control form-control-user @error('email') is-invalid @enderror"
                                            aria-describedby="emailHelp" placeholder="Enter Email Address..."
                                            name="email" value="{{ old('email') }}" required autocomplete="email"
                                            autofocus>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input id="password" type="password"
                                            class="form-control form-control-user @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="current-password" placeholder="Enter Password...">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input id="password-confirm" type="password" class="form-control form-control-user" name="password_confirmation" placeholder="Enter Password Confirm..." required autocomplete="new-password">
                                    </div>
                                    <button type="Submit" class="btn btn-primary btn-user btn-block">
                                        Register
                                    </button>
                                </form>
                                <hr>                                
                                <div class="text-center">
                                    <a class="small" href="{{ route('login') }}">Have Account?, login instead</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
</x-auth-layout>
