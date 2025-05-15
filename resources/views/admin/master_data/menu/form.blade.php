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
                <label for="category">Category</label>
                <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>{{ old('category', $model->category) }} required>
                    <option selected>Pilih category</option>
                    <option value="makanan">Makanan</option>
                    <option value="minuman">Minuman</option>
                    <option value="cemilan">Cemilan</option>
                </select>
                @error('category')
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
            <button type="submit" class="mb-2 btn btn-primary">{{ $button }}</button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const priceInput = document.querySelector('input[name="price"]');

            if (priceInput) {
                priceInput.addEventListener('input', function(e) {
                    const value = e.target.value.replace(/\D/g, '');
                    e.target.value = formatNumber(value);
                });

                const form = priceInput.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        priceInput.value = priceInput.value.replace(/\./g, '');
                    });
                }
            }

            function formatNumber(number) {
                return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        });
    </script>
</x-app-layout>
