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

  <!-- All smses -->
  <div class="row">
    <div class="col-6 m-auto">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">تعديل الرسالة</h3>
            </div>
          </div>
        </div>
        <form action="{{ URL('/sms/update') }}" method="POST">
          @csrf

          <div class="col-md-12 col-sm-12">
            <div class="form-group">
              <label class="form-control-label">القالب</label>
              <div class="input-group">
                <textarea type="text" class="form-control @error('sms_body') is-invalid @enderror" name="sms_body" placeholder="القالب" autocomplete="sms_body" rows="4">{{ $sms->body }}</textarea>
                <input type="hidden" name="sms_body_id" value="{{ $sms->id }}">
              </div>
              @error('sms_body')
              <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
          </div>

          <div class="modal-footer justify-content-center mt--3">
            <a href="{{ URL('/sms') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
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
