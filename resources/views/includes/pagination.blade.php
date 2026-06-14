@if (isset($paginator) && $paginator->lastPage() > 1)
  @php
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $start = max(2, $currentPage - 2);
    $end = min($lastPage - 1, $currentPage + 2);
  @endphp
  <nav aria-label="..." class="justify-content-center">
    <ul class="pagination justify-content-center">
      {{-- Previous Page Link --}}
      @if ($paginator->onFirstPage())
        <li class="page-item disabled">
          <span class="page-link"><i class="fa fa-angle-left"></i></span>
        </li>
      @else
        <li class="page-item">
          <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
            <i class="fa fa-angle-left"></i>
          </a>
        </li>
      @endif

      {{-- First Page --}}
      <li class="page-item @if($currentPage == 1) active @endif">
        <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
      </li>

      {{-- First Ellipsis --}}
      @if ($start > 2)
        <li class="page-item disabled"><span class="page-link">...</span></li>
      @endif

      {{-- Middle Pages --}}
      @for ($p = $start; $p <= $end; $p++)
        <li class="page-item @if($currentPage == $p) active @endif">
          <a class="page-link" href="{{ $paginator->url($p) }}">{{ $p }}</a>
        </li>
      @endfor

      {{-- Last Ellipsis --}}
      @if ($end < $lastPage - 1)
        <li class="page-item disabled"><span class="page-link">...</span></li>
      @endif

      {{-- Last Page --}}
      @if ($lastPage > 1)
        <li class="page-item @if($currentPage == $lastPage) active @endif">
          <a class="page-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
        </li>
      @endif

      {{-- Next Page Link --}}
      @if ($paginator->hasMorePages())
        <li class="page-item">
          <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
            <i class="fa fa-angle-right"></i>
          </a>
        </li>
      @else
        <li class="page-item disabled">
          <span class="page-link"><i class="fa fa-angle-right"></i></span>
        </li>
      @endif
    </ul>
  </nav>
@endif
