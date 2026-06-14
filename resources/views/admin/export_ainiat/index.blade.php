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

  <!-- All Export Ainiat -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card" id="export_ainiat_table_card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col-xl-2 col-md-12 text-md-center text-xl-left">
              <h3 class="mb-0">عينيات صادرة</h3>
            </div>
            <div class="col-xl-6 col-md-12 text-xl-left text-md-center">
              <input type="text" name="search_input" id="search_input" class="form-control"
                placeholder="...ابحث عن فاتورة عينيات صادرة">
            </div>
            <div class="col-xl-4 col-md-12 text-xl-right text-md-center">
              <button class="btn btn-success from_to_xlsx_button" data-toggle="tooltip" data-placement="top" title="تصدير xlsx"><i class="fas fa-file-excel fa-lg mr-1"></i></button>
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
              <button class="btn btn-dark create_export_ainiat_button" data-toggle="tooltip" data-placement="top" title="فاتورة جديدة"><i class="fas fa-plus fa-lg mr-1"></i> فاتورة عينيات صادرة</button>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- Projects table -->
          <table class="table tablee align-items-center table-flush table-hover" id="export_ainiat_table">
            @include('admin.export_ainiat.table')
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- paginate -->
  @include('includes.pagination', ['paginator' => $export_ainiats])

  <!-- Footer -->
  @include('includes.footer')

</div>

<!-- Modal::create new export ainiat-->
<div class="modal fade" id="create_export_ainiat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">اضافة فاتورة عينيات صادرة جديدة</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="create_export_ainiat_form">
        <div class="modal-body">
          @csrf
          <ul>
            <li>ملف الاكسل: الرقم | الاسم | رقم الهوية | الهاتف | عدد افراد الاسرة | المسجد | ملاحظات</li>
          </ul>
          <div class="row">

            <div class="col-xl-6 col-md-12">
              <div class="form-group">
                <label class="form-control-label">اختار التاريخ</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                  </div>
                  <input class="form-control datepicker @error('date_created') is-invalid @enderror"
                    placeholder="اختار التاريخ" type="text" name="date_created"
                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                </div>
              </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <!-- Add input here to attach a file -->
              <div class="form-group">
                <label class="form-control-label">استيراد مستفيدين</label>
                <div class="custom-file">
                  <input type="file" class="custom-file-input @error('file_attachment') is-invalid @enderror" name="file_attachment" accept=".xls,.xlsx">
                  <label class="custom-file-label" for="file_attachment">اختر ملفاً...</label>
                </div>
                @error('file_attachment')
                <span class="text-danger d-block mt-2">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                <label class="form-control-label">العينيات</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-primary"></i></span>
                  </div>
                  <select class="form-control @error('product_id') is-invalid @enderror" name="product_id" id="product_id" required>
                    <option value="">اختر العينية</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('product')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                <label class="form-control-label">الملاحظات</label>
                <div class="input-group">
                  <textarea type="text" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="الملاحظات" autocomplete="notes" rows="2">{{ old('notes') }}</textarea>
                </div>
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

<!-- Modal::show bill -->
@include('includes.show_bill')

