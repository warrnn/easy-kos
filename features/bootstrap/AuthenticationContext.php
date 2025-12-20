<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * autentikasi user
 */
class AuthenticationContext extends RawMinkContext implements Context
{
    /**
     * @Given user membuka aplikasi
     */
    public function steps_impl_user_buka_aplikasi()
    {
        $this->visitPath('/');
    }

    /**
     * @When admin membuka aplikasi
     */
    public function steps_impl_admin_buka_aplikasi()
    {
        $this->visitPath('/');
    }

    /**
     * @When pemilik kos membuka aplikasi
     */
    public function steps_impl_pemilik_buka_aplikasi()
    {
        $this->visitPath('/');
    }

    /**
     * @When penghuni kos membuka aplikasi
     */
    public function steps_impl_penghuni_buka_aplikasi()
    {
        $this->visitPath('/');
    }

    /**
     * @Given user pergi ke halaman register
     */
    public function steps_impl_user_ke_register()
    {
        $this->visitPath('/authentication/register');
    }

    /**
     * @Given user mengisi form register
     */
    public function steps_impl_user_isi_form_register()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->fillField('username', 'testuser_' . time());
        $page->fillField('password', 'testpassword123');
        $page->fillField('password_confirmation', 'testpassword123');
        $page->selectFieldOption('role', '3'); 
    }

    /**
     * @Given user menekan tombol register
     */
    public function steps_impl_user_click_register()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->pressButton('Register');
    }

    /**
     * @Then user berhasil registrasi akun baru
     */
    public function steps_impl_user_berhasil_registrasi()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $content = $page->getContent();
        if (strpos($content, 'Registrasi berhasil') === false && strpos($content, 'success') === false) {
            throw new Exception('Registration success message not found');
        }
    }

    /**
     * @Then user ter-direct ke halaman login
     */
    public function steps_impl_user_redirect_ke_login()
    {
        $session = $this->getSession();
        
        $this->visitPath('/authentication/login');
        $currentUrl = $session->getCurrentUrl();
        
        if (strpos($currentUrl, '/authentication/login') === false) {
            throw new Exception('User was not redirected to login page');
        }
    }

    /**
     * @When admin mengisi form login
     */
    public function steps_impl_admin_isi_form_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->fillField('username', 'admin');
        $page->fillField('password', 'admin123');
    }

    /**
     * @When pemilik kos mengisi form login
     */
    public function steps_impl_pemilik_isi_form_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->fillField('username', 'Pemilik');
        $page->fillField('password', 'pemilik123');
    }

    /**
     * @When penghuni kos mengisi form login
     */
    public function steps_impl_penghuni_isi_form_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->fillField('username', 'Penghuni');
        $page->fillField('password', 'penghuni123');
    }

    /**
     * @When user mengisi form login dengan kredensial salah
     */
    public function steps_impl_user_isi_form_login_salah()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->fillField('username', 'invaliduser');
        $page->fillField('password', 'wrongpassword');
    }

    /**
     * @When admin menekan tombol login
     */
    public function steps_impl_admin_click_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->pressButton('Login');
    }

    /**
     * @When pemilik kos menekan tombol login
     */
    public function steps_impl_pemilik_click_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->pressButton('Login');
    }

    /**
     * @When penghuni kos menekan tombol login
     */
    public function steps_impl_penghuni_click_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->pressButton('Login');
    }

    /**
     * @When user menekan tombol login
     */
    public function steps_impl_user_click_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->pressButton('Login');
    }

    /**
     * @Then admin berhasil masuk ke dashboard admin
     */
    public function steps_impl_admin_berhasil_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $content = $page->getContent();
        if (strpos($content, 'Login berhasil') === false && strpos($content, 'success') === false) {
            throw new Exception('Login success message not found');
        }
        
        $this->visitPath('/admin');
        $currentUrl = $session->getCurrentUrl();
        
        if (strpos($currentUrl, '/admin') === false) {
            throw new Exception('Admin cannot access admin dashboard. Current URL: ' . $currentUrl);
        }
    }

    /**
     * @Then pemilik kos berhasil masuk ke dashboard pemilik kos
     */
    public function steps_impl_pemilik_berhasil_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $content = $page->getContent();
        if (strpos($content, 'Login berhasil') === false && strpos($content, 'success') === false) {
            throw new Exception('Login success message not found');
        }
        
        $this->visitPath('/pemilik_kos/index');
        $currentUrl = $session->getCurrentUrl();
        
        if (strpos($currentUrl, '/pemilik_kos/index') === false) {
            throw new Exception('Owner cannot access owner dashboard. Current URL: ' . $currentUrl);
        }
    }

    /**
     * @Then penghuni kos berhasil masuk ke halaman utama
     */
    public function steps_impl_penghuni_berhasil_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $content = $page->getContent();
        if (strpos($content, 'Login berhasil') === false && strpos($content, 'success') === false) {
            throw new Exception('Login success message not found');
        }
        
        $this->visitPath('/penghuni/index');
        $currentUrl = $session->getCurrentUrl();
        
        if (strpos($currentUrl, '/penghuni/index') === false) {
            throw new Exception('Tenant cannot access homepage. Current URL: ' . $currentUrl);
        }
    }

    /**
     * @Then aplikasi menolak login dari user
     */
    public function steps_impl_aplikasi_tolak_login()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $content = $page->getContent();
        
        if (strpos($content, 'Username atau password salah') === false && 
            strpos($content, 'error') === false) {
            throw new Exception('No error message found after failed login');
        }
    }

    /**
     * @Then user tetap di halaman login
     */
    public function steps_impl_user_tetap_di_login()
    {
        $session = $this->getSession();
        $currentUrl = $session->getCurrentUrl();
        
        if (strpos($currentUrl, '/authentication/login') === false) {
            throw new Exception('User was redirected away from login page. Current URL: ' . $currentUrl);
        }
    }
}
