@extends('base.base')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-sm p-8 space-y-6 ">
        <div class="card bg-white shadow-xl text-primary-content w-96">
            <div class="card-body">
              <h2 class="card-title">Konfirmasi Pembayaran</h2>
              <p>Anda akan memesan kamar: <strong>{{ $kamar->kos->name }} - {{ $kamar->name }}</strong></p>
              <p>Harga: Rp{{ number_format($kamar->harga, 0, ',', '.') }}</p>
              <div class="card-actions justify-end">
                <button id="pay-button" class="btn-bayar btn btn-success text-white">Bayar Sekarang</button>
              </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('library-js')

<script>
    document.getElementById('pay-button').addEventListener('click', function () {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function (result) {
                // Redirect or notify success
                window.location.href = '/penghuni/pemesanan/index';
            },
            onPending: function (result) {
                // Handle pending result
                alert('Pending payment: ' + JSON.stringify(result));
            },
            onError: function (result) {
                // Handle error
                alert('Payment failed: ' + JSON.stringify(result));
            },
            onClose: function () {
                alert('Payment popup closed');
            }
        });
    });
</script>
@endsection