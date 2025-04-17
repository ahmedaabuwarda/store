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

  <!-- All currencies -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col-2">
              <h3 class="mb-0">الصندوق</h3>
            </div>
            <div class="col-xl-5 col-md-6 text-center">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="...ابحث عن صندوق">
            </div>
            <div class="col-xl-5 col-md-6 text-right">
              <button class="btn btn-success from_to_xlsx_button" data-toggle="tooltip" data-placement="top" title="تصدير xlsx" data-fromto="0"><i class="fas fa-file-excel fa-lg mr-1"></i></button>
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf" data-fromto="0"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
              <a class="btn text-white btn-primary" data-toggle="modal" data-target="#convert_from_box_to_box_modal"><i class="fa fa-book">
                </i> تحويل بين الصناديق</a>
              <a class="btn text-white btn-dark" data-toggle="modal" data-target="#create_box_modal"><i class="fa fa-plus"></i> اضافة صندوق</a>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- currency table -->
          <table class="table tablee align-items-center table-flush table-hover" id="box_table">
            @include('admin.box.table')
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
        <li class="page-item @if(Request::fullUrl() == URL('/boxes?page=' . $p)) active @endif"><a class="page-link" href="{{ URL('/boxes?page=' . $p) }}">{{ $p }}</a></li>
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

<!-- Modal::create new boc -->
<div class="modal fade" id="create_box_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">اضافة صندوق جديد</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="create_box_form">
        <div class="modal-body">
          @csrf
          <div class="row">
            <div class="col-12">

              <div class="form-group">
                <label class="form-control-label">اسم الصندوق</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم الصنندوق" value="{{ old('name') }}" autocomplete="name" required autofocus>
                </div>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

              <div class="form-group">
                <label class="form-control-label">عملة الصندوق</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <select class="form-control @error('currency_id') is-invalid @enderror" name="currency_id" id="currency_id" required>
                    <option value="">اختر العملة</option>
                    @foreach($currencies as $currency)
                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('currency')
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

