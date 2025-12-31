<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h5 class="card-title mb-4">Ganti Password</h5>
        <form action="{{ route('password.update') }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Password Lama</label>
                <input type="password" name="current_password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-danger">Update Password</button>
        </form>
    </div>
</div>