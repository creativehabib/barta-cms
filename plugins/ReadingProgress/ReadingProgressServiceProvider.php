<?php

namespace Plugins\ReadingProgress;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

/**
 * Reading Progress — a reference plugin for Barta CMS.
 *
 * It demonstrates the three things a real plugin needs:
 *   1. Discovery   — a plugin.json manifest at the folder root (handled by PluginManager).
 *   2. Autoloading — this class is reachable via the "Plugins\\" PSR-4 prefix
 *                    declared in composer.json (Plugins\ => plugins/).
 *   3. Hooks       — it registers action callbacks against the core HookManager
 *                    (resolved as "barta.hooks") so its behaviour is injected into
 *                    the active theme without touching theme files.
 *
 * Front-end effect: a scroll progress bar pinned to the top of the viewport and a
 * "back to top" button. Back-end effect: a log line each time a comment is created.
 */
class ReadingProgressServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // The HookManager singleton. Guard in case the plugin is loaded in a
        // context where the CMS core has not registered its bindings.
        if (! $this->app->bound('barta.hooks')) {
            return;
        }

        $hooks = $this->app->make('barta.hooks');

        // (1) ACTION — inject styles into the theme's <head>.
        //     The bar colour is passed through a filter so other plugins (or a
        //     child theme) can override it: Hook::addFilter('reading_progress.color', …).
        $hooks->addAction('theme.head', function () use ($hooks) {
            $color = (string) $hooks->applyFilters('reading_progress.color', '#c81420');
            echo $this->styles($color);
        });

        // (2) ACTION — inject the markup + behaviour just before </body>.
        $hooks->addAction('theme.footer', function () {
            echo $this->markup();
        });

        // (3) ACTION — a back-end side effect: log every new comment. Shows that
        //     plugins can react to core domain events, not just template hooks.
        $hooks->addAction('comment.created', function ($comment = null) {
            Log::info('[reading-progress] comment created', [
                'comment_id' => is_object($comment) ? ($comment->id ?? null) : null,
                'post_id' => is_object($comment) ? ($comment->post_id ?? null) : null,
            ]);
        });
    }

    /** Inline CSS for the progress bar and the floating button. */
    protected function styles(string $color): string
    {
        // Basic sanitisation: only allow a hex/rgb/hsl or simple keyword colour.
        if (! preg_match('/^[#a-zA-Z0-9(),.%\s]+$/', $color)) {
            $color = '#c81420';
        }

        return <<<HTML
<style>
#barta-reading-progress{position:fixed;top:0;left:0;height:3px;width:0;z-index:60;background:{$color};transition:width .1s ease-out}
#barta-to-top{position:fixed;right:1rem;bottom:1rem;z-index:60;display:none;height:2.75rem;width:2.75rem;align-items:center;justify-content:center;border-radius:9999px;background:{$color};color:#fff;font-size:1.25rem;line-height:1;box-shadow:0 6px 18px rgba(0,0,0,.25);cursor:pointer;border:0}
#barta-to-top.is-visible{display:flex}
@media (prefers-reduced-motion:reduce){#barta-reading-progress{transition:none}}
</style>
HTML;
    }

    /** Progress-bar + button markup and the scroll behaviour. */
    protected function markup(): string
    {
        return <<<'HTML'
<div id="barta-reading-progress" aria-hidden="true"></div>
<button id="barta-to-top" type="button" aria-label="Back to top">&uarr;</button>
<script>
(function(){
    var bar=document.getElementById('barta-reading-progress');
    var btn=document.getElementById('barta-to-top');
    function onScroll(){
        var h=document.documentElement;
        var max=(h.scrollHeight-h.clientHeight)||1;
        var pct=Math.min(100,Math.max(0,(h.scrollTop/max)*100));
        if(bar){bar.style.width=pct+'%';}
        if(btn){btn.classList.toggle('is-visible',h.scrollTop>400);}
    }
    document.addEventListener('scroll',onScroll,{passive:true});
    window.addEventListener('resize',onScroll,{passive:true});
    onScroll();
    if(btn){btn.addEventListener('click',function(){window.scrollTo({top:0,behavior:'smooth'});});}
})();
</script>
HTML;
    }
}
