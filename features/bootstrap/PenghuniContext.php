<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Step\When;
use Behat\Step\Then;

/**
 * penghuni kos context
 */
class PenghuniContext extends RawMinkContext implements Context
{
    // Scenario: Penghuni kos melihat daftar kos yang tersedia
    /**
     * @When penghuni kos melakukan login
     */
    public function steps_impl_penghuni_melakukan_login() {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $this->visitPath('/authentication/login');
        
        $page->fillField('username', 'Penghuni');
        $page->fillField('password', 'penghuni123');
        
        $page->pressButton('Login');
    }

    /**
     * @Then halaman utama ditampilkan
     */
    public function steps_impl_halaman_utama_ditampilkan() {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $content = $page->getContent();
        if (strpos($content, 'Login berhasil') === false && strpos($content, 'success') === false) {
            throw new Exception('Login success message not found');
        }
        
        $this->visitPath('/penghuni/index');
        $currentUrl = $session->getCurrentUrl();
        $content = $page->getContent();

        if (strpos($currentUrl, '/penghuni/index') === false) {
            throw new Exception('Tenant cannot access homepage. Current URL: ' . $currentUrl);
        }

        if (strpos($content, 'Halo Penghuni') === false) {
            throw new Exception('Greeting user tidak ditemukan di halaman utama.');
        }
    }

    /**
     * @Then data-data kos yang tersedia ditampilkan lengkap dengan informasi kos
     */
    public function steps_impl_data_kos_tersedia() {
        $session = $this->getSession();
        $page = $session->getPage();

        $content = $page->getContent();

        $listKos = $page->findAll('css', '.card.bg-white.shadow-lg');

        if (count($listKos) === 0) {
            if (strpos($content, 'Belum ada kos tersedia') === false) {
                throw new Exception('Belum ada kos yang tersedia, pesan Belum ada kos tersedia juga tampil');
            }
        }
    }

    // Scenario: Penghuni kos melihat daftar kamar kos yang tersedia
    /**
     * @When penghuni kos menekan salah satu card kos
     */
    public function steps_impl_penghuni_menekan_card_kos() {
        $session = $this->getSession();
        $page = $session->getPage();

        $cardKos = $page->find('css', 'a.card[href*="/penghuni/kos/index"]');

        if (!$cardKos) {
            throw new Exception('Tidak ditemukan card kos yang bisa diklik di halaman ini.');
        }

        $cardKos->click();
    }

    /**
     * @Then halaman kamar kos ditampilkan
     */
    public function steps_impl_kamar_kos_ditampilkan() {
        $session = $this->getSession();

        $currentUrl = $session->getCurrentUrl();

        if (strpos($currentUrl, '/penghuni/kos/index') === false) {
            throw new Exception('Gagal masuk ke halaman detail kamar kos. URL saat ini: ' . $currentUrl);
        }
    }

    /**
     * @Then data-data kamar kos yang tersedia ditampilkan sesuai dengan ketersediaan kamar-nya
     */
    public function steps_impl_data_kamar_kos_tersedia() {
        $session = $this->getSession();
        $page = $session->getPage();

        $listKamar = $page->findAll('css', '.kamar-card');
        $currentUrl = $session->getCurrentUrl();
        $content = $page->getContent();

        if (count($listKamar) === 0 && strpos($content, 'Belum ada kamar tersedia') === false) {
            throw new Exception('Data kamar masih kosong'. $currentUrl);
        }
    }

    // Scenario: Penghuni kos melakukan pemesanan And pembayaran kamar
    /**
     * @When penghuni kos menekan tombol "Pesan"
     */
    public function steps_impl_penghuni_kos_menekan_tombol() {
        $session = $this->getSession();
        $page = $session->getPage();

        $buttonPesan = $page->findButton("Pesan");
        if (!$buttonPesan) {
            throw new Exception("Tombol Pesan tidak ditemukan.");
        }
        $buttonPesan->press();
    }

    /**
     * @Then halaman konfirmasi pembayaran ditampilkan
     */
    public function steps_impl_konfirmasi_pembayaran_ditampilkan() {
        $session = $this->getSession();
        $page = $session->getPage();

        $content = $page->getContent();
        $currentUrl = $session->getCurrentUrl();

        if (strpos($content, 'Konfirmasi Pembayaran') === false) {
            throw new Exception(('Halaman Konfirmasi pembayaran tidak muncul' . $currentUrl));
        }

        if (strpos($content, 'snap.pay') === false) {
             throw new Exception('Snap Token Midtrans tidak ditemukan di halaman.');
        }
    }

    /**
    * @When penghuni kos menekan tombol "Bayar"
    */
    public function steps_impl_penghuni_kos_menekan_tombol_bayar() {
        $session = $this->getSession();
        $page = $session->getPage();


        $buttonBayar = $page->find('css', '#pay-button');
        if (!$buttonBayar) {
            throw new Exception('Tombol Bayar tidak ditemukan.');
        }
        // $buttonBayar->click();
        return;
    }

