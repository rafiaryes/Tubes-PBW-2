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
                <label for="name">Nama Role</label>
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

            @if(!$permissions->isEmpty())
            <div class="form-group">
                <label>Permissions</label>
                <div class="row gap-4 px-2">
                    @php
                        // Kelompokkan permissions berdasarkan prefix
                        $groupedPermissions = $permissions->groupBy(function ($permission) {
                            // Ambil prefix sebelum titik (misalnya 'user', 'admin')
                            return explode('.', $permission->name)[0];
                        });
                    @endphp
        
                    @foreach($groupedPermissions as $prefix => $group)
                        <div class="col-md-4">
                            <h5>{{ ucfirst($prefix) }} Permissions</h5> 
                            <div class="row">
                                @foreach($group as $permission)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                   class="form-check-input"
                                                   @if(in_array($permission->id, old('permissions', $model->permissions->pluck('id')->toArray()))) checked @endif>
                                            <label class="form-check-label">{{ $permission->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('permissions')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        @endif
        
            <button type="submit" class="btn btn-primary mb-2">{{ $button }}</button>
        </form>
    </div>
</x-app-layout>
