@extends('base.base_admin')

@section('content')

<section id="back-button">
    <a href="{{ url()->previous() }}" class="btn btn-secondary text-xl font-bold">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
            <path fill="currentColor" d="M16.88 2.88a1.25 1.25 0 0 0-1.77 0L6.7 11.29a.996.996 0 0 0 0 1.41l8.41 8.41c.49.49 1.28.49 1.77 0s.49-1.28 0-1.77L9.54 12l7.35-7.35c.48-.49.48-1.28-.01-1.77" />
        </svg>
        Back    
    </a>    
</section>

<div class="container border rounded-md p-5 mt-5">
    @if(isset($kos))
    {{-- // form edit --}}
    <form action="{{ route('admin.form-pemilik.edit', $kos->id) }}" method="POST">
        @csrf 
        <div class="form-control">
            <label class="label">
                <span class="label-text">Pilih pemilik</span>
            </label>
            <select name="role" class="select select-bordered w-full" required>
                <option value="{{ $kos->id_pengguna }}" selected>{{ $kos->pengguna->username }}</option>
            </select>
        </div>
        <div class="form-control mt-4">
            <label class="label">
                <span class="label-text">Nama Kos</span>
            </label>
            <input type="text" name="name" placeholder="Masukan nama kos" value="{{ $kos->name }}" class="input input-bordered w-full" required>
        </div>
        <div class="form-control mt-4">
            <label class="label">
                <span class="label-text">Alamat</span>
            </label>
            <input type="text" name="alamat" placeholder="Masukan alamat" value="{{ $kos->alamat }}" class="input input-bordered w-full" required>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary w-full">Tambah Kos</button>
        </div>

    </form>
    @else
    {{-- // form add kos --}}
    <form action="{{ route('admin.form-pemilik.add-kos') }}" method="POST">
        @csrf 
        <div class="form-control">
            <label class="label">
                <span class="label-text">Pilih pemilik</span>
            </label>
            <select name="role" class="select select-bordered w-full" required>
                <option value="" disabled selected>Pilih pemilik</option>
                @foreach($pemilikBaru as $pemilik)
                    <option value="{{ $pemilik->id }}">{{ $pemilik->username }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-control mt-4">
            <label class="label">
                <span class="label-text">Nama Kos</span>
            </label>
            <input type="text" name="name" placeholder="Masukan nama kos" class="input input-bordered w-full" required>
        </div>
        <div class="form-control mt-4">
            <label class="label">
                <span class="label-text">Alamat</span>
            </label>
            <input type="text" name="alamat" placeholder="Masukan alamat" class="input input-bordered w-full" required>
        </div>
        <div class="mt-4">
            <button id="tambah-kos" type="submit" class="btn btn-primary w-full">Tambah Kos</button>
        </div>
    </form>
    @endif
    
</div>

@endsection

@section('library-js')
<script>
    $('form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this)

        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah Anda yakin ingin menyimpan perubahan?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan'
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = form.serialize();
                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Data berhasil disimpan!',
                            }).then(() => {
                                window.location.href = '{{ route('admin.manage-users') }}';
                            });
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors || { error: 'Terjadi kesalahan saat menyimpan data.' };
                        var errorMessages = Object.values(errors).join('\n');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMessages,
                        });
                    }
                });
            }
        })
    });
</script>
@endsection
