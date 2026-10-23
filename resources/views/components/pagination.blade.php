@props(['currentPage' => 1, 'lastPage' => 1])
<nav {{ $attributes->merge(['class' => 't-pagination']) }} aria-label="Pagination"><span>Page {{ $currentPage }} of {{ $lastPage }}</span><div><button class="btn btn-sm btn-outline-secondary" type="button" disabled>Previous</button><button class="btn btn-sm btn-outline-secondary" type="button" disabled>Next</button></div></nav>
