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

  <!-- All currencies -->
  <div class="row">
    <div class="col-8 m-auto">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">تعديل زبون ({{ $customer->name }})</h3>
            </div>
          </div>
        </div>
        <form action="{{ URL('/customer/update') }}" method="POST">
          @csrf
          <div class="row p-4">

            <div class="col-md-6 col-sm-12">

              <div class="form-group">
                <label class="form-control-label">اسم الزبون</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" minlength='6' class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم الزبون" value="{{ $customer->name }}" autocomplete="name" autofocus>
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
                <label class="form-control-label">المساجد</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-mosque text-primary"></i></span>
                  </div>
                  <select class="form-control @error('mosque_id') is-invalid @enderror" name="mosque_id" id="mosque_id" required>
                    <option value="">اختر المسجد</option>
                    @foreach($mosques as $mosque)
                    <option value="{{ $mosque->id }}" @if(!$customer->mosque) @elseif($mosque->id == $customer->mosque->id) selected @endif>{{ $mosque->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('mosque')
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
                    <option value="1" @if($customer->status == 1) selected @endif>زبون</option>
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
                <label class="form-control-label">العينيات</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-primary"></i></span>
                  </div>
                  <select class="form-control @error('product_id') is-invalid @enderror" name="product_id" id="product_id">
                    <option value="">اختر العينية</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('product')
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

          </div>
          <div class="modal-footer justify-content-center mt--3">
            <a href="{{ URL('/customers') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
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
