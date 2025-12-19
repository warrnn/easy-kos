@extends('base.base')

@section('content')
<div class="container mx-auto py-4">
    <!-- Button home dan pesanan -->
    {{-- <div class="flex justify-end space-x-4 mb-6">
        <button class="btn btn-ghost">Home</button>
        <button class="btn btn-ghost">Pemesanan</button>
    </div> --}}

    <div class="navbar bg-gray-800 rounded-full border">
        <div class="flex-1">
            <a class="btn btn-ghost text-xl text-white">Home</a>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-2 text-white gap-4">
                <li><a href="/penghuni/index" class="font-bold bg-gray-500">Home</a></li>
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

    <section id="greet-user">
        <h1 class="text-3xl font-bold mt-5 ">Halo {{ Auth::user()->username }}</h1>
    </section>
    @if ($listKos->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
        @foreach($listKos as $kos)
            <a href="{{ route('penghuni.kos.index', $kos->id) }}"
                class="card bg-white shadow-lg rounded-lg overflow-hidden">
                <!-- Gambar -->
                <figure class="h-40 bg-gray-300 flex justify-center items-center">
                    <span class="text-lg font-bold text-gray-700">GAMBAR</span>
                </figure>
                <!-- Isi Kartu -->
                <div class="p-4 bg-gray-100">
                    <h3 class="text-lg font-bold">{{ $kos->name }}</h3>
                    <p class="text-gray-600 text-sm mt-2">{{ $kos->alamat }}</p>
                    <p class="text-gray-800 text-sm mt-2">Pemilik: {{ $kos->pengguna->username }}</p>
                    <p class="text-gray-800 text-sm mt-2">Total Kamar: {{ $kos->kamar_count }}</p>
                </div>
            </a>
        @endforeach
    </div>      
    @else
        <h1 class="text-3xl font-bold text-center mt-5 ">Belum ada kos tersedia</h1>
    @endif
    
</div>
@endsection

@section('library-js')
<script>
    // function saveKosIdAndRedirect(event, kosId) {
    //     event.preventDefault(); // Menghentikan aksi default dari klik (navigasi)

    //     // Menyimpan ID kos ke sessionStorage
    //     sessionStorage.setItem('kos_id', kosId);

    //     // Navigasi ke URL setelah ID disimpan
    //     window.location.href = '/penghuni/kos/index'; // Ganti URL sesuai kebutuhan
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
