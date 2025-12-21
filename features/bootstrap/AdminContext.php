<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Step\When;
use Behat\Step\Then;

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
}