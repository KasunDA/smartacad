<?php

namespace App\Providers;

use App\Models\Admin\Menus\Menu;
use App\Models\School\School;
use Hashids\Hashids;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Preload the Menu Level One
        if(Schema::hasTable('menus')){
            $menus = Menu::roots()->where('active', 1)->where('type', 1)->get();
            $active_home_menu = Menu::roots()->where('active', 1)->where('type', 2)->get();
            view()->share('active_menus', $menus);
            view()->share('active_home_menu', $active_home_menu);
        }
        view()->share('school_name', 'Solid Step International School');
        
        //Set The School Info. into a variable school
        if(env('SCHOOL_ID') && Schema::connection('admin_mysql')->hasTable('schools') && School::count() > 0){
            $school = School::findOrFail(env('SCHOOL_ID'));
            view()->share('mySchool', $school);
        }
//      Set The HashIds Secret Key, Length and Possible Characters Combinations To Be Accessible to every View
        view()->share('hashIds', new Hashids(env('APP_KEY'), 20, env('APP_CHAR')));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
