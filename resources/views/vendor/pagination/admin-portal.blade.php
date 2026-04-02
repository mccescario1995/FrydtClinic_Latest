@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <div class="d-flex justify-content-center">
            <ul class="pagination pagination-rounded shadow-sm" style="margin: 0; background: white; border-radius: 10px; padding: 0.5rem; border: 1px solid #dee2e6;">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" style="color: #6c757d; border: none; background: transparent;">
                            <i class="fas fa-chevron-left me-1"></i>@lang('pagination.previous')
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="color: var(--admin-primary); border: none; background: transparent; border-radius: 6px; transition: all 0.3s ease;">
                            <i class="fas fa-chevron-left me-1"></i>@lang('pagination.previous')
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link" style="border: none; background: transparent; color: #6c757d;">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link" style="background: var(--admin-primary); border: 1px solid var(--admin-primary); color: black; border-radius: 6px; font-weight: 600;">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}" style="color: var(--admin-primary); border: none; background: transparent; border-radius: 6px; transition: all 0.3s ease;">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" style="color: var(--admin-primary); border: none; background: transparent; border-radius: 6px; transition: all 0.3s ease;">
                            @lang('pagination.next')<i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" style="color: #6c757d; border: none; background: transparent;">
                            @lang('pagination.next')<i class="fas fa-chevron-right ms-1"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>

        {{-- Optional: Show results info --}}
        <div class="d-flex justify-content-center mt-2">
            <small class="text-muted">
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
            </small>
        </div>
    </nav>
@endif

<style>
.pagination .page-link:hover {
    background-color: var(--admin-light) !important;
    color: var(--admin-secondary) !important;
    border-color: var(--admin-primary) !important;
}

.pagination .page-item.active .page-link {
    background-color: var(--admin-primary) !important;
    border-color: var(--admin-primary) !important;
}

.pagination .page-link:focus {
    box-shadow: 0 0 0 0.2rem rgba(74, 222, 128, 0.25) !important;
}
</style>
