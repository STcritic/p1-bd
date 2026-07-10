@props(['variant' => null])
<section class="pp-page {{ $variant ? 'pp-page--'.$variant : '' }}">
    {{ $slot }}
</section>
