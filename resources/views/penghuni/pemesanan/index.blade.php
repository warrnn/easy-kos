@extends('base.base')

@section('content')
<div class="container mx-auto py-4">
    <div class="navbar bg-gray-800 rounded-full border">
        <div class="flex-1">
            <a class="btn btn-ghost text-xl text-white">Pemesanan</a>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-2 text-white gap-4">
                <li><a href="/penghuni/index" class="font-bold hover:bg-gray-500">Home</a></li>
                <li><a href="/penghuni/pemesanan/index" class="font-bold bg-gray-500">Pemesanan</a></li>
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
        <h1 class="text-3xl font-bold mt-5 ">Riwayat Pemesanan</h1>
    </section>
    <div class="flex items-center justify-center my-10">
        <div class="w-full max-w-screen-lg mx-auto">
            <table id="table-pesanan" class="table-auto w-full border-collapse border border-gray-300 bg-white shadow-md rounded-lg">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-6 py-3 border border-gray-300 text-left">Nama Kos</th>
                        <th class="px-6 py-3 border border-gray-300 text-left">No Kamar</th>
                        <th class="px-6 py-3 border border-gray-300 text-left">Nama Kamar</th>
                        <th class="px-6 py-3 border border-gray-300 text-left">Status</th>
                        <th class="px-6 py-3 border border-gray-300 text-left">Harga</th>
                        <th class="px-6 py-3 border border-gray-300 text-left">Review</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listPesanan as $pesanan)
                    <tr>
                        <td class="px-6 py-3 border border-gray-300">{{ $pesanan->kamar->kos->name }}</td>
                        <td class="px-6 py-3 border border-gray-300">{{ $pesanan->id_kamar }}</td>
                        <td class="px-6 py-3 border border-gray-300">{{ $pesanan->kamar->name }}</td>
                        <td class="px-6 py-3 border border-gray-300">{{ $pesanan->status_pemesanan }}</td>
                        <td class="px-6 py-3 border border-gray-300">{{ $pesanan->kamar->harga }}</td>
                        <td class="px-6 py-3 border border-gray-300">
                            <a href="{{ route('penghuni.review', $pesanan->kamar->kos->id) }}" class="btn-review btn btn-warning btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 32 32">
                                    <path fill="currentColor" d="m16 8l1.912 3.703l4.088.594L19 15l1 4l-4-2.25L12 19l1-4l-3-2.703l4.2-.594z" />
                                    <path fill="currentColor" d="M17.736 30L16 29l4-7h6a1.997 1.997 0 0 0 2-2V8a1.997 1.997 0 0 0-2-2H6a1.997 1.997 0 0 0-2 2v12a1.997 1.997 0 0 0 2 2h9v2H6a4 4 0 0 1-4-4V8a4 4 0 0 1 4-4h20a4 4 0 0 1 4 4v12a4 4 0 0 1-4 4h-4.835Z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('library-js')
<script>
    $(document).ready(function() {
        $('#table-pesanan').DataTable({
            language: {
                searchPlaceholder: "Cari pesanan", 
            }
        });
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
    });
</script>
@endsection
