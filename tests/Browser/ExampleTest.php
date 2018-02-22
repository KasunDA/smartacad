<?php

namespace Tests\Browser;

use App\Models\Admin\Users\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{
    //use DatabaseMigrations;
    
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/')
//                    ->assertSee('Laravel');
//        });

//        $this->assertDatabaseHas('users', [
//            'email' => 'ekaruztest@gmail.com'
//        ]);
        
        $user = factory(User::class)->create([
//            'email' => 'ekaruztest2@gmail.com',
//            'first_name' => 'Joker',
//            'last_name' => 'Douche',
        ]);

        $user = factory(User::class, 1)->make();

        //dd($user[0]);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                ->type('login', $user[0]->email)
                ->type('password', 'password')
                ->press('Sign In')
                ->assertPathIs('/');
        });
    }
}
