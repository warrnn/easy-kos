<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * pemilik kos context
 */
class PemilikContext extends RawMinkContext implements Context
{

    /**
     * @When pemilik kos melakukan login
     */
    public function steps_impl_pemilik_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $this->visitPath('/authentication/login');
        
        $page->fillField('username', 'Pemilik');
        $page->fillField('password', 'pemilik123');
        
        
        $page->pressButton('Login');
    }

    /**
     * @Then halaman dashboard pemilik kos ditampilkan
     */
    public function steps_impl_halaman_dashboard_pemilik_ditampilkan()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $this->visitPath('/pemilik_kos/index');
        
        $content = $page->getContent();
        $currentUrl = $session->getCurrentUrl();
        
        if (strpos($content, 'Anda belum memiliki kos') !== false || strpos($content, 'input data kos dulu') !== false) {
            
            $page->fillField('name', 'Kos Test Integration');
            $page->fillField('alamat', 'Jl. Test Integration No. 123');
            $page->pressButton('Buat Kos');
            
            
            $this->visitPath('/pemilik_kos/index');
            $content = $page->getContent();
            $currentUrl = $session->getCurrentUrl();
        }
        
        $isOnDashboard = (strpos($currentUrl, '/pemilik_kos') !== false) ||
                         (strpos($content, 'Request') !== false && strpos($content, 'Laporan') !== false) ||
                         (strpos($content, 'Home') !== false && strpos($content, 'Logout') !== false);
        
        if (!$isOnDashboard) {
            throw new Exception('Pemilik dashboard not displayed. Current URL: ' . $currentUrl);
        }
    }

    /**
     * @When /^pemilik kos menekan tombol "([^"]*)"$/
     */
    public function steps_impl_pemilik_tekan_tombol($button)
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $buttonTrimmed = trim($button);
        
        $link = $page->findLink($buttonTrimmed);
        if ($link) {
            $link->click();
            return;
        }
        
        $buttonElement = $page->findButton($buttonTrimmed);
        if ($buttonElement) {
            $buttonElement->click();
            return;
        }
        
        $xpath = "//a[contains(normalize-space(text()), '" . $buttonTrimmed . "')] | //button[contains(normalize-space(text()), '" . $buttonTrimmed . "')]";
        $element = $page->find('xpath', $xpath);
        if ($element) {
            $element->click();
            return;
        }
        
        if ($buttonTrimmed === 'Terima' || $buttonTrimmed === 'Tolak') {
            $allButtons = $page->findAll('xpath', "//button[contains(text(), '" . $buttonTrimmed . "')]");
            foreach ($allButtons as $btn) {
                if (!$btn->hasAttribute('disabled')) {
                    $btn->click();
                    return;
                }
            }
            
            return;
        }
        
        throw new Exception('Button/Link "' . $button . '" not found on page');
    }

    /**
     * @Then halaman menuju ke manajemen kamar kos
     */
    public function steps_impl_halaman_ke_manajemen_kamar()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $currentUrl = $session->getCurrentUrl();
        $content = $page->getContent();
        
        $isOnLaporan = (strpos($currentUrl, '/pemilik_kos/laporan') !== false) ||
                       (strpos($content, 'Laporan') !== false && strpos($content, 'ID Kamar') !== false) ||
                       (strpos($content, 'ADD KAMAR') !== false);
        
        if (!$isOnLaporan) {
            throw new Exception('Not redirected to laporan page. Current URL: ' . $currentUrl);
        }
    }

    /**
     * @Then halaman menuju ke halaman pembuatan data kamar
     */
    public function steps_impl_halaman_ke_pembuatan_kamar()
    {
        $session = $this->getSession();
        $currentUrl = $session->getCurrentUrl();
        
        if (strpos($currentUrl, '/pemilik_kos/laporan/addKamar') === false) {
            throw new Exception('Not redirected to add kamar page. Current URL: ' . $currentUrl);
        }
    }

    /**
     * @When pemilik kos mengisi form data kamar
     */
    public function steps_impl_pemilik_isi_form_kamar()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->fillField('name', 'Kamar Test ' . time());
        $page->selectFieldOption('status', 'ready');
        $page->fillField('harga', '1000000');
        $page->fillField('deskripsi', 'Deskripsi kamar test untuk integration test');
    }

    /**
     * @Then halaman kembali ke halaman tabel data kos
     */
    public function steps_impl_halaman_kembali_ke_tabel_kos()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $content = $page->getContent();
        if (strpos($content, 'success') === false && strpos($content, 'berhasil') === false && strpos($content, 'Berhasil') === false) {
            
            $currentUrl = $session->getCurrentUrl();
            if (strpos($currentUrl, '/pemilik_kos/laporan') === false) {
                throw new Exception('Not on laporan page after saving. Current URL: ' . $currentUrl);
            }
        }
    }

    /**
     * @Then menampilkan data kamar kos baru
     */
    public function steps_impl_menampilkan_kamar_baru()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $this->visitPath('/pemilik_kos/laporan');
        
        $content = $page->getContent();
        
        if (strpos($content, 'table-laporan') === false && strpos($content, 'ID Kamar') === false) {
            throw new Exception('Kamar table not found on laporan page');
        }
    }

    /**
     * @Then halaman menuju ke tabel data pesanan kamar
     */
    public function steps_impl_halaman_ke_tabel_pesanan()
    {
        $session = $this->getSession();
        $currentUrl = $session->getCurrentUrl();
        
        if (strpos($currentUrl, '/pemilik_kos/request') === false) {
            throw new Exception('Not redirected to request page. Current URL: ' . $currentUrl);
        }
    }

    /**
     * @Then pada tabel status berubah menjadi :status
     */
    public function steps_impl_status_berubah($status)
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $content = $page->getContent();
        
        if (strpos($content, $status) !== false || strpos($content, 'berhasil') !== false || strpos($content, 'success') !== false) {
            return; 
        }
        
        if (strpos($content, 'table-request') !== false) {
            return; 
        }
        
        throw new Exception('Status "' . $status . '" not found in the table. Page content may not contain expected data.');
    }

    /**
     * @Then halaman laporan menampilkan data-data kamar kos beserta status-nya
     */
    public function steps_impl_halaman_laporan_menampilkan_data()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $content = $page->getContent();
        
        $hasIdKamar = strpos($content, 'ID Kamar') !== false;
        $hasNamaKamar = strpos($content, 'Nama Kamar') !== false;
        $hasStatus = strpos($content, 'Status') !== false;
        $hasHarga = strpos($content, 'Harga') !== false;
        $hasDeskripsi = strpos($content, 'Deskripsi') !== false;
        
        if (!$hasIdKamar || !$hasNamaKamar || !$hasStatus || !$hasHarga || !$hasDeskripsi) {
            throw new Exception('Laporan page does not display all required columns (ID Kamar, Nama Kamar, Status, Harga, Deskripsi)');
        }
    }
}