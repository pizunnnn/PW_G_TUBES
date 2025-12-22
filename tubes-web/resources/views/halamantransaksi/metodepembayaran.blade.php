<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            @include('transactions.partials.checkout-form')
        </div>

        <div class="col-lg-4">
            @include('transactions.partials.order-summary')
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>