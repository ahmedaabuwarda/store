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

  <!-- All permissions -->
  <div class="row">
    <div class="col-6 m-auto">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">تعديل صلاحية ({{ $permission->name }})</h3>
            </div>
          </div>
        </div>
        <form action="{{ URL('/permission/updateOne') }}" method="POST">
          @csrf
          <div class="col-12">

            <div class="form-group">
              <label class="form-control-label">اسم الصلاحية</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                </div>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم الصلاحية" value="{{ $permission->name }}" autocomplete="name" required autofocus>
                <input type="hidden" name="id" value="{{ $permission->id }}" required>
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
                <input type="text" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="وصف الصلاحية" value="{{ $permission->description }}" autocomplete="description" required autofocus>
              </div>
              @error('description')
              <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <div class="modal-footer justify-content-center mt--3">
              <a href="{{ URL('/permissions') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
              <button class="btn btn-icon btn-primary" type="submit"><i class="fa fa-plus mr-1"></i>حفظ</button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  @include('includes.footer')

</div>

@endsection
