<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h5 class="card-title mb-4">Riwayat Pembelian</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop data dari backend --}}
                    <tr>
                        <td>#TRX12345</td>
                        <td><span class="badge bg-success text-white">Selesai</span></td>
                        <td>Rp 250.000</td>
                        <td><a href="#" class="btn btn-sm btn-outline-primary">Detail</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>