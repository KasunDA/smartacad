<?php

namespace App\Providers;

use App\Models\Admin\Menus\MenuHeader;
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
        if(Schema::hasTable('menu_headers')){
            $headers = MenuHeader::where('active', 1)->orderBy('sequence')->get();
            $active_headers = array();
            foreach($headers as $active_header){
                if($active_header->menus()->count() !== 0){
                    $active_headers[] = $active_header;
                }
            }
            view()->share('active_headers', $active_headers);
        }
//      Set The HashIds Secret Key, Length and Possible Characters Combinations To Be Accessible to every View
        view()->share('hashIds', new Hashids(env('APP_KEY'), 15, env('APP_CHAR')));
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
