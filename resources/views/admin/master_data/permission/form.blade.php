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
                <label for="name">Nama Permission</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $model->name) }}" required>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="guard_name">Guard Name</label>
                <input type="text" name="guard_name" class="form-control @error('guard_name') is-invalid @enderror"
                    value="{{ old('guard_name', $model->guard_name) }}" required>
                @error('guard_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Roles yang memiliki permission ini</label>
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                    class="form-check-input"
                                    @if(in_array($role->id, old('roles', $model->roles->pluck('id')->toArray()))) checked @endif>
                                <label class="form-check-label">{{ $role->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('roles')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mb-2">{{ $button }}</button>
        </form>
    </div>
</x-app-layout>
