@props([
    'number',
    'label',
    'title',
    'variant'  => null,
    'pageHead' => null,
])
<section class="proposal-page {{ $variant ? 'proposal-'.$variant.'-page' : '' }}">
    @if ($pageHead !== null)
        <div class="proposal-page-head">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
            <span>{{ $pageHead }}</span>
        </div>
    @endif
    <div class="proposal-section-title">
        <span>{{ $number }}</span>
        <div>
            <small>{{ $label }}</small>
            <h2>{{ $title }}</h2>
        </div>
    </div>
    {{ $slot }}
</section>
