@extends('layouts.main')
@section('content')
<!-- Header -->
<div class="header pb-6" style="background-color:#222222;">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">

      </div>
    </div>
  </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--6">

  <!-- All products -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">نتائج البحث</h3>
            </div>
            <div class="col-xl-8 col-md-12 text-center">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="...ابحث في نتائج البحث">
            </div>
            <div class="col-xl-2 col-md-12 text-right">
              <a href="{{ URL('/product/to_excel') }}" class="btn btn-success disabled" data-toggle="tooltip" data-placement="top" title="تصدير excel"><i class="fas fa-file-excel fa-lg mr-1"></i></a>
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf" data-fromto="0"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- Projects table -->
          <table class="table tablee align-items-center table-flush table-hover" id="product_table">
            @php $providers = ''; @endphp
            @if($target == 'providers')
              @php $providers = $result; @endphp
              @include('admin.provider.table')
            @elseif($target == 'customers')
              @php $customers = $result; @endphp
              @include('admin.customer.table')
            @elseif($target == 'products')
              @php $products = $result; @endphp
              @include('website.table')
            @endif
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
      <li class="page-item @if(Request::fullUrl() == URL('/search?page=' . $p)) active @endif"><a class="page-link" href="{{ URL('/search?page=' . $p) }}">{{ $p }}</a></li>
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

<!-- Modal::product to pdf -->
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

  @if($target == 'providers')
    // show provider kashf to pdf modal
    $('#provider_table').on('click', '.from_to_pdf_button', function(e){
      let from_to = $(this).data('fromto');
      $('#from_to_pdf_modal').modal('show');
      $('#from_to').val(from_to);
    });
    // show provider to pdf modal
    $('.from_to_pdf_button').click(function(e){
      let from_to = $(this).data('fromto');
      $('#from_to_pdf_modal').modal('show');
      $('#from_to').val(from_to);
    });
    // create provider to pdf form
    $('#from_to_pdf_form').submit(function(e){
      e.preventDefault();
      let from = $('input[name="from"]').val();
      let to = $('input[name="to"]').val();
      let from_to = $('#from_to').val();
      let _token = $('input[name="_token"]').val();
      if(from_to == '0'){
        $.ajax({
          url: "/provider/to_pdf",
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
      } else {
        $.ajax({
          url: "/provider/kashf_to_pdf",
          type: "POST",
          data: {
            from: from,
            to: to,
            id: from_to,
            _token: _token
          },
          success: function(response){
            $('#from_to_pdf_modal').modal('hide');
          }
        });
      }
      $('#from_to_pdf_form')[0].reset();
      $('#from_to_pdf_modal').modal('hide');
    });
  @elseif($target == 'customers')
    // show customer kashf to pdf modal
    $('#customer_table').on('click', '.from_to_pdf_button', function(e){
      let from_to = $(this).data('fromto');
      $('#from_to_pdf_modal').modal('show');
      $('#from_to').val(from_to);
    });
    // show customer to pdf modal
    $('.from_to_pdf_button').click(function(e){
      let from_to = $(this).data('fromto');
      $('#from_to_pdf_modal').modal('show');
      $('#from_to').val(from_to);
    });
    // create provider to pdf form
    $('#from_to_pdf_form').submit(function(e){
      e.preventDefault();
      let from = $('input[name="from"]').val();
      let to = $('input[name="to"]').val();
      let from_to = $('#from_to').val();
      let _token = $('input[name="_token"]').val();
      if(from_to == '0'){
        $.ajax({
          url: "/customer/to_pdf",
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
      } else {
        $.ajax({
          url: "/customer/kashf_to_pdf",
          type: "POST",
          data: {
            from: from,
            to: to,
            id: from_to,
            _token: _token
          },
          success: function(response){
            $('#from_to_pdf_modal').modal('hide');
          }
        });
      }
      $('#from_to_pdf_form')[0].reset();
      $('#from_to_pdf_modal').modal('hide');
    });
  @elseif($target == 'products')
    // show product to pdf modal
    $('#product_table').on('click', '.from_to_pdf_button', function(e) {
        let from_to = $(this).data('fromto');
        $('#from_to_pdf_modal').modal('show');
        $('#from_to').val(from_to);
    });
    // show product to pdf modal
    $('.from_to_pdf_button').click(function(e) {
        let from_to = $(this).data('fromto');
        $('#from_to_pdf_modal').modal('show');
        $('#from_to').val(from_to);
    });
    // create product to pdf form
    $('#from_to_pdf_form').submit(function(e) {
        e.preventDefault();
        let from = $('input[name="from"]').val();
        let to = $('input[name="to"]').val();
        let from_to = $('#from_to').val();
        let _token = $('input[name="_token"]').val();
        // from to == -1 means: all box movements
        if (from_to == '-1') {
            $.ajax({
                url: "/box/to_pdf",
                type: "POST",
                data: {
                    from: from,
                    to: to,
                    _token: _token
                },
                success: function(response) {
                    $('#from_to_pdf_modal').modal('hide');
                }
            });
            // from to == 0 means all products
        } else if (from_to == '0') {
            $.ajax({
                url: "/product/to_pdf",
                type: "POST",
                data: {
                    from: from,
                    to: to,
                    _token: _token
                },
                success: function(response) {
                    $('#from_to_pdf_modal').modal('hide');
                }
            });
        } else {
            $.ajax({
                url: "/product/jard_to_pdf",
                type: "POST",
                data: {
                    from: from,
                    to: to,
                    id: from_to,
                    _token: _token
                },
                success: function(response) {
                    $('#from_to_pdf_modal').modal('hide');
                }
            });
        }
        $('#from_to_pdf_form')[0].reset();
        $('#from_to_pdf_modal').modal('hide');
    });
  @endif

</script>

@endsection