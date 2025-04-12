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

  <!-- All currencies -->
  <div class="row">
    <div class="col-8 m-auto">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">تعديل مستفيد</h3>
            </div>
          </div>
        </div>
        <form action="{{ URL('/customer/update') }}" method="POST">
          @csrf
          <div class="row p-4">

            <div class="col-md-6 col-sm-12">

              <div class="form-group">
                <label class="form-control-label">اسم المستفيد</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" minlength='6' class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم المستفيد" value="{{ $customer->name }}" autocomplete="name" autofocus>
                  <input type="hidden" name="id" value="{{ $customer->id }}">
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
                  <input type="text" maxlength='9' minlength='9' class="form-control @error('identity') is-invalid @enderror" name="identity" placeholder="رقم الهوية" value="{{ $customer->identity }}" autocomplete="identity" autofocus>
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
                  <input type="text" maxlength='10' minlength='10' class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="رقم الجوال" value="{{ $customer->phone }}" autocomplete="phone" autofocus>
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
                  <input type="text" maxlength='2' class="form-control @error('family_number') is-invalid @enderror" name="family_number" placeholder="عدد افراد الاسرة" value="{{ $customer->family_number }}" autocomplete="family_number" autofocus>
                </div>
                @error('family_number')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                <label class="form-control-label">حالة الاستفادة</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i
                        class="fa fa-heart text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="status">
                    <option value="1" @if($customer->status == 1) selected @endif>مستفيد</option>
                    <option value="0" @if($customer->status == 0) selected @endif>مرشح</option>
                  </select>
                </div>
                @error('status')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                <label class="form-control-label">ملاحظات + العنوان</label>
                <div class="input-group">
                  <textarea type="text" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="ملاحظات + العنوان" autocomplete="notes">{{ $customer->notes }}</textarea>
                </div>
                @error('notes')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <!-- <div class="modal-footer justify-content-center mt--3">
              <button class="btn btn-icon btn-primary" type="submit"><i class="fa fa-plus mr-1"></i>حفظ</button>
              <a href="{{ URL('/customers') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
            </div> -->
          </div>
          <div class="modal-footer justify-content-center mt--3">
            <button class="btn btn-icon btn-primary" type="submit"><i class="fa fa-plus mr-1"></i>حفظ</button>
            <a href="{{ URL('/customers') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
          </div>

        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  @include('includes.footer')

</div>

@endsection
