@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="list-group list-group-flush" id="profile-tabs">
                    <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#edit-form">
                        <i class="bi bi-person me-2"></i> Edit Profile
                    </a>
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#security">
                        <i class="bi bi-lock me-2"></i> Ganti Password
                    </a>
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#history">
                        <i class="bi bi-clock-history me-2"></i> Riwayat Pesanan
                    </a>
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#vouchers">
                        <i class="bi bi-ticket-perforated me-2"></i> Voucher Saya
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="edit-form">
                    @include('profile.partials.edit-profile-form')
                </div>

                <div class="tab-pane fade" id="security">
                    @include('profile.partials.change-password-form')
                </div>

                <div class="tab-pane fade" id="history">
                    @include('profile.partials.purchase-history')
                </div>

                <div class="tab-pane fade" id="vouchers">
                    @include('profile.partials.voucher-list')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection