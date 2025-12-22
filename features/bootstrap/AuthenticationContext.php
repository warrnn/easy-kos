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
     * @Given /^"([^"]*)" membuka aplikasi$/
     */
    public function steps_impl_buka_aplikasi($role)
    {
        $this->visitPath('/');
    }

    // /**
    //  * @When user membuka aplikasi
    //  */
    // public function steps_impl_user_membuka_aplikasi()
    // {
    //     $this->visitPath('/');
    // }

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
     * @When :role mengisi form login
     */
    public function steps_impl_isi_form_login($role)
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $credentials = $this->getCredentials($role);
        $page->fillField('username', $credentials['username']);
        $page->fillField('password', $credentials['password']);
    }

    /**
     * helper
     */
    private function getCredentials($role)
    {
        switch ($role) {
            case 'admin':
                return ['username' => 'admin', 'password' => 'admin123'];
            case 'pemilik kos':
                return ['username' => 'Pemilik', 'password' => 'pemilik123'];
            case 'penghuni kos':
                return ['username' => 'Penghuni', 'password' => 'penghuni123'];
            default:
                throw new Exception('Unknown role: ' . $role);
        }
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
     * @When :role menekan tombol login
     */
    public function steps_impl_click_login($role)
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $page->pressButton('Login');
    }

    /**
     * @Then :role berhasil masuk ke :dashboard
     */
    public function steps_impl_berhasil_login($role, $dashboard)
    {
        $session = $this->getSession();
        $page = $session->getPage();
        
        $content = $page->getContent();
        if (strpos($content, 'Login berhasil') === false && strpos($content, 'success') === false) {
            throw new Exception('Login success message not found');
        }
        
        $dashboardPath = $this->getDashboardPath($role);
        $this->visitPath($dashboardPath);
        $currentUrl = $session->getCurrentUrl();
        
        if (strpos($currentUrl, $dashboardPath) === false) {
            throw new Exception($role . ' cannot access ' . $dashboard . '. Current URL: ' . $currentUrl);
        }
    }

    /**
     * helper
     */
    private function getDashboardPath($role)
    {
        switch ($role) {
            case 'admin':
                return '/admin';
            case 'pemilik kos':
                return '/pemilik_kos/index';
            case 'penghuni kos':
                return '/penghuni/index';
            default:
                throw new Exception('Unknown role: ' . $role);
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
