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

  <!-- All workers -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">الموظفون</h3>
            </div>
            <div class="col-xl-7 text-right">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="...ابحث عن موظف">
            </div>
            <div class="col-xl-3 text-right">
              @can('add_workers')
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf" data-fromto="-1"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
              <a class="btn text-white" data-toggle="modal" data-target="#show_salaries_modal" style="background-color: #297F87;"><i class="fa fa-chart-bar"></i> الرواتب</a>
              <a class="btn btn-dark  text-white" data-toggle="modal" data-target="#create_worker_modal"><i class="fa fa-plus"></i> موظف</a>
              @endcan
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- Projects table -->
          <table class="table tablee align-items-center table-flush table-hover" id="worker_table">
            @include('admin.worker.table')
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- paginate -->
  @include('includes.pagination', ['paginator' => $workers])

  <!-- Footer -->
  @include('includes.footer')

</div>

<!-- Modal::Add new worker -->
<div class="modal fade" id="create_worker_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">اضافة موظف جديد</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="create_worker_form">
        <div class="modal-body">
          @csrf
          <div class="row">
            <div class="col-12">

              <div class="form-group">
                <label class="form-control-label">اسم الموظف</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم الموظف" value="{{ old('name') }}" autocomplete="name" required autofocus>
                </div>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

              <div class="form-group">
                <label class="form-control-label">ملاحظات + العنوان</label>
                <div class="input-group">
                  <textarea type="text" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="ملاحظات + العنوان (في حال كان فارغ : لايوجد)" autocomplete="notes">{{ old('name') }}</textarea>
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
          <button type="submit" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>اضافة
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal::Show Salaries-->
<div class="modal fade" id="show_salaries_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header align-items-center ">
        <h5 class="modal-title" id="exampleModalCenterTitle">كشف الرواتب لكل الموظفين</h5>
        <div class="col-xl-3 col-md-12 text-right">
          <a href="{{ URL('/salary/to_excel') }}" class="btn btn-success disabled" data-toggle="tooltip" data-placement="top" title="تصدير excel"><i class="fas fa-file-excel fa-lg mr-1"></i></a>
          <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf" data-fromto="0"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>
      <div class="modal-body">
        <!-- All salaries -->
        <div class="row">
          <div class="col-xl-12">
            <div class="card">

              <div class="table-responsive">
                <!-- Projects table -->
                <table class="table tablee align-items-center table-hover" id="salary_table">
                  @include('admin.worker.salaries_table')
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-center mt--3">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-door-open mr-1"></i>الغاء</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal::create salary -->
<div class="modal fade" id="create_salary_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">انشاء راتب موظف</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="create_salary_form">
        <div class="modal-body">
          @csrf
          <div class="row">
            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">اسم الموظف</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" id="worker_name" class="form-control @error('name') is-invalid @enderror" placeholder="اسم الموظف" value="" autocomplete="name" required autofocus>
                  <input type="hidden" name="worker_id" id="worker_id" value="">
                </div>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-6 col-md-12">

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

            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">مجموع السلف</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-danger"></i></span>
                  </div>
                  <input type="number" id="remaining_balance" step="0.0001" class="form-control" placeholder="المبلغ المتبقي" value="" disabled>
                </div>
              </div>

            </div>

            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">راتب اساسي</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-danger"></i></span>
                  </div>
                  <input type="number" id="balance" class="form-control @error('balance') is-invalid @enderror" name="balance" placeholder="راتب اساسي" value="500" step="0.0001" autocomplete="balance" required>
                </div>
                @error('balance')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">صافي الراتب</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-danger"></i></span>
                  </div>
                  <input type="number" id="net_balance" class="form-control @error('net_balance') is-invalid @enderror" name="net_balance" step="0.0001" placeholder="صافي الراتب" autocomplete="net_balance" required>
                </div>
                @error('net_balance')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">الصندوق</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i
                        class="fa fa-heart text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="box_id">
                    @foreach($boxes as $box)
                    <option value="{{ $box->id }}">{{ $box->name }} ({{ $box->balance }})</option>
                    @endforeach
                  </select>
                </div>
                @error('box_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-12 col-md-12">

              <div class="form-group">
                <label class="form-control-label">ملاحظات</label>
                <div class="input-group">
                  <textarea type="text" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="(في حال كان فارغ : لايوجد) ملاحظات" autocomplete="notes" rows="3">{{ old('notes') }}</textarea>
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
          <button type="submit" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>اضافة راتب</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal::from to pdf -->
@include('includes.from_to_pdf')

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
  // create worker form
  $('#create_worker_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/worker/store",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        if (response.status == "success") {
          Swal.fire(
            'تم !',
            'تم اضافة الموظف بنجاح',
            'success'
          );
          // refresh the table
          get_workers();
          $('#create_worker_form')[0].reset();
          $('#create_worker_modal').modal('hide');
        } else {
          Swal.fire(
            'عفوا !',
            'حدث خطأ ما، قد يكون الموظف موجوداً بالفعل',
            'error'
          );
        }
      },
      error: function(response) {
        Swal.fire(
          'عفواً !',
          'حدث خطأ ما، قد يكون الموظف موجوداً بالفعل',
          'error'
        );
      }
    });
  });
  // show worker kashf to pdf modal
  $('#worker_table').on('click', '.from_to_pdf_button', function(e) {
    let from_to = $(this).data('fromto');
    $('#from_to_pdf_modal').modal('show');
    $('#from_to').val(from_to);
  });
  // show worker to pdf modal
  $('.from_to_pdf_button').click(function(e) {
    let from_to = $(this).data('fromto');
    $('#from_to_pdf_modal').modal('show');
    $('#from_to').val(from_to);
  });
  // create worker to pdf form
  $('#from_to_pdf_form').submit(function(e) {
    e.preventDefault();
    let from = $('input[name="from"]').val();
    let to = $('input[name="to"]').val();
    let from_to = $('input[name="from_to"]').val();
    let _token = $('input[name="_token"]').val();
    // from to == 0 mean: for all salaries
    if (from_to == '0') {
      $.ajax({
        url: "/salary/to_pdf",
        type: "POST",
        data: {
          from: from,
          to: to,
          _token: _token
        },
        success: function(response) {
          $('#worker_to_pdf_modal').modal('hide');
        }
      });
      // from to == -1 means: for all workers
    } else if (from_to == '-1') {
      $.ajax({
        url: "/worker/to_pdf",
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
        url: "/worker/kashf_to_pdf",
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
  // show salary modal
  $('#worker_table').on('click', '.create_salary_button', function(e) {
    let id = $(this).data('dataid');
    $.ajax({
      url: "/salary/create",
      type: "POST",
      data: {
        id: id,
        _token: $('input[name="_token"]').val()
      },
      success: function(response) {
        if (response.status == 'success') {
          $('#create_salary_modal').modal('show');
          $('#create_salary_modal #worker_id').val(response.worker[0].id);
          $('#create_salary_modal #worker_name').val(response.worker[0].name);
          $('#create_salary_modal #remaining_balance').val(response.worker[0].balance);
          $('#remaining_balance').ready(function() {
            var input1 = parseInt($('#remaining_balance').val());
            var input2 = parseInt($('#balance').val());
            $('#net_balance').val(input1 + input2);
          });
          $('#remaining_balance').keyup(function() {
            var input1 = parseInt($('#remaining_balance').val());
            var input2 = parseInt($('#balance').val());
            $('#net_balance').val(input1 + input2);
          });
          $('#balance').ready(function() {
            var input1 = parseInt($('#remaining_balance').val());
            var input2 = parseInt($('#balance').val());
            $('#net_balance').val(input1 + input2);
          });
          $('#balance').keyup(function() {
            var input1 = parseInt($('#remaining_balance').val());
            var input2 = parseInt($('#balance').val());
            $('#net_balance').val(input1 + input2);
          });
        } else {
          Swal.fire(
            '!عفواً',
            '!حدث خطأ ما',
            'error'
          );
        }
      }
    });
  });
  // create new salary modal
  $('#create_salary_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/salary/store",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        if (response.status == 'success') {
          $('#create_salary_modal').modal('hide');
          Swal.fire(
            '!تم',
            response.message,
            'success'
          );
          get_workers();
        } else {
          Swal.fire(
            '!عفواً',
            response.message,
            'error'
          );
        }
      },
      error: function(response) {
        Swal.fire(
          '!عفواً',
          response.message,
          'error'
        );
      }
    });
    $('#create_salary_form')[0].reset();
    $('#create_salary_modal').modal('hide');
  });
  // get all workers and salaries
  function get_workers() {
    $.ajax({
      url: "/workers",
      type: "GET",
      success: function(response) {
        $('#worker_table').html('');
        $('#worker_table').append(response.table);
        $('#salary_table').html('');
        $('#salary_table').append(response.salaries_table);
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
