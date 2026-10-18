@if($paginator->hasPages())
<nav class="pagination" aria-label="Phân trang">
    @if($paginator->onFirstPage())<span class="muted">← Trước</span>@else<a class="btn btn-outline btn-small" href="{{ $paginator->previousPageUrl() }}">← Trước</a>@endif
    <span>Trang {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
    @if($paginator->hasMorePages())<a class="btn btn-outline btn-small" href="{{ $paginator->nextPageUrl() }}">Sau →</a>@else<span class="muted">Sau →</span>@endif
</nav>
@endif

