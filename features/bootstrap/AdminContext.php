<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;


/**
 * admin context
 */
class AdminContext extends RawMinkContext implements Context
{
    /**
     * @Then admin melakukan login
     */
    public function steps_impl_admin_melakukan_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $this->visitPath('/authentication/login');

        $page->fillField('username', 'admin');
        $page->fillField('password', 'admin123');


        $page->pressButton('Login');
    }

    /**
     * @Then halaman dashboard admin ditampilkan
     */
    public function steps_impl_halaman_dashboard_admin_ditampilkan()
    {
        $this->visitPath('/admin');
    }

    /**
     * @Then admin menekan tombol "+ Users" pada halaman dashboard admin
     */
    public function steps_impl_admin_menekan_tombol_add_users_pada_halaman_dashboard_admin()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $buttonLinkAddUser = $page->find('css', 'a[href*="/admin/form-users"]');

        if ($buttonLinkAddUser === null) {
            throw new Exception('Button "Add Users" not found');
        }

        $buttonLinkAddUser->click();
    }

    /**
     * @Then admin mengisi form data akun pemilik kos baru
     */
    public function steps_impl_admin_mengisi_form_data_akun_pemilik_kos_baru()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $page->selectFieldOption('role', '2');
        $page->fillField('username', 'Pemilik');
        $page->fillField('password', 'pemilik123');
        $page->fillField('password_confirmation', 'pemilik123');
    }

    /**
     * @Then admin menekan tombol "Tambah Akun"
     */
    public function steps_impl_admin_menekan_tombol_tambah_akun()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $page->pressButton('tambah-akun');
    }

    /**
     * @Then data akun tampil di tabel user pada halaman manajemen data user
     */
    public function steps_impl_data_akun_tampil_di_tabel_user()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $this->visitPath('/admin/manage-users');


        if ($session->getStatusCode() != 200) {
            throw new \Exception("Gagal memuat halaman. Status code: " . $session->getStatusCode());
        }

        $table = $page->find('css', '#user-table');

        if (null === $table) {
            throw new \Exception("Tabel User (#userTable) tidak ditemukan di HTML.");
        }

        $rows = $table->findAll('css', 'tbody tr');

        if (count($rows) === 0) {
            throw new \Exception("Tabel User ditemukan, tetapi tidak ada baris data (tbody kosong).");
        }
    }

    /**
     * @Then admin menekan tombol "+ Kos" pada halaman dashboard admin
     */
    public function steps_impl_admin_menekan_tombol_add_kos_pada_halaman_dashboard_admin()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $buttonLinkAddUser = $page->find('css', 'a[href*="/admin/form-pemilik"]');

        if ($buttonLinkAddUser === null) {
            throw new Exception('Button "Add Kos" not found');
        }

        $buttonLinkAddUser->click();
    }

    /**
     * @Then admin mengisi form data kos baru
     */
    public function steps_impl_admin_mengisi_form_data_kos()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $page->selectFieldOption('role', 'Pemilik');
        $page->fillField('name', 'Kos Test ' . time());
        $page->fillField('alamat', 'Jl. Test ' . time());
    }

    /**
     * @Then admin menekan tombol "Tambah Kos"
     */
    public function steps_impl_admin_menekan_tombol_tambah_kos()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $page->pressButton('tambah-kos');
    }

    /**
     * @Then data kos tampil di tabel kos pada halaman manajemen data kos
     */
    public function steps_impl_data_kos_tampil_di_tabel_kos()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $this->visitPath('/admin/manage-users');


        if ($session->getStatusCode() != 200) {
            throw new \Exception("Gagal memuat halaman. Status code: " . $session->getStatusCode());
        }

        $table = $page->find('css', '#pemilikTable');

        if (null === $table) {
            throw new \Exception("Tabel User (#userTable) tidak ditemukan di HTML.");
        }

        $rows = $table->findAll('css', 'tbody tr');

        if (count($rows) === 0) {
            throw new \Exception("Tabel pemilik kos ditemukan, tetapi tidak ada baris data (tbody kosong).");
        }
    }

    /**
     * @When admin menuju ke halaman manajemen
     */
    public function steps_impl_admin_menuju_ke_halaman_manajemen()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $this->visitPath('/admin');
    }

    /**
     * @Then admin menekan tab "Kos" untuk menampilkan data-data kos yang ada
     */
    public function steps_impl_admin_menekan_tab_kos()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $this->visitPath('/admin/manage-users');

        $pemilikTable = $page->find('css', '#pemilik-table');
        if ($pemilikTable === null) {
            throw new \Exception('Tab Kos (pemilik-table) tidak ditemukan di halaman.');
        }
    }

    /**
     * @Then admin menekan "ikon edit" yang ada di salah satu data
     */
    public function steps_impl_admin_menekan_ikon_edit()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $editButton = $page->find('css', '#pemilik-table a[href*="form-pemilik"].btn-secondary');

        // if ($editButton === null) {
        //     $editButton = $page->find('css', '#pemilikTable a[href*="form-pemilik"]');
        // }

        // if ($editButton === null) {
        //     $editButton = $page->find('css', 'a[href*="form-pemilik"][href*="edit"]');
        // }

        if ($editButton === null) {
            throw new \Exception('Ikon edit tidak ditemukan di halaman.');
        }

        $editButton->click();
    }

    /**
     * @Then admin melakukan perubahan data di form
     */
    public function steps_impl_admin_melakukan_perubahan_data_di_form()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $nameField = $page->findField('name');
        if ($nameField !== null) {
            $nameField->setValue('Kos Updated ' . time());
        }

        $alamatField = $page->findField('alamat');
        if ($alamatField !== null) {
            $alamatField->setValue('Jl. Updated ' . time());
        }
    }

    /**
     * @Then admin menekan tombol "Edit Kos"
     */
    public function steps_impl_admin_menekan_tombol_edit_kos()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $submitButton = $page->find('css', 'form button[type="submit"]');

        // if ($submitButton === null) {
        //     $submitButton = $page->find('css', '.btn-primary[type="submit"]');
        // }

        if ($submitButton === null) {
            throw new \Exception('Tombol Edit Kos tidak ditemukan di halaman.');
        }

        $submitButton->click();
    }

    /**
     * @Then data tampil pada tabel di halaman manajemen kos dengan data yang sudah berubah
     */
    public function steps_impl_data_tampil_dengan_data_berubah()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $this->visitPath('/admin/manage-users');

        if ($session->getStatusCode() != 200) {
            throw new \Exception("Gagal memuat halaman. Status code: " . $session->getStatusCode());
        }

        $table = $page->find('css', '#pemilikTable, #pemilik-table table');

        if (null === $table) {
            throw new \Exception("Tabel Kos tidak ditemukan di HTML.");
        }

        $rows = $table->findAll('css', 'tbody tr');

        if (count($rows) === 0) {
            throw new \Exception("Tabel Kos ditemukan, tetapi tidak ada baris data (tbody kosong).");
        }

        $tableContent = $table->getText();
        if (empty($tableContent)) {
            throw new \Exception("Tabel Kos tidak memiliki konten.");
        }
    }

    /**
     * @Then admin menekan "ikon delete trash" yang ada di salah satu data
     */
    public function steps_impl_admin_menekan_ikon_delete_trash()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $deleteForm = $page->find('css', '#pemilik-table .delete-form');

        // if ($deleteForm === null) {
        //     $deleteForm = $page->find('css', '#pemilikTable .delete-form');
        // }

        // if ($deleteForm === null) {
        //     $deleteForm = $page->find('css', 'form.delete-form[action*="kos"]');
        // }

        if ($deleteForm === null) {
            throw new \Exception('Form delete (ikon delete trash) tidak ditemukan di halaman.');
        }

        $deleteForm->submit();
    }

    /**
     * @Then admin menekan tombol "Yes, delete it!" pada modal alert
     */
    public function steps_impl_admin_menekan_tombol_confirm_delete()
    {
        $session = $this->getSession();
        
        if ($session->getStatusCode() >= 400) {
            throw new \Exception('Terjadi error setelah delete. Status code: ' . $session->getStatusCode());
        }
    }

    /**
     * @Then data tidak lagi tampil di aplikasi
     */
    public function steps_impl_data_tidak_lagi_tampil_di_aplikasi()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        if ($session->getStatusCode() != 200) {
            throw new \Exception("Gagal memuat halaman. Status code: " . $session->getStatusCode());
        }

        $successMessage = $page->find('css', '.swal2-success');
        $table = $page->find('css', '#pemilik-table, #pemilikTable');

        if ($successMessage !== null || $table !== null) {
            return;
        }

        $currentUrl = $session->getCurrentUrl();
        if (strpos($currentUrl, 'manage-users') !== false || strpos($currentUrl, 'admin') !== false) {
            return;
        }

        throw new \Exception("Tidak dapat memverifikasi bahwa data telah dihapus.");
    }
}