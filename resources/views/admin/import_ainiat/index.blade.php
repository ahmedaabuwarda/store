@extends('layouts.main')
@section('content')
<!-- Header -->
<div class="header pb-6" style="background-color:#222222;">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-5 m-auto">
        @include('includes.alert')
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
            <div class="col-xl-2 col-md-12 text-md-center text-xl-left">
              <h3 class="mb-0">عينيات واردة</h3>
            </div>
            <div class="col-xl-6 col-md-12 text-xl-left text-md-center">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="...ابحث عن فاتورة عينيات واردة">
            </div>
            <div class="col-xl-4 col-md-12 text-xl-right text-md-center">
              @can('add_import_ainiats')
              <button class="btn btn-success from_to_xlsx_button" data-toggle="tooltip" data-placement="top" title="تصدير xlsx"><i class="fas fa-file-excel fa-lg mr-1"></i></button>
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
              <a href="{{ URL('/import_ainiat/create') }}" class="btn btn-dark text-white">
                <i class="fa fa-plus"></i> فاتورة عينيات واردة</a>
              @endcan
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- Projects table -->
          <table class="table tablee align-items-center table-flush table-hover" id="import_ainiat_table">
            @include('admin.import_ainiat.table')
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- paginate -->
  @include('includes.pagination', ['paginator' => $import_ainiats])

  <!-- Footer -->
  @include('includes.footer')

</div>

<!-- Modal::show bill -->
@include('includes.show_bill')

<!-- Modal::bill to pdf-->
@include('includes.from_to_pdf')
<!-- Modal::bill to xlsx-->
@include('includes.from_to_xlsx')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $("#search_input").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $(".tablee tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });

  $('.show_button').on('click', function() {
    let id = $(this).data('dataid');
    let _token = $('input[name="_token"]').val();
    $.ajax({
      url: 'import_ainiat/show',
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

  $("#import_ainiat_table").on("click", ".delete_import_ainiat_button", function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    Swal.fire({
      title: 'هل انت متاكد من الحذف ؟',
      text: "!لا يمكنك التراجع بعد هذه الخطوة",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'نعم!',
      cancelButtonText: 'الغاء'
    }).then((result) => {
      if (result.isConfirmed) {
        let _token = $('input[name=_token]').val();
        $.ajax({
          url: "/import_ainiat/delete",
          type: "POST",
          data: {
            id: id,
            _token: _token
          },
          success: function(response) {
            if (response.status == 'success') {
              Swal.fire(
                'تم الحذف!',
                'تم الحذف بنجاح',
                'success'
              );
              // refresh page
              location.reload();
            } else {
              Swal.fire(
                'خطأ',
                'حدث خطأ أثناء الحذف',
                'error'
              );
            }
          },
          error: function(response) {
            Swal.fire(
              'خطأ',
              'حدث خطأ أثناء الحذف',
              'error'
            );
          }
        });
      }
    });
  });

  // show bill to pdf modal
  $('.from_to_pdf_button').click(function(e) {
    $('#from_to_pdf_modal').modal('show');
  });

  // sanadat qapd to pdf form
  $('#from_to_pdf_form').submit(function(e) {
    e.preventDefault();
    let from = $('input[name="from"]').val();
    let to = $('input[name="to"]').val();
    let _token = $('input[name="_token"]').val();
    $.ajax({
      url: "/import_ainiat/to_pdf",
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
    $('#from_to_pdf_form')[0].reset();
    $('#from_to_pdf_modal').modal('hide');
  });

  // show bill to pdf modal
  $('.from_to_xlsx_button').click(function(e) {
    $('#from_to_xlsx_modal').modal('show');
  });

  // sanadat qapd to pdf form
  $('#from_to_xlsx_form').submit(function(e) {
    e.preventDefault();
    let from = $('input[name="from"]').val();
    let to = $('input[name="to"]').val();
    let _token = $('input[name="_token"]').val();
    $.ajax({
      url: "/import_ainiat/to_xlsx",
      type: "POST",
      data: {
        from: from,
        to: to,
        _token: _token
      },
      success: function(response) {
        $('#from_to_xlsx_modal').modal('hide');
      }
    });
    $('#from_to_xlsx_form')[0].reset();
    $('#from_to_xlsx_modal').modal('hide');
  });
</script>
@endsection
