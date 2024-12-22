<div class="mb-4 col-md-3 col-sm-6">
    <div class="shadow-sm card" style="border-radius: 12px;">
        <img src="{{ asset('storage/' . $menu->image) }}" class="card-img-top" alt="{{ $menu->nama }}" style="height: 200px; object-fit: cover;">
        <div class="text-center card-body">
            <h5 class="card-title">{{ $menu->nama }}</h5>
            <p class="card-text">Rp  {{ number_format($menu->price, 0, ',', '.') }}</p>
            <a href="{{ route('user.add-menu', $menu->id) }}" class="btn btn-warning w-100" style="border: 1px solid #EBE5DD; background-color: #F8BF40;">Tambah ke Keranjang</a>
        </div>
    </div>
</div>
