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

  <!-- All users -->
  <div class="row">
    <div class="col-6 m-auto">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">تعديل مستخدم</h3>
            </div>
          </div>
        </div>
        <form action="{{ URL('/user/update') }}" method="POST">
          @csrf
          <div class="col-12">

            <div class="form-group">
              <label class="form-control-label">اسم المستخدم</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                </div>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم العملة" value="{{ $user->name }}" autocomplete="name" required autofocus>
                <input type="hidden" name="id" value="{{ $user->id }}" required>
              </div>
              @error('name')
              <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group">
              <label class="form-control-label">البريد الالكتروني</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                </div>
                <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="البريد الالكتروني" value="{{ $user->email }}" autocomplete="email" required>
              </div>
              @error('email')
              <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group">
              <label class="form-control-label">كلمة المرور</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                </div>
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="كلمة المرور" value="" autocomplete="password" required>
              </div>
              @error('password')
              <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <div class="modal-footer justify-content-center mt--3">
              <a href="{{ URL('/home') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
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
