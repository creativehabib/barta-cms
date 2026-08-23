{{-- Reusable sidebar. Uses configured 'sidebar' widgets, else sensible defaults. --}}
@php($sidebarWidgets = \App\Models\Widget::active()->area('sidebar')->get())
<aside class="space-y-6">
    @if ($sidebarWidgets->isNotEmpty())
        @foreach ($sidebarWidgets as $widget)
            @include('theme::partials.widget', ['widget' => $widget])
        @endforeach
    @else
        {{-- Default: most read --}}
        @php($popular = \App\Models\Post::published()->orderByDesc('views')->take(5)->get())
        @if ($popular->isNotEmpty())
            <section>
                <h3 class="mb-2 border-l-4 border-brand-600 pl-2 text-lg font-black">{{ app()->getLocale() === 'bn' ? 'সর্বাধিক পঠিত' : 'Most read' }}</h3>
                <div class="divide-y divide-ink-100">
                    @foreach ($popular as $index => $post)
                        <div class="flex items-center gap-3">
                            <span class="text-2xl font-black text-ink-200">{{ localized_number($index + 1) }}</span>
                            <div class="min-w-0 flex-1">@include('theme::partials.card', ['post' => $post, 'variant' => 'list'])</div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @include('theme::partials.newsletter')
        @include('theme::partials.ad', ['slot' => 'sidebar', 'class' => 'text-center'])
    @endif
</aside>
