<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ShopLoginTest extends DuskTestCase
{
    public function test_shop_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->assertPresent('form.login-form')
                    ->type('username', 'amal_admin')
                    ->type('password', 'pass123!')
                    ->click('button#login-btn')
                    ->pause(4000)
                    ->assertPathIs('/pharmacy/1/dashboard'); // use actual shop_id
        });
    }

    public function test_shop_login_wrong_password()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->assertPresent('form.login-form')
                    ->type('username', 'amal_admin')
                    ->type('password', 'wrongpass')
                    ->click('button#login-btn')
                    ->pause(2500)
                    ->assertSeeIn('div.error ul li', 'Incorrect password'); // updated selector
        });
}


    public function test_shop_login_user_not_found()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->assertPresent('form.login-form')
                    ->type('username', 'nonexistent_user')
                    ->type('password', 'any_password')
                    ->click('button#login-btn')
                    ->pause(2000)
                    ->assertSeeIn('div.error', 'No user found with this username');
        });
    }
}
