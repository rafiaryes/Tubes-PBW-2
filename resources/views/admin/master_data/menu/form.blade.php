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
        <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($method === 'PUT')
            @method('PUT')
            @endif
            <div class="form-group">
                <label for="nama">Nama Menu</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                    value="{{ old('nama', $model->nama) }}" required>
                @error('nama')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $model->deskripsi) }}</textarea>
                @error('deskripsi')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="price">Harga</label>
                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                    value="{{ old('price', default: $model->price) }}" required>
                @error('price')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror"
                    value="{{ old('stok', $model->stok) }}" required>
                @error('stok')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="image">Gambar</label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                    {{ $method === 'POST' ? 'required' : '' }} onchange="previewImage(event)">
                @if ($model->image)
                <img src="{{ asset('storage/' . $model->image) }}" id="imagePreview" width="400" class="mt-2">
                @else
                <img id="imagePreview" width="400" class="mt-2" style="display:none;">
                @endif
                @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary mb-2">{{ $button }}</button>
        </form>
    </div>
</x-app-layout>