<!-- Modal::multi_modal -->
@include('includes.multi_modal')

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

  // create Export Ainiat modal
  $('.create_export_ainiat_button').click(function(e) {
    $('#create_export_ainiat_modal').modal('show');
  });

  // sanadat qapd to pdf form
  $('#create_export_ainiat_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/export_ainiat/store",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        if (response.status == "success") {
          // refresh the table
          get_tables();
          $('#create_export_ainiat_form')[0].reset();
          $('#create_export_ainiat_modal').modal('hide');
          Swal.fire(
            'تم!',
            response.message,
            'success'
          );
        } else {
          Swal.fire(
            '! عفواً',
            response.message,
            'error'
          );
        }
      }
    });
    $('#create_export_ainiat_form')[0].reset();
    $('#create_export_ainiat_modal').modal('hide');
  });

  // create Export Ainiat
  $('#export_ainiat_table_card').on('click', '.multi_button', function() {
    var movement = $(this).data('movement');
    if (movement == 'create_export_ainiat') {
      $.ajax({
        url: 'export_ainiat/create',
        type: 'GET',
        success: function(response) {
          if (response.status == 'success') {
            $('#show_bill_form .modal-body').html('');
            $('#show_bill_form .modal-body').html(response.modal);
            $('#show_bill_modal #show_bill_modalLabel').text('انشاء فاتورة عينيات صادرة جديدة');
            $('#show_bill_modal #movement').val(movement);
            $('#show_bill_modal').modal('show');
            $('.selectpicker').selectpicker();
          } else {
            Swal.fire(
              'عفواً',
              'حدث خطأ ما.',
              'error'
            );
          }
        }
      });
    }
  });

  $('#show_bill_modal #show_bill_form').submit(function(e) {
    e.preventDefault();
    var data = new FormData(this);
    var movement = data.get('movement');
    $.ajax({
      url: 'export_ainiat/store',
      type: 'POST',
      processData: false,
      contentType: false,
      cache: false,
      data: data,
      success: function(response) {
        if (response.status == 'success') {
          Swal.fire({
            title: 'تم!',
            text: 'تم إضافة الفاتورة بنجاح',
            icon: 'success',
            confirmButtonText: 'حسنا',
            confirmButtonColor: '#3085d6',
          });
          get_tables();
          $('#show_bill_modal').modal('hide');
        } else {
          Swal.fire({
            title: 'خطأ!',
            text: 'حدث خطأ أثناء إضافة الفاتورة',
            icon: 'error',
            confirmButtonText: 'حسنا',
            confirmButtonColor: '#3085d6',
          });
        }
      }
    });
  });

  // show Export Ainiat
  $('#export_ainiat_table').on('click', '.show_button', function() {
    let id = $(this).data('dataid');
    let movement = $(this).data('movement');
    let _token = $('input[name="_token"]').val();
    if (movement == 'show_export_ainiat') {
      $.ajax({
        url: 'export_ainiat/show',
        type: 'GET',
        data: {
          id: id,
          _token: _token
        },
        success: function(response) {
          $('#show_bill_form .modal-body').html('');
          $('#show_bill_form .modal-body').html(response.bill_data);
          $('#show_bill_modal #show_bill_modalLabel').text('عرض فاتورة عينيات صادرة');
          $('#show_bill_modal').modal('show');
        }
      });
    }
  });

  $('#show_bill_modal #show_bill_form').submit(function(e) {
    e.preventDefault();
    var data = new FormData(this);
    var movement = $('#show_bill_modal #movement').val();

  });

  $("#export_ainiat_table").on("click", ".delete_export_ainiat_button", function(e) {
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
          url: "/export_ainiat/delete",
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
    let data = new FormData(this);
    $.ajax({
      url: "/export_ainiat/to_pdf",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        $('#from_to_pdf_modal').modal('hide');
      }
    });
    $('#from_to_pdf_form')[0].reset();
    $('#from_to_pdf_modal').modal('hide');
  });

  // show bill to xlsx modal
  $('.from_to_xlsx_button').click(function(e) {
    $('#from_to_xlsx_modal').modal('show');
  });
  // sanadat qapd to xlsx form
  $('#from_to_xlsx_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/export_ainiat/to_xlsx",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        $('#from_to_xlsx_modal').modal('hide');
      }
    });
    $('#from_to_xlsx_form')[0].reset();
    $('#from_to_xlsx_modal').modal('hide');
  });

  function get_tables() {
    $.ajax({
      url: 'export_ainiats',
      type: 'GET',
      success: function(response) {
        $('#export_ainiat_table').html('');
        $('#export_ainiat_table').html(response.table);
      }
    });
  }
</script>
@endsection