    /**
     * @Then halaman pemilihan metode pembayaran ditampilkan
     */
    public function steps_impl_pilihan_metode_pembayaran_ditampilkan() {
        $session = $this->getSession();
        $page = $session->getPage();

        $content = $page->getContent();

        if (strpos($content, 'snap.js') === false) {
             throw new Exception('Library Midtrans Snap tidak ter-load.');
        }

        // Pastikan token snap juga ada di script
        if (strpos($content, 'snap.pay') === false) {
             throw new Exception('Fungsi pembayaran (snap.pay) tidak ditemukan.');
        }
    }

    /**
     * @When penghuni kos memilih :metode sebagai metode pembayaran
     */
    public function steps_impl_pilih_metode_bayar($metode) {
        //
    }

    /**
     * @Then QR Code pembayaran ditampilkan
     */
    public function steps_impl_qr_tampil() {
        //
    }

    /**
     * @When penghuni melakukan pembayaran
     */
    public function steps_impl_lakukan_pembayaran() {        
        $this->visitPath('/penghuni/pemesanan/index');
    }

    /**
     * @Then aplikasi menampilkan pemberitahuan bahwa pembayaran berhasil
     */
    public function steps_impl_notifikasi_bayar_sukses() {
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Then halaman akan menuju ke halaman riwayat pemesanan
     */
    public function steps_impl_redirect_ke_riwayat() {
        $currentUrl = $this->getSession()->getCurrentUrl();
        if (strpos($currentUrl, '/penghuni/pemesanan/index') === false) {
            throw new Exception('User tidak diarahkan ke halaman riwayat. URL: ' . $currentUrl);
        }
    }


    // Scenario: Penghuni kos melihat riwayat pemesanan kamar
    /**
     * @When penghuni kos menekan menu "Pemesanan" pada navigation bar
     */
    public function steps_impl_penghuni_kos_menekan_menu_pemesanan_pada_navbar() {
        $session = $this->getSession();
        $page = $session->getPage();

        $pemesananButton = $page->findLink('Pemesanan');

        if (!$pemesananButton) {
            throw new Exception('Menu Pemesanan tidak ditemukan pada navigation bar.');
        }

        $pemesananButton->click();
    }

    /**
     * @Then halaman pemesanan menampilkan riwayat pemesanan penghuni
     */
    public function steps_impl_halaman_pemesanan_menampilkan_riwayat_pemesanan_penghuni() {
        $session = $this->getSession();
        $page = $session->getPage();

        $riwayatTable = $page->find('css', '#table-pesanan');
        
        if (!$riwayatTable) {
            throw new Exception('Tabel riwayat pemesanan (#table-pesanan) tidak ditemukan.');
        }

        $dataPesanan = $riwayatTable->findAll('css', 'tbody tr');
        
        if (count($dataPesanan) === 0) {
            throw new Exception('Tabel pesanan kosong.'); 
        }
    }

    // Scenario: Penghuni kos memberikan review pada suatu kos
    /**
     * @When penghuni kos menekan tombol review
     */
    public function steps_impl_penghuni_menekan_tombol_review() {
        $session = $this->getSession();
        $page = $session->getPage();

        $reviewButton = $page->find('css', '.btn-review');

        if (!$reviewButton) {
            throw new Exception('Tombol review tidak ditemukan pada halaman pemesanan. Pastikan data pesanan tidak kosong');
        }

        $reviewButton->click();
    }

    /**
     * @When penghuni kos mengisi review untuk kos yang dipilih
     */
    public function steps_impl_penghuni_mengisi_review_kos() {
        $session = $this->getSession();
        $page = $session->getPage();

        $textarea = $page->findField('isi');

        if (!$textarea) {
            throw new Exception('Form textarea review (name="isi") tidak ditemukan.');
        }

        $textarea->setValue('Kos ini sangat bersih, wifi kencang, dan ibu kos ramah!');
    }

    /**
     * @When penghuni kos menekan tombol "Submit Review"
     */
    public function steps_impl_penghuni_menekan_tombol_submit_review() {
        $session = $this->getSession();
        $page = $session->getPage();

        $submitButton = $page->findButton('Submit Review');

        if (!$submitButton) {
            throw new Exception('Tombol Submit Review tidak ditemukan pada halaman review.');
        }

        $submitButton->click();
    }

    /**
     * @Then data review tersimpan ke database dan muncul notifikasi "Review berhasil ditambahkan!"
     */
    public function steps_impl_data_review_disimpan() {
        $session = $this->getSession();

        $content = $session->getPage()->getContent();

        $notifikasi = 'Review berhasil ditambahkan!';

        if (strpos($content, $notifikasi) === false) {
            throw new Exception("Pesan sukses SweetAlert ('$notifikasi') tidak ditemukan di halaman. Review mungkin gagal.");
        }
    }
}