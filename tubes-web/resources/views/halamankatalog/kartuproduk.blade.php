<div class="col product-item">
    <div class="card h-100 border-0 shadow-sm transition-hover">
        <div class="position-relative">
            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top p-3" alt="{{ $product->name }}" style="height: 200px; object-fit: contain;">
        </div>

        <div class="card-body d-flex flex-column">
            <h6 class="card-title mb-1 fw-bold text-dark">{{ $product->name }}</h6>
            <p class="small text-muted mb-2">{{ $product->category->name }}</p>
            <h5 class="text-primary mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</h5>

            <div class="mt-auto d-grid gap-2">
                <button class="btn btn-outline-primary btn-sm rounded-pill">
                    <i class="bi bi-cart-plus me-1"></i> Add to Cart
                </button>
                <a href="/buy/{{ $product->id }}" class="btn btn-primary btn-sm rounded-pill">
                    Beli Langsung
                </a>
            </div>
        </div>
    </div>
</div>