<x-app-layout>
    <div class="container-fluid">        
         <!-- Display Breadcrumbs -->
         <div aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach (Breadcrumbs::generate() as $breadcrumb)
                    <li class="breadcrumb-item">
                        @if ($breadcrumb->url)
                            <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                        @else
                            {{ $breadcrumb->title }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
        <form action="{{ $route }}" method="POST">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif
            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $model->name) }}" required>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $model->email) }}" required>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    {{ $method === 'POST' ? 'required' : '' }}>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                    {{ $method === 'POST' ? 'required' : '' }}>
                @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                    <option value="">Pilih Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', $model->roles->pluck('name')->first()) === $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>            
            <button type="submit" class="btn btn-primary mb-2">{{ $button }}</button>
        </form>
    </div>
</x-app-layout>
