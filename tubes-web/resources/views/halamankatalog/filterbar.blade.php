<div class="row g-3 mb-4 align-items-center">
    <div class="col-md-4">
        <div class="input-group border rounded shadow-sm">
            <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
            <input type="text" id="liveSearch" class="form-control border-0 shadow-none" placeholder="Cari produk...">
        </div>
    </div>

    <div class="col-md-3">
        <select class="form-select border shadow-sm" id="categoryFilter">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <select class="form-select border shadow-sm" id="sortBy">
            <option value="latest">Terbaru</option>
            <option value="price_low">Harga Terendah</option>
            <option value="price_high">Harga Tertinggi</option>
            <option value="popular">Terpopuler</option>
        </select>
    </div>

    <div class="col-md-2 text-end d-none d-md-block">
        <button class="btn btn-light border shadow-sm" id="toggleGrid"><i class="bi bi-grid-3x3-gap"></i></button>
        <button class="btn btn-light border shadow-sm" id="toggleList"><i class="bi bi-list-task"></i></button>
    </div>
</div>