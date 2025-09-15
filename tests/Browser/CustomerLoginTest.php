<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CustomerLoginTest extends DuskTestCase
{
    /**
     * Test successful customer login.
     *
     * @return void
     */
    public function test_customer_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertPresent('form.login-form') // check if form exists
                ->type('username', 'user_sep1')
                ->type('password', 'pass333!')
                ->click('#login-btn') 
                ->pause(4000)
                ->assertPathIs('/dashboard');
        });
    }

    /**
     * Test wrong password for customer login.
     *
     * @return void
     */
    public function test_customer_login_wrong_password()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertPresent('form.login-form')
                ->type('username', 'user_sep1')
                ->type('password', 'wrongpass')
                ->click('#login-btn')
                ->pause(2500)
                ->assertSeeIn('div.error ul li', 'Incorrect password'); // updated selector
        });
    }

    /**
     * Test login with non-existent customer username.
     *
     * @return void
     */
    public function test_customer_login_user_not_found()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertPresent('form.login-form')
                ->type('username', 'nonexistent_user')
                ->type('password', 'any_password')
                ->click('#login-btn')
                ->pause(2500)
                ->assertSeeIn('div.error ul li', 'No user found with this username'); // controller message
        });
    }
}
