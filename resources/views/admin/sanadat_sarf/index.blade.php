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

  <!-- All sanadat sarf -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col-xl-2 col-md-12 text-md-center text-xl-left">
              <h3 class="mb-0">سندات الصرف</h3>
            </div>
            <div class="col-xl-7 col-md-12 text-xl-left text-md-center">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="...ابحث عن سند صرف">
            </div>
            <div class="col-xl-3 col-md-12 text-xl-right text-md-center">
              <button class="btn btn-success from_to_xlsx_button" data-toggle="tooltip" data-placement="top" title="تصدير xlsx"><i class="fas fa-file-excel fa-lg mr-1"></i></button>
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
              <a class="btn btn-dark text-white" data-toggle="modal" data-target="#create_sanadat_sarf_modal"><i class="fa fa-plus"></i> سند صرف </a>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- sanadat sarf table -->
          <table class="table tablee align-items-center table-flush table-hover" id="sanadat_sarf_table">
            @include('admin.sanadat_sarf.table')
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- paginate -->
  @include('includes.pagination', ['paginator' => $sanadat_sarfs])

  <!-- Footer -->
  @include('includes.footer')

</div>

<!-- Modal::create sanadat sarf -->
<div class="modal fade" id="create_sanadat_sarf_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">اضافة سند صرف جديد</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="create_sanadat_sarf_form">

        <div class="modal-body">
          @csrf
          <div class="row">
            <div class="col-xl-3 col-md-12">

              <div class="form-group">
                <label class="form-control-label">اختار التاريخ</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                  </div>
                  <input class="form-control datepicker @error('date_created') is-invalid @enderror" placeholder="اختار التاريخ" type="text" name="date_created" value="{{ date('Y-m-d') }}" required>
                </div>
                @error('date_created')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-3 col-md-12">

              <div class="form-group">
                <label class="form-control-label">رقم السند</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-university text-primary"></i></span>
                  </div>
                  <input type="number" class="form-control @error('number') is-invalid @enderror" name="number" placeholder="رقم السند" value="{{ date('ymdhis') }}" autocomplete="number" required>
                </div>
                @error('number')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-3 col-md-12">

              <div class="form-group">
                <label class="form-control-label">المبلغ</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-book text-danger"></i></span>
                  </div>
                  <input type="number" class="form-control @error('balance') is-invalid @enderror" name="balance" placeholder="المبلغ" value="{{ old('balance') }}" step="0.0001" autocomplete="balance">
                </div>
                @error('balance')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-3 col-md-12">

              <div class="form-group">
                <label class="form-control-label">الصندوق</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i
                        class="fa fa-box text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="box_id"
                    data-live-search="true">
                    @foreach ($boxes as $box)
                    <option value="{{ $box->id }}">{{ $box->name }} ({{ $box->balance }})</option>
                    @endforeach
                  </select>
                </div>
                @error('box_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-4 col-md-12 mt-5 text-center">

              <div class="custom-control custom-radio mb-3 d-inline mr-3">
                <input name="target" class="custom-control-input" id="customRadio11" type="radio" value="providers" checked>
                <label class="custom-control-label" for="customRadio11">الداعمون</label>
              </div>
              <div class="custom-control custom-radio mb-3 d-inline mr-3">
                <input name="target" class="custom-control-input" id="customRadio22" type="radio" value="customers">
                <label class="custom-control-label" for="customRadio22">المستفيدون</label>
              </div>
              <div class="custom-control custom-radio mb-3 d-inline">
                <input name="target" class="custom-control-input" id="customRadio33" type="radio" value="workers">
                <label class="custom-control-label" for="customRadio33">الموظفون</label>
              </div>

            </div>

            <div class="col-xl-4 col-md-12">

              <div class="form-group">
                <label class="form-control-label">الداعمون</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="provider_id" data-live-search="true">
                    @foreach($providers as $provider)
                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('provider_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-4 col-md-12">

              <div class="form-group">
                <label class="form-control-label">المستفيدون</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="customer_id" data-live-search="true">
                    @if(Auth::user()->id == 1)
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
                @error('customer_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-xl-4 col-md-12">
              <!-- Add input here to attach a file -->
              <div class="form-group">
                <label class="form-control-label">ملف المستفيدون</label>
                <div class="custom-file">
                  <input type="file" class="custom-file-input @error('file_attachment') is-invalid @enderror" name="file_attachment" accept=".xls,.xlsx">
                  <label class="custom-file-label" for="file_attachment">اختر ملفاً...</label>
                </div>
                @error('file_attachment')
                <span class="text-danger d-block mt-2">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-4 col-md-12">

              <div class="form-group">
                <label class="form-control-label">الموظفون</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="worker_id" data-live-search="true">
                    @if(Auth::user()->id == 1)
                    @foreach($workers as $worker)
                    <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
                @error('worker_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-12 col-md-12">

              <div class="form-group">
                <label class="form-control-label">الملاحظات</label>
                <div class="input-group">
                  <textarea type="text" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="الملاحظات (في حال كان فارغ : لايوجد)" autocomplete="notes" rows="3">{{ old('notes') }}</textarea>
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
          <button type="submit" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal::sanadat sarf to pdf -->
@include('admin.sanadat_sarf.from_to_pdf')

@include('admin.sanadat_sarf.from_to_xlsx')

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
  // create sanadat sarf form
  $('#create_sanadat_sarf_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/sanadat_sarf/store",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        if (response.status == "success") {
          Swal.fire(
            'تم !',
            response.message,
            'success'
          );
          // refresh table
          get_sanadat_sarf();
          // reset form
          $('#create_sanadat_sarf_form')[0].reset();
          $('#create_sanadat_sarf_modal').modal('hide');
        } else {
          Swal.fire(
            'عفواً !',
            response.message,
            'error'
          );
        }
      },
      error: function(response) {
        Swal.fire(
          '! عفوا',
          response.message,
          'error'
        );
      }
    });
  });
  // show sanadat sarf modal
  $('.from_to_pdf_button').click(function(e) {
    $('#from_to_pdf_modal').modal('show');
  });
  // sanadat sarf to pdf form
  $('#from_to_pdf_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/sanadat_sarf/to_pdf",
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
  $('.from_to_xlsx_button').click(function(e) {
    $('#from_to_xlsx_modal').modal('show');
  });
  // sanadat sarf to pdf form
  $('#from_to_xlsx_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/sanadat_sarf/to_xlsx",
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
  // delete sanadat sarf form
  $("#sanadat_sarf_table").on("click", ".delete_sanadat_sarf_button", function(e) {
    e.preventDefault();
    Swal.fire({
      title: 'هل انت متاكد من حذف هذا السند؟',
      text: "!لا يمكنك التراجع بعد هذه الخطوة",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'نعم ، احذف!',
      cancelButtonText: 'الغاء'
    }).then((result) => {
      if (result.isConfirmed) {
        let id = $(this).data('dataid');
        let _token = $('input[name=_token]').val();
        $.ajax({
          url: "/sanadat_sarf/delete",
          type: "POST",
          data: {
            id: id,
            _token: _token
          },
          success: function(response) {
            if (response.status != 'Not Found') {
              Swal.fire(
                'تم الحذف!',
                'تم حذف السند بنجاح',
                'success'
              );
              // refresh table
              get_sanadat_sarf();
            } else {
              Swal.fire(
                'خطأ',
                'حدث خطأ أثناء حذف السند',
                'error'
              );
            }
          },
          error: function(response) {
            Swal.fire(
              'خطأ',
              'حدث خطأ أثناء حذف السند',
              'error'
            );
          }
        });
      }
    });
  });
  // get all sanadat sarfs
  function get_sanadat_sarf() {
    $.ajax({
      url: "/sanadat_sarfs",
      type: "GET",
      success: function(response) {
        $('#sanadat_sarf_table').html('');
        $('#sanadat_sarf_table').append(response.table);
      },
      error: function(response) {
        Swal.fire(
          'خطأ',
          'حدث خطأ أثناء حذف السند',
          'error'
        );
      }
    });
  }
</script>
@endsection
