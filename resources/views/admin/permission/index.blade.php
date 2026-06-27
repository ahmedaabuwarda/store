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

  <!-- All permissions -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col-2">
              <h3 class="mb-0">الصلاحيات</h3>
            </div>
            <div class="col-xl-5 col-md-12 text-center">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="...ابحث عن صلاحية">
            </div>
            <div class="col-xl-5 col-md-12 text-right">
              @can('add_to_box')
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf" data-fromto="0"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
              <a href="{{ url('/permission/upgrade') }}" class="btn text-white btn-primary"><i class="fa fa-plus"></i> تطوير الصلاحيات</a>
              <a class="btn text-white btn-dark" data-toggle="modal" data-target="#create_permission_modal"><i class="fa fa-plus"></i> اضافة صلاحية</a>
              @endcan
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- permission table -->
          <table class="table tablee align-items-center table-flush table-hover" id="permission_table">
            @include('admin.permission.table')
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
        <li class="page-item @if(Request::fullUrl() == URL('/permission?page=' . $p)) active @endif"><a class="page-link" href="{{ URL('/permission?page=' . $p) }}">{{ $p }}</a></li>
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
<div class="modal fade" id="create_permission_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">اضافة صلاحية جديد</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="create_permission_form">
        <div class="modal-body">
          @csrf
          <div class="row">
            <div class="col-12">

              <div class="form-group">
                <label class="form-control-label">اسم الصلاحية</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم الصلاحية" value="{{ old('name') }}" autocomplete="name" required autofocus>
                </div>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

              <div class="form-group">
                <label class="form-control-label">وصف الصلاحية</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="وصف الصلاحية" value="{{ old('description') }}" autocomplete="description" required autofocus>
                </div>
                @error('description')
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

<!-- Modal::customer to pdf -->
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
  // create new customer form
  $('#create_permission_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
      url: "/permission/store",
      type: "POST",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
      success: function(response) {
        if (response.status == "success") {
          Swal.fire(
            'تم!',
            'تم اضافة الصلاحية بنجاح',
            'success'
          );
          // refresh the table
          get_curencies();
          $('#create_permission_form')[0].reset();
          $('#create_permission_modal').modal('hide');
        } else {
          Swal.fire(
            'عفواً',
            'حدث خطأ ما، قد تكون الصلاحية موجوداً بالفعل',
            'error'
          );
        }
      },
      error: function(response) {
        Swal.fire(
          'عفواً',
          'حدث خطأ ما، قد تكون الصلاحية موجوداً بالفعل',
          'error'
        );
      }
    });
  });
  $('#permission_table').on('click', '.edit_button', function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    $.ajax({
      url: "/permission/edit",
      type: "GET",
      data: {
        id: id
      },
      success: function(response) {
        if (response.status == "success") {
          $('#create_permission_modal').modal('show');
          $('#exampleModalLabel').text('تعديل صلاحية');
          $('#create_permission_modal .row').html(response.modal);
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
  // get all customers
  function get_curencies() {
    $.ajax({
      url: "/permissions",
      type: "GET",
      success: function(response) {
        $('#permission_table').html('');
        $('#permission_table').append(response.table);
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
