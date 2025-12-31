<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h5 class="card-title mb-4">Edit Profile</h5>
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf @method('PATCH')
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>