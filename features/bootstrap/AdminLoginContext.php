<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use PHPUnit\Framework\Assert;

/**
 * Context untuk testing admin login
 */
class AdminLoginContext extends MinkContext implements Context
{
    /**
     * @Given saya berada di halaman login
     */
    public function sayaBeradaDiHalamanLogin()
    {
        $this->visitPath('/authentication/login');
    }

    /**
     * @When saya mengisi username dengan :username
     */
    public function sayaMengisiUsernameDengan($username)
    {
        $this->fillField('username', $username);
    }

    /**
     * @When saya mengisi password dengan :password
     */
    public function sayaMengisiPasswordDengan($password)
    {
        $this->fillField('password', $password);
    }

    /**
     * @When saya menekan tombol login
     */
    public function sayaMenekanTombolLogin()
    {
        $this->pressButton('Login');
    }

    /**
     * @Then saya harus berada di halaman dashboard admin
     */
    public function sayaHarusBeradaDiHalamanDashboardAdmin()
    {
        $currentUrl = $this->getSession()->getCurrentUrl();
        Assert::assertStringContainsString('/admin', $currentUrl, 
            "Expected to be on admin dashboard, but current URL is: {$currentUrl}");
    }

    /**
     * @Then saya harus melihat teks :text
     */
    public function sayaHarusMelihatTeks($text)
    {
        $this->assertPageContainsText($text);
    }
}
