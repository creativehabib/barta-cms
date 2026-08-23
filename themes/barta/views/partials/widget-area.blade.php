{{-- Renders all active widgets assigned to $area. --}}
@php
    $areaWidgets = \App\Models\Widget::active()->area($area)->get();
@endphp
@foreach ($areaWidgets as $widget)
    @include('theme::partials.widget', ['widget' => $widget])
@endforeach
