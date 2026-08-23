<?php

namespace App\Providers;

use App\Models\Menu;
use App\Services\Ai\AiService;
use App\Services\Payment\PaymentManager;
use App\Services\Plugin\HookManager;
use App\Services\PermalinkService;
use App\Services\Seo\SeoManager;
use App\Services\SettingsRepository;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Wires the CMS "engine": core singletons (settings, permalinks, SEO, hooks,
 * AI, payments), Blade helpers, and the data shared into every themed view.
 */
class CmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('barta.settings', fn () => new SettingsRepository());
        $this->app->singleton('barta.permalink', fn () => new PermalinkService());
        $this->app->singleton('barta.seo', fn () => new SeoManager());
        $this->app->singleton('barta.hooks', fn () => new HookManager());
        $this->app->singleton(AiService::class, fn () => new AiService());
        $this->app->singleton(PaymentManager::class, fn () => new PaymentManager());

        $this->app->alias('barta.settings', SettingsRepository::class);
        $this->app->alias('barta.permalink', PermalinkService::class);
        $this->app->alias('barta.seo', SeoManager::class);
        $this->app->alias('barta.hooks', HookManager::class);
    }

    public function boot(): void
    {
        $this->registerBladeDirectives();
        $this->shareThemeData();
    }

    protected function registerBladeDirectives(): void
    {
        Blade::directive('setting', fn ($expr) => "<?php echo e(setting({$expr})); ?>");
        Blade::directive('money', fn ($expr) => "<?php echo e(money({$expr})); ?>");
        Blade::directive('bn', fn ($expr) => "<?php echo e(to_bn_number({$expr})); ?>");

        // @doAction('hook', $arg) — fire a plugin/theme action from a template.
        Blade::directive('doAction', fn ($expr) => "<?php app('barta.hooks')->doAction({$expr}); ?>");
    }

    /**
     * Make site settings and navigation menus available to theme/layout views
     * without every controller having to pass them. Resilient before migration.
     */
    protected function shareThemeData(): void
    {
        View::composer(['theme::*', 'layouts.*', 'components.*'], function ($view) {
            $menus = [];

            try {
                if (Schema::hasTable('menus')) {
                    $menus = Menu::with('items')->get()->keyBy('location');
                }
            } catch (\Throwable) {
                // Database not ready yet (e.g. during install) — degrade quietly.
            }

            $view->with([
                'siteSettings' => app('barta.settings')->all(),
                'siteMenus' => $menus,
            ]);
        });
    }
}
