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

  <!-- All customers -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col-2">
              <h3 class="mb-0">المستفيدون</h3>
            </div>
            <div class="col-xl-6 col-md-12 text-center">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="...ابحث عن مستفيد">
            </div>
            <div class="col-xl-4 col-md-12 text-right">
              <button class="btn btn-success from_to_xlsx_button" data-toggle="tooltip" data-placement="top" title="تصدير xlsx" data-fromto="0"><i class="fas fa-file-excel fa-lg mr-1"></i></button>
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf" data-fromto="0"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
              <a class="btn text-white btn-dark" data-toggle="modal" data-target="#create_customer_modal"><i class="fa fa-plus"></i> اضافة مستفيد</a>
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
  @include('includes.pagination', ['paginator' => $customers])

  <!-- Footer -->
  @include('includes.footer')

</div>

<!-- Modal::create new customer -->
<div class="modal fade" id="create_customer_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">اضافة مستفيد جديد</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="create_customer_form">
        <div class="modal-body">
          @csrf
          <ul>
            <li>ملف الاكسل: الرقم | الاسم | رقم الهوية | الهاتف | عدد افراد الاسرة | المسجد | ملاحظات</li>
          </ul>
          <div class="row">

            <div class="col-md-6 col-sm-12">

              <div class="form-group">
                <label class="form-control-label">اسم المستفيد</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" minlength="6" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم المستفيد" value="{{ old('name') }}" autocomplete="name" autofocus>
                </div>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>
            <div class="col-md-6 col-sm-12">

              <div class="form-group">
                <label class="form-control-label">رقم الهوية</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" maxlength='9' minlength='9' class="form-control @error('identity') is-invalid @enderror" name="identity" placeholder="رقم الهوية" value="{{ old('identity') }}" autocomplete="identity" autofocus>
                </div>
                @error('identity')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>
            <div class="col-md-6 col-sm-12">

              <div class="form-group">
                <label class="form-control-label">رقم الجوال</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" maxlength='10' minlength='10' class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="رقم الجوال" value="{{ old('phone') }}" autocomplete="phone" autofocus>
                </div>
                @error('phone')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>
            <div class="col-md-6 col-sm-12">

              <div class="form-group">
                <label class="form-control-label">عدد افراد الاسرة</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" maxlength='2' class="form-control @error('family_number') is-invalid @enderror" name="family_number" placeholder="عدد افراد الاسرة" value="{{ old('family_number') }}" autocomplete="family_number" autofocus>
                </div>
                @error('family_number')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                <label class="form-control-label">المساجد</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-mosque text-primary"></i></span>
                  </div>
                  <select class="form-control @error('mosque_id') is-invalid @enderror" name="mosque_id" id="mosque_id">
                    <option value="">اختر المسجد</option>
                    @foreach($mosques as $mosque)
                    <option value="{{ $mosque->id }}">{{ $mosque->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('mosque')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
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
            <div class="col-md-6 col-sm-12">
              <!-- Add input here to attach a file -->
              <div class="form-group">
                <label class="form-control-label">استيراد مستفيدون</label>
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
@include('admin.customer.from_to_pdf')

@include('admin.customer.from_to_xlsx')

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
  $('#create_customer_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/customer/store",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        if (response.status == "success") {
          Swal.fire(
            'تم!',
            response.message,
            'success'
          );
          // refresh the table
          get_customers();
          $('#create_customer_form')[0].reset();
          $('#create_customer_modal').modal('hide');
        } else {
          Swal.fire(
            'عفواً',
            response.message,
            'error'
          );
        }
      },
      error: function(response) {
        Swal.fire(
          'عفواً',
          response.message,
          'error'
        );
      }
    });
  });
  // show customer kashf to pdf modal
  $('#customer_table').on('click', '.from_to_pdf_button', function(e) {
    let from_to = $(this).data('fromto');
    $('#from_to_pdf_modal').modal('show');
    $('#from_to').val(from_to);
  });
  // show customer to pdf modal
  $('.from_to_pdf_button').click(function(e) {
    let from_to = $(this).data('fromto');
    $('#from_to_pdf_modal').modal('show');
    $('#from_to').val(from_to);
  });
  // create customer to pdf form
  $('#from_to_pdf_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    if (data.get('from_to') == '0') {
      $.ajax({
        url: "/customer/to_pdf",
        type: "POST",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function(response) {
          $('#from_to_pdf_modal').modal('hide');
        }
      });
    } else {
      $.ajax({
        url: "/customer/kashf_to_pdf",
        type: "POST",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function(response) {
          $('#from_to_pdf_modal').modal('hide');
        }
      });
    }
    $('#from_to_pdf_form')[0].reset();
    $('#from_to_pdf_modal').modal('hide');
  });
  // show customer to xlsx modal
  $('.from_to_xlsx_button').click(function(e) {
    $('#from_to_xlsx_modal').modal('show');
    $('#from_to').val(from_to);
  });
  // create customer to xlsx form
  $('#from_to_xlsx_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/customer/to_xlsx",
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
  // get all customers
  function get_customers() {
    $.ajax({
      url: "/customers",
      type: "GET",
      success: function(response) {
        $('#customer_table').html('');
        $('#customer_table').append(response.table);
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
