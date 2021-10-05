@extends('layouts.main')
@section('content')
<!-- Header -->
<div class="header pb-6" style="background-color:#420516;">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-5">

      </div>
    </div>
  </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--8">

  <!-- All buy bills -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">فواتير الشراء</h3>
            </div>
            <div class="col-xl-8 col-md-12 text-center">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="...ابحث عن فاتورة">
            </div>
            <div class="col-xl-3 col-md-12 text-xl-right text-md-center">
              <a href="{{ URL('/sanadat_qapd/to_excel') }}" class="btn btn-success disabled" data-toggle="tooltip" data-placement="top" title="تصدير excel"><i class="fas fa-file-excel fa-lg mr-1"></i></a>
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
              <a href="{{ URL('/buy_bill/create') }}" class="btn btn-dark text-white"><i class="fa fa-plus"></i> فاتورة شراء</a>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- Projects table -->
          <table class="table tablee align-items-center table-flush table-hover">
            @include('admin.buy_bill.table')
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- paginate -->
  <nav aria-label="..." class="justify-content-center">
    <ul class="pagination justify-content-center">
      <li class="page-item">
        <a class="page-link" href="{{ Request::fullUrl(); }}" tabindex="-1">
        <i class="fa fa-angle-left"></i>
        <span class="sr-only">Previous</span>
        </a>
      </li>
      @for($p = 1; $p <= $pages; $p++)
      <li class="page-item @if(Request::fullUrl() == URL('/buy_bills?page=' . $p)) active @endif"><a class="page-link" href="{{ URL('/buy_bills?page=' . $p) }}">{{ $p }}</a></li>
      @endfor
      <li class="page-item">
        <a class="page-link" href="{{ Request::fullUrl(); }}">
        <i class="fa fa-angle-right"></i>
        <span class="sr-only">Next</span>
        </a>
      </li>
    </ul>
  </nav>

  <!-- Footer -->
  @include('includes.footer')

</div>

<!-- Modal::show bill -->
@include('includes.show_bill')

<!-- Modal::bill to pdf-->
@include('includes.from_to')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function(){
    $("#search_input").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $(".tablee tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });
  $('.show_button').on('click', function(){
    let id = $(this).data('dataid');
    let _token = $('input[name="_token"]').val();
    $.ajax({
      url: 'buy_bill/show',
      type: 'GET',
      data: {
        id: id,
        _token: _token
      },
      success: function(response) {
        $('#show_bill_form .modal-body').html(response.bill_data);
        $('#show_bill_modal').modal('show');
      }
    });
  });
  // show bill to pdf modal
  $('.from_to_pdf_button').click(function(e){
    $('#from_to_pdf_modal').modal('show');
  });
  // sanadat qapd to pdf form
  $('#from_to_pdf_form').submit(function(e){
    e.preventDefault();
    let from = $('input[name="from"]').val();
    let to = $('input[name="to"]').val();
    let _token = $('input[name="_token"]').val();
    $.ajax({
      url: "/buy_bill/to_pdf",
      type: "POST",
      data: {
        from: from,
        to: to,
        _token: _token
      },
      success: function(response){
        $('#from_to_pdf_modal').modal('hide');
      }
    });
    $('#from_to_pdf_form')[0].reset();
    $('#from_to_pdf_modal').modal('hide');
  });
</script>
@endsection