<div class="modal fade" id="convert_from_box_to_box_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">تحويل بين الصناديق</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="convert_from_box_to_box_form">
        <div class="modal-body">
          @csrf
          <div class="row">
            <div class="col-6">

              <div class="form-group">
                <label class="form-control-label">الى الصندوق</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <select class="form-control @error('box_to') is-invalid @enderror" name="box_to" id="box_to" required>
                    <option value="">اختر الصندوق</option>
                    @foreach($boxes as $box)
                    <option value="{{ $box->id }}">{{ $box->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('box_to')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>


              <div class="form-group">
                <label class="form-control-label">سعر التحويل</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="number" class="form-control @error('convert_price') is-invalid @enderror" step="0.001" name="convert_price" placeholder="سعر التحويل" value="{{ old('convert_price') }}" autocomplete="convert_price" required autofocus>
                </div>
                @error('convert_price')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-6">

              <div class="form-group">
                <label class="form-control-label">من الصندوق</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <select class="form-control @error('box_from') is-invalid @enderror" name="box_from" id="box_from" required>
                    <option value="">اختر الصندوق</option>
                    @foreach($boxes as $box)
                    <option value="{{ $box->id }}">{{ $box->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('box_from')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

              <div class="form-group">
                <label class="form-control-label">مبلغ التحويل</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="number" class="form-control @error('balance') is-invalid @enderror" name="balance" step="0.001" placeholder="مبلغ التحويل" value="{{ old('balance') }}" autocomplete="balance" required autofocus>
                </div>
                @error('balance')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

              <div class="form-group">
                <label class="form-control-label">العميلة</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <select class="form-control @error('operation') is-invalid @enderror" name="operation" id="operation" required>
                    <option value="">اختر العملية</option>
                    <option value="*">ضرب</option>
                    <option value="/">قسمة</option>
                  </select>
                </div>
                @error('operation')
                <span class="text-danger">{{ $operation }}</span>
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

<!-- Modal::box to pdf -->
<div class="modal fade" id="from_to_pdf_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">تصدير من - الى pdf</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="from_to_pdf_form">
        <div class="modal-body">
          @csrf
          <div class="row">

            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">من</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                  </div>
                  <input class="form-control datepicker @error('from') is-invalid @enderror" placeholder="من" type="text" name="from" value="{{ date('Y-m-01') }}" required>
                </div>
                @error('from')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">الى</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                  </div>
                  <input class="form-control datepicker @error('to') is-invalid @enderror" placeholder="الى" type="text" name="to" value="{{ date('Y-m-d') }}" required>
                </div>
                @error('to')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-12 col-md-12">

              <div class="form-group">
                <label class="form-control-label">الصندوق</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i
                        class="fa fa-heart text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="box_id">
                    <option value="all">كل الصناديق</option>
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

            <input type="hidden" name="from_to" id="from_to" value="">

          </div>
        </div>

        <div class="modal-footer justify-content-center mt--3">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-door-open mr-1"></i>الغاء</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>تصدير pdf
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal:: to xlsx -->
<div class="modal fade" id="from_to_xlsx_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">تصدير من - الى xlsx</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="from_to_xlsx_form">
        <div class="modal-body">
          @csrf
          <div class="row">

            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">من</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                  </div>
                  <input class="form-control datepicker @error('from') is-invalid @enderror" placeholder="من" type="text" name="from" value="{{ date('Y-m-01') }}" required>
                </div>
                @error('from')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">الى</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                  </div>
                  <input class="form-control datepicker @error('to') is-invalid @enderror" placeholder="الى" type="text" name="to" value="{{ date('Y-m-d') }}" required>
                </div>
                @error('to')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-12 col-md-12">

              <div class="form-group">
                <label class="form-control-label">الصندوق</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i
                        class="fa fa-heart text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="box_id">
                    <option value="all">كل الصناديق</option>
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

            <input type="hidden" name="from_to" id="from_to" value="">

          </div>
        </div>

        <div class="modal-footer justify-content-center mt--3">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-door-open mr-1"></i>الغاء</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>تصدير pdf
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

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
  // create new box form
  $('#create_box_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/box/store",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        if (response.status == "success") {
          Swal.fire(
            'تم!',
            'تم اضافة الصندوق بنجاح',
            'success'
          );
          // refresh the table
          get_boxes();
          $('#create_box_form')[0].reset();
          $('#create_box_modal').modal('hide');
        } else {
          Swal.fire(
            'عفواً',
            'حدث خطأ ما، قد يكون الصندوق موجوداً بالفعل',
            'error'
          );
        }
      },
      error: function(response) {
        Swal.fire(
          'عفواً',
          'حدث خطأ ما، قد يكون الصندوق موجوداً بالفعل',
          'error'
        );
      }
    });
  });
  $('#convert_from_box_to_box_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/box/convert",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        if (response.status == "success") {
          Swal.fire(
            'تم!',
            'تم التحويل الى الصندوق بنجاح',
            'success'
          );
          // refresh the table
          get_boxes();
          $('#convert_from_box_to_box_form')[0].reset();
          $('#convert_from_box_to_box_modal').modal('hide');
        } else {
          Swal.fire(
            'عفواً',
            response.error,
            'error'
          );
        }
      },
      error: function(response) {
        Swal.fire(
          'عفواً',
          'حدث خطأ ما اثناء عملية التحويل!',
          'error'
        );
      }
    });
  });
  $('#box_table').on('click', '.edit_button', function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    $.ajax({
      url: "/box/edit",
      type: "GET",
      data: {
        id: id
      },
      success: function(response) {
        if (response.status == "success") {
          $('#create_box_modal').modal('show');
          $('#exampleModalLabel').text('تعديل صندوق');
          $('#create_box_modal .row').html(response.modal);
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
  // show customer to pdf modal
  $('.from_to_pdf_button').click(function(e) {
    let from_to = $(this).data('fromto');
    $('#from_to_pdf_modal').modal('show');
    $('#from_to').val(from_to);
  });
  // create customer to pdf form
  $('#from_to_pdf_form').submit(function(e) {
    e.preventDefault();
    var data = new FormData(this);
    $.ajax({
      url: "/box/to_pdf",
      type: "POST",
      processData: false,
      contentType: false,
      cache: false,
      data: data,
      success: function(response) {
        $('#from_to_pdf_modal').modal('hide');
      }
    });
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
    var data = new FormData(this);
    $.ajax({
      url: "/box/to_xlsx",
      type: "POST",
      processData: false,
      contentType: false,
      cache: false,
      data: data,
      success: function(response) {
        $('#from_to_xlsx_modal').modal('hide');
      }
    });

    $('#from_to_xlsx_form')[0].reset();
    $('#from_to_xlsx_modal').modal('hide');
  });
  // get all customers
  function get_boxes() {
    $.ajax({
      url: "/boxes",
      type: "GET",
      success: function(response) {
        $('#box_table').html('');
        $('#box_table').append(response.table);
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
