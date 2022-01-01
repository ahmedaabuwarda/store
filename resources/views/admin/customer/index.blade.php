@extends('layouts.main')
@section('content')
<!-- Header -->
<div class="header pb-6" style="background-color:#222222;">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-5">

      </div>
    </div>
  </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--8">

  <!-- All customers -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">الزبائن</h3>
            </div>
            <div class="col-xl-8 col-md-12 text-center">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="...ابحث عن زبون">
            </div>
            <div class="col-xl-3 col-md-12 text-right">
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf" data-fromto="0"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
              <a class="btn text-white btn-dark" data-toggle="modal" data-target="#create_customer_modal"><i class="fa fa-plus"></i> اضافة زبون</a>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- Projects table -->
          <table class="table tablee align-items-center table-flush table-hover" id="customer_table">
            @include('admin.customer.table')
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
      <li class="page-item @if(Request::fullUrl() == URL('/customers?page=' . $p)) active @endif"><a class="page-link" href="{{ URL('/customers?page=' . $p) }}">{{ $p }}</a></li>
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

<!-- Modal::create new customer -->
<div class="modal fade" id="create_customer_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">اضافة زبون جديد</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="create_customer_form">
        <div class="modal-body">
          @csrf
          <div class="row">
            <div class="col-12">

              <div class="form-group">
                <label class="form-control-label">اسم الزبون</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                      </div>
                      <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم الزبون" value="{{ old('name') }}" autocomplete="name" required autofocus>
                  </div>
                  @error('name')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>

              <div class="form-group">
                <label class="form-control-label">ملاحظات + العنوان</label>
                  <div class="input-group">
                      <textarea type="text" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="ملاحظات + العنوان" autocomplete="notes">{{ old('name') }}</textarea>
                  </div>
                  @error('notes')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>

            </div>

          </div>
        </div>

        <div class="modal-footer justify-content-center mt--3">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-door-open mr-1"></i>الغاء</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>اضافة</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal::customer to pdf -->
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
  // create new customer form
  $('#create_customer_form').submit(function(e){
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/customer/store",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response){
        if(response.status == "success"){
          Swal.fire(
            'تم!',
            'تم اضافة الزبون بنجاح',
            'success'
          );
          // refresh the table
          get_customers();
          $('#create_customer_form')[0].reset();
          $('#create_customer_modal').modal('hide');
        } else {
          Swal.fire(
            'عفواً',
            'حدث خطأ ما، قد يكون الزبون موجوداً بالفعل',
            'error'
          );
        }
      },
      error: function(response){
        Swal.fire(
          'عفواً',
          'حدث خطأ ما، قد يكون الزبون موجوداً بالفعل',
          'error'
        );
      }
    });
  });
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
  // get all customers
  function get_customers() {
    $.ajax({
      url: "/customers",
      type: "GET",
      success: function(response){
        $('#customer_table').html('');
        $('#customer_table').append(response.table);
      },
      error: function(response){
        Swal.fire(
          'خطأ',
          'حدث خطأ أثناء جلب البيانات',
          'error'
        );
      }
    });
  }
</script>
@endsection
