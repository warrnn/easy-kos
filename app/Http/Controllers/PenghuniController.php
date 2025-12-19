<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pengguna;
use App\Models\Pesanan;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class PenghuniController extends Controller
{
    // public function index() {
    //     return view('penghuni.index');
    // }

    public function showAllKos()
    {
        $listKos = Kos::with(['pengguna', 'kamar'])->withCount('kamar')->get();
        return view('penghuni.index', [
            "listKos" => $listKos
        ]);
    }

    public function showAllKamar($kosId)
    {
        $kos = Kos::findOrFail($kosId);

        $listKamar = Kamar::where('id_kos', $kosId)->get();
        $listReview = Review::where('id_kos', $kosId)->get();

        return view('penghuni.kos.index', [
            "listKamar" => $listKamar,
            "listKos" => $kos,
            "listReview" => $listReview,
        ]);
    }

    public function show($id)
    {
        // Ambil data kos berdasarkan ID
        $kos = Kos::find($id);

        // Cek apakah kos ditemukan
        if ($kos) {
            return response()->json([
                'name' => $kos->name,
                'contact_person' => $kos->contact_person,
                'alamat' => $kos->alamat,
                'catatan' => $kos->catatan,
            ]);
        } else {
            return response()->json(['error' => 'Kos not found'], 404);
        }
    }

    public function pesanKamar(Request $request, $kamarId) {
        // Validasi data (misalnya, apakah user sudah login)
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu!');
        }

        $username = Auth::id(); 
        $userId = Pengguna::query()->where('username', $username)->first()->id;

        // Cek apakah kamar ada
        $kamar = Kamar::find($kamarId);
        if (!$kamar) {
            return redirect()->back()->with('error', 'Kamar tidak ditemukan!');
        }
        $kosName = $kamar->kos->name;

        // Buat pesanan terlebih dahulu
        $pemesanan = new Pesanan();
        $pemesanan->id_pengguna = $userId;
        $pemesanan->id_kamar = $kamarId;
        $pemesanan->status_pemesanan = 'pending';
        $pemesanan->save(); // Simpan untuk mendapatkan ID

        // Gunakan ID pemesanan sebagai order_id
        $orderId = $pemesanan->id;

        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        // Data untuk Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $kamar->harga,
            ],
            'item_details' => [
                [
                    'id' => $kamar->id,
                    'price' => $kamar->harga,
                    'quantity' => 1,
                    'name' => $kosName . ' - ' . $kamar->name,
                ],
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email, // Pastikan email user ada
                'phone' => Auth::user()->phone ?? '08111222333', // Gunakan default jika kosong
            ],
        ];

        try {
            // Dapatkan Snap Token dari Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            return view('penghuni.kos.payment.index', compact('snapToken', 'kamar'));
        } catch (\Exception $e) {
            logger()->error('Midtrans Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showPemesanan()
    {
        // Mengambil ID user yang sedang login
        $username = Auth::id(); // Atau Auth::user()->id
        $userId = Pengguna::query()->where('username', $username)->first()->id;

        $listPesanan = Pesanan::with(['kamar.kos'])
            ->where('id_pengguna', $userId)->get();
        return view('penghuni.pemesanan.index', compact('listPesanan'));
    }

    public function formReview($idKos)
    {
        $kos = Kos::find($idKos);
        return view('penghuni.review.index', compact('kos'));
    }

    public function addReview(Request $request, $idKos)
    {
        $request->validate([
            'isi' => 'required|string|max:255',
        ]);
        $review = new Review();
        $review->isi = $request->isi;
        $review->tanggal_review = now();
        $review->id_pengguna = Auth::user()->id;
        $review->id_kos = $idKos;

        $review->save();

        return redirect()->route('penghuni.index')->with('success', 'Review berhasil ditambahkan!');
    }
}
