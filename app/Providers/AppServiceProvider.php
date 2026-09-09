<?php

namespace App\Providers;

use Akh\Typograf\Typograf;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Typograf::class, function (): Typograf {
            $typograf = new Typograf;
            // Keep output plain-text safe for Blade {{ }} (no <sup>/<sub>).
            $typograf->disableRule('Number\Sup');
            $typograf->disableRule('Number\Sub');
            $typograf->disableRule('Number\DimensionSup');

            return $typograf;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('typo', function (string $expression): string {
            return "<?php echo e(\\App\\Support\\Typograph::apply($expression)); ?>";
        });
    }
}
