@extends('layouts.main')
@section('content')
<!-- Header -->
<div class="header pb-6" style="background-color:#222222;">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-5 m-auto">
        <!-- display alert -->
        @include('includes.alert')
      </div>
    </div>
  </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--8">

  <!-- All wasis -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">الاوصياء</h3>
            </div>
            <div class="col-xl-8 col-md-12 text-center">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="...ابحث عن وصي">
            </div>
            <div class="col-xl-3 col-md-12 text-right">
              @can('add_wasis')
              <button class="btn btn-success from_to_xlsx_button" data-toggle="tooltip" data-placement="top" title="تصدير xlsx" data-fromto="0"><i class="fas fa-file-excel fa-lg mr-1"></i></button>
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf" data-fromto="0"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
              <a class="btn text-white btn-dark" data-toggle="modal" data-target="#create_wasi_modal"><i class="fa fa-plus"></i> اضافة وصي</a>
              @endcan
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- wasi table -->
          <table class="table tablee align-items-center table-flush table-hover" id="wasi_table">
            @include('admin.wasi.table')
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- paginate -->
  @include('includes.pagination', ['paginator' => $wasis])

  <!-- Footer -->
  @include('includes.footer')

</div>

<!-- Modal::create new customer -->
<div class="modal fade" id="create_wasi_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">اضافة وصي جديد</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="create_wasi_form">
        <div class="modal-body">
          @csrf
          <ul>
            <li>ملف الاكسل: الرقم | الاسم | ملاحظات</li>
          </ul>
          <div class="row">
            <div class="col-12">

              <div class="form-group">
                <label class="form-control-label">اسم الوصي</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم الوصي" value="{{ old('name') }}" autocomplete="name" autofocus>
                </div>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

              <div class="form-group">
                <label class="form-control-label">ملاحظات</label>
                <div class="input-group">
                  <textarea type="text" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="(في حال كان فارغ : لايوجد) ملاحظات" autocomplete="notes" rows="3">{{ old('notes') }}</textarea>
                </div>
                @error('notes')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

              <!-- Add input here to attach a file -->
              <div class="form-group">
                <label class="form-control-label">استيراد اوصياء</label>
                <div class="custom-file">
                  <input type="file" class="custom-file-input @error('file_attachment') is-invalid @enderror" name="file_attachment" accept=".xls,.xlsx">
                  <label class="custom-file-label" for="file_attachment">اختر ملفاً...</label>
                </div>
                @error('file_attachment')
                <span class="text-danger d-block mt-2">{{ $message }}</span>
                @enderror
              </div>

            </div>

          </div>
        </div>

        <div class="modal-footer justify-content-center mt--3">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-door-open mr-1"></i>الغاء</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal::customer to pdf -->
@include('includes.from_to_pdf')

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
  // create new customer form
  $('#create_wasi_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/wasi/store",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        if (response.status == "success") {
          Swal.fire(
            'تم!',
            'تم اضافة العملة بنجاح',
            'success'
          );
          // refresh the table
          get_wasis();
          $('#create_wasi_form')[0].reset();
          $('#create_wasi_modal').modal('hide');
        } else {
          Swal.fire(
            'عفواً',
            'حدث خطأ ما، قد تكون العملة موجوداً بالفعل',
            'error'
          );
        }
      },
      error: function(response) {
        Swal.fire(
          'عفواً',
          'حدث خطأ ما، قد تكون العملة موجوداً بالفعل',
          'error'
        );
      }
    });
  });
  $('#wasi_table').on('click', '.edit_button', function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    $.ajax({
      url: "/wasi/edit",
      type: "GET",
      data: {
        id: id
      },
      success: function(response) {
        if (response.status == "success") {
          $('#create_wasi_modal').modal('show');
          $('#exampleModalLabel').text('تعديل عملة');
          $('#create_wasi_modal .row').html(response.modal);
        } else {
          Swal.fire(
            'عفواً',
            'حدث خطأ ما',
            'error'
          );
        }
      },
      error: function(response) {
        Swal.fire(
          'عفواً',
          'حدث خطأ ما',
          'error'
        );
      }
    });
  });
  // wasi to pdf modal
  $('.from_to_pdf_button').click(function(e) {
    $('#from_to_pdf_modal').modal('show');
  });
  // wasi to pdf form
  $('#from_to_pdf_form').submit(function(e) {
    e.preventDefault();
    let from = $('input[name="from"]').val();
    let to = $('input[name="to"]').val();
    let _token = $('input[name="_token"]').val();
    $.ajax({
      url: "/wasi/to_pdf",
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
  // wasi to xlsx modal
  $('.from_to_xlsx_button').click(function(e) {
    $('#from_to_xlsx_modal').modal('show');
  });
  // wasi to xlsx form
  $('#from_to_xlsx_form').submit(function(e) {
    e.preventDefault();
    let from = $('input[name="from"]').val();
    let to = $('input[name="to"]').val();
    let _token = $('input[name="_token"]').val();
    $.ajax({
      url: "/wasi/to_xlsx",
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
  // get all customers
  function get_wasis() {
    $.ajax({
      url: "/wasis",
      type: "GET",
      success: function(response) {
        $('#wasi_table').html('');
        $('#wasi_table').append(response.table);
      },
      error: function(response) {
        Swal.fire(
          'خطأ',
          'حدث خطأ أثناء جلب الملاحظاتات',
          'error'
        );
      }
    });
  }
</script>
@endsection
