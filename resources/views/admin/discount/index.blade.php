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

  <!-- All discounts -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">المصاريف</h3>
            </div>
            <div class="col-xl-8 col-md-12 text-center">
              <input type="text" name="search_input" id="search_input" class="form-control"
                placeholder="...ابحث عن خصم او مصروف">
            </div>
            <div class="col-xl-3 col-md-12 text-right">
              <button class="btn btn-danger" data-toggle="modal" data-target="#from_to_pdf_modal"><i
                  class="fas fa-file-pdf fa-lg mr-1"></i></button>
              <a class="btn btn-dark text-white position-relative" data-toggle="modal" data-target="#create_discount_modal">
                <span class="badge text-white bg-success mr-1">{{ $box[0]->remaining }} &#8362;
                </span> مصروف
              </a>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- Projects table -->
          <table class="table tablee align-items-center table-flush table-hover" id="discount_table">
            @include('admin.discount.table')
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- paginate -->
  <nav aria-label="..." class="justify-content-center">
    <ul class="pagination justify-content-center">
      <li class="page-item">
        <a class="page-link" href="{{ Request::fullUrl() }}" tabindex="-1">
          <i class="fa fa-angle-left"></i>
          <span class="sr-only">Previous</span>
        </a>
      </li>
      @for ($p = 1; $p <= $pages; $p++)
        <li class="page-item @if (Request::fullUrl() == URL('/discounts?page=' . $p)) active @endif"><a class="page-link"
          href="{{ URL('/discounts?page=' . $p) }}">{{ $p }}</a></li>
        @endfor
        <li class="page-item">
          <a class="page-link" href="{{ Request::fullUrl() }}">
            <i class="fa fa-angle-right"></i>
            <span class="sr-only">Next</span>
          </a>
        </li>
    </ul>
  </nav>

  <!-- Footer -->
  @include('includes.footer')

</div>

<!-- Modal::create discount -->
<div class="modal fade" id="create_discount_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">اضافة خصم او مصروف جديد</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="create_discount_form">
        <div class="modal-body">
          @csrf
          <div class="row">
            <div class="col-md-6">

              <div class="form-group">
                <label class="form-control-label">المبلغ</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i
                        class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="number" class="form-control @error('balance') is-invalid @enderror"
                    name="balance" placeholder="المبلغ" value="{{ old('balance') }}"
                    autocomplete="balance" required autofocus>
                </div>
                @error('balance')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">اختار التاريخ</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i
                        class="fa fa-calendar text-success"></i></span>
                  </div>
                  <input class="form-control datepicker @error('date_created') is-invalid @enderror"
                    placeholder="اختار التاريخ" type="text" name="date_created"
                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                </div>
                @error('date_created')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-6 col-md-6">

              <div class="form-group">
                <label class="form-control-label">بواسطة</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i
                        class="fa fa-heart text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="done_by">
                    @foreach($users as $user)
                    <option value="{{ $user->name }}">{{ $user->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('done_by')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-6 col-md-6">

              <div class="form-group">
                <label class="form-control-label">الصندوق</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i
                        class="fa fa-heart text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="box_id">
                    @foreach($boxes as $box)
                    <option value="{{ $box->id }}">{{ $box->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('box_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-md-12">

              <div class="form-group">
                <label class="form-control-label">ملاحظات</label>
                <div class="input-group">
                  <textarea type="text" class="form-control @error('notes') is-invalid @enderror"
                    name="notes" placeholder="ملاحظات"
                    autocomplete="notes">{{ old('name') }}</textarea>
                </div>
                @error('notes')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

          </div>
        </div>

        <div class="modal-footer justify-content-center mt--3">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i
              class="fa fa-door-open mr-1"></i>الغاء</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>اضافة
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal::discount to pdf -->
@include('admin.discount.from_to')

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
  // create discount form
  $('#create_discount_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/discount/store",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        if (response.status == "success") {
          // refresh the table
          get_discounts();
          $('#create_discount_form')[0].reset();
          $('#create_discount_modal').modal('hide');
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
      },
      error: function(response) {
        Swal.fire(
          '! عفواً',
          response.message,
          'error'
        );
      }
    });
  });
  // create discount to pdf form
  $('#from_to_pdf_form').submit(function(e) {
    e.preventDefault();
    let from = $('input[name="from"]').val();
    let to = $('input[name="to"]').val();
    let done_by = $('#done_by').val();
    let _token = $('input[name="_token"]').val();
    $.ajax({
      url: "/discount/to_pdf",
      type: "POST",
      data: {
        from: from,
        to: to,
        done_by: done_by,
        _token: _token
      },
      success: function(response) {
        $('#from_to_pdf_modal').modal('hide');
      }
    });
    $('#from_to_pdf_form')[0].reset();
    $('#from_to_pdf_modal').modal('hide');
  });
  // get all discounts
  function get_discounts() {
    $.ajax({
      url: "/discounts",
      type: "GET",
      success: function(response) {
        $('#discount_table').html('');
        $('#discount_table').append(response.table);
      },
      error: function(response) {
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
