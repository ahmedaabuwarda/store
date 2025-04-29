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

  <!-- All kafeels -->
  <div class="row">
    <div class="col-6 m-auto">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">تعديل كفيل</h3>
            </div>
          </div>
        </div>
        <form action="{{ URL('/kafeel/update') }}" method="POST">
          @csrf
          <div class="row p-4">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-control-label">اسم الكفيل</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم الكفيل" value="{{ $kafeel->name }}" autocomplete="name" required autofocus>
                  <input type="hidden" name="id" value="{{ $kafeel->id }}" required>
                </div>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-control-label">رقم الهوية</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" minlength="9" maxlength="9" class="form-control @error('identity') is-invalid @enderror" name="identity" placeholder="رقم الهوية" value="{{ $kafeel->identity }}" autocomplete="identity">
                </div>
                @error('identity')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-control-label">رقم الهاتف</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" minlength="10" maxlength="10" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="رقم الهاتف" value="{{ $kafeel->phone }}" autocomplete="phone">
                </div>
                @error('phone')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-control-label">الحالة</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i
                        class="fa fa-heart text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="status">
                    <option value="1" @if($kafeel->status == 1) selected @endif>مستمر</option>
                    <option value="0" @if($kafeel->status == 0) selected @endif>خلص</option>
                  </select>
                </div>
                @error('status')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label class="form-control-label">ملاحظات</label>
                <div class="input-group">
                  <textarea type="text" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="(في حال كان فارغ : لايوجد) ملاحظات" autocomplete="notes" rows="3">{{ $kafeel->notes }}</textarea>
                </div>
                @error('notes')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

          </div>

          <div class="modal-footer justify-content-center mt--3">
            <a href="{{ URL('/kafeels') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
            <button class="btn btn-icon btn-primary" type="submit"><i class="fa fa-plus mr-1"></i>حفظ</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  @include('includes.footer')

</div>

@endsection
