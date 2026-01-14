@if ($paginator->hasPages())
    <nav class="d-flex justify-content-center">
        <div class="d-flex align-items-center">
            {{-- Information sur les résultats --}}
            <p class="small text-muted me-3 mb-0">
                {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} sur {{ $paginator->total() }}
            </p>

            {{-- Pagination simple --}}
            <ul class="pagination pagination-sm pagination-minimal mb-0">
                {{-- Page précédente --}}
                @if (!$paginator->onFirstPage())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Précédent</a>
                    </li>
                @endif

                {{-- Numéros de pages --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Page suivante --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant</a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif