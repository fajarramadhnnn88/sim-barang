<?php
namespace App\Providers;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    public function register():void {
        $this->app->singleton(\App\Services\ApprovalService::class);
        $this->app->singleton(\App\Services\PengajuanService::class);
        $this->app->singleton(\App\Services\StockService::class);
    }
    public function boot():void {
        Blade::directive('canRole',function($roles){
            return "<?php if(auth()->check()&&(auth()->user()->role->value==='superadmin'||in_array(auth()->user()->role->value,$roles))): ?>";
        });
        Blade::directive('endCanRole',function(){return "<?php endif; ?>";});
    }
}
