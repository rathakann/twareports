@props(['responsive' => true])
<section {{ $attributes->merge(['class' => 't-card']) }}><div @class(['t-table-container', 'table-responsive' => $responsive])><table class="table table-bordered table-hover nowrap text-nowrap align-middle mb-0">{{ $slot }}</table></div></section>
