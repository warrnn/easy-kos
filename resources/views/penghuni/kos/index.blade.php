@extends('base.base')

@section('content')
<!-- Main Layout -->
<div class="flex h-screen">
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success ') }}", // Perbaiki dengan menghapus spasi ekstra
            showConfirmButton: false,
            timer: 3000, // Tampilkan selama 3 detik
        });
    </script>
    @endif

    <!-- Sidenav -->
    <aside class="w-128 bg-gray-800 text-white h-full p-4 space-y-4">
        @if($kos)
        <h1 class="text-4xl font-bold mb-4" id="kos-name">{{  $kos->name }}</h1>
        <p><strong>Owner:</strong> <span id="kos-contact">{{ $kos->pengguna->username }}</span></p>
        <p><strong>Alamat Kos:</strong> <span id="kos-address">{{ $kos->alamat }}</span></p>
        @endif
        
        <hr>
        <p class="text-lg font-bold">Review Customer:</p>
        @if ($listReview->isNotEmpty())
        @foreach($listReview as $review)
        <div class="card bg-neutral shadow-lg text-white ">
            <div class="card-body">
                <h3 class="card-title">{{ $review->pengguna->username}}</h3>
                <p>{{ $review->isi }}</p>
            </div>
        </div>
        @endforeach
        @else
        <div class="card bg-neutral shadow-lg text-white ">
            <div class="card-body">
                <h3 class="card-title">Belum ada review!</h3>
            </div>
        </div>
        @endif

        
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 overflow-y-auto">

        <!-- Button home dan pesanan -->
        <div class="navbar bg-gray-800 rounded-full border">
            <div class="flex-1">
                <a class="btn btn-ghost text-xl text-white">List Kamar</a>
            </div>
            <div class="flex-none">
                <ul class="menu menu-horizontal px-2 text-white gap-4">
                    <li><a href="/penghuni/index" class="font-bold hover:bg-gray-500">Home</a></li>
                    <li><a href="/penghuni/pemesanan/index" class="font-bold hover:bg-gray-500">Pemesanan</a></li>
                    <li>
                        <form id="logout-form" class="hover:bg-red-500" action="{{ route('authentication.logout') }}" method="POST">
                            @csrf
                            <a class="font-bold " id="logout-link">Logout</a>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Room Cards -->
        @if ($listKamar->isNotEmpty())
        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($listKamar as $kamar)
            <div class="kamar-card card bg-white shadow-lg" data-id-kos="{{ $kamar->id_kos }}">
                <figure class="h-48 bg-gray-200 flex items-center justify-center">
                    <span class="text-xl font-bold">GAMBAR KAMAR</span>
                </figure>
                <div class="card-body">
                    <h2 class="card-title">{{ $kamar->name }}</h2>
                    <p>Status: {{ $kamar->status }}</p>
                    <p>Harga kamar: Rp{{ number_format($kamar->harga, 0, ',', '.') }}</p>
                    <p>{{ $kamar->deskripsi }}</p>
                    <div class="card-actions justify-end">
                        @if($kamar->status === 'booked')
                            <!-- Tombol dinonaktifkan jika kamar sudah penuh -->
                            <button class="btn btn-neutral" disabled>Sudah Penuh</button>
                        @else
                            <!-- Form untuk memesan kamar -->
                            <form action="{{ route('penghuni.kos.pesan', $kamar->id)}}" method="POST">
                                @csrf
                                <button id="pay-button" type="submit" class="btn btn-neutral">Pesan</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <h1 class="text-3xl font-bold text-center mt-5 ">Belum ada kamar tersedia</h1>
        @endif

    </main>
</div>
@endsection

@section('library-js')
<script>    
    // Mengambil ID kos dari sessionStorage dan menampilkannya dalam alert
    // document.addEventListener('DOMContentLoaded', function() {
    //     var kosId = sessionStorage.getItem('kos_id');

    //     if (kosId) {
    //         // Ambil elemen kamar yang sesuai
    //         var kamarCards = document.querySelectorAll('[data-id-kos]');
    //         kamarCards.forEach(function(card) {
    //             if (card.dataset.idKos !== kosId) {
    //                 card.style.display = 'none'; // Sembunyikan kartu kamar yang tidak sesuai
    //             }
    //         });
    //     }
    // });

    // Ambil kos_id dari sessionStorage
    // let kosId = sessionStorage.getItem('kos_id');

    // if (kosId) {
    //     // Kirimkan request ke backend (misalnya menggunakan Fetch atau Ajax)
    //     fetch(`/kos/${kosId}`)
    //         .then(response => response.json())
    //         .then(data => {
    //             // Tampilkan detail kos pada halaman
    //             document.querySelector('#kos-name').innerText = data.name;
    //             document.querySelector('#kos-contact').innerText = data.contact_person;
    //             document.querySelector('#kos-address').innerText = data.alamat;
    //             document.querySelector('#kos-rating').innerText = `⭐⭐⭐⭐`;
    //             document.querySelector('#kos-notes').innerText = data.catatan;
    //         })
    //         .catch(error => {
    //             console.error('Error:', error);
    //         });
    // }

    document.getElementById('logout-link').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, logout!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    });
</script>
@endsection
