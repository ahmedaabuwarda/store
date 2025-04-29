@extends('layouts.main')
@section('content')
<!-- Header -->
<div class="header pb-6" style="background-color:#222222;">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-5">
        @include('includes.alert')
      </div>
    </div>
  </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--8">

  <!-- All orphans -->
  <div class="row">
    <div class="col-12 m-auto">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">تعديل يتيم</h3>
            </div>
          </div>
        </div>
        <form action="{{ URL('/orphan/update') }}" method="POST">
          @csrf
          <div class="row p-4">

            <div class="col-md-4">
              <div class="form-group">
                <label class="form-control-label">اسم اليتيم</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم الكفيل" value="{{ $orphan->name }}" autocomplete="name" required autofocus>
                  <input type="hidden" name="id" value="{{ $orphan->id }}" required>
                </div>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label class="form-control-label">رقم الهوية</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" minlength="9" maxlength="9" class="form-control @error('identity') is-invalid @enderror" name="identity" placeholder="رقم الهوية" value="{{ $orphan->identity }}" autocomplete="identity">
                </div>
                @error('identity')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label class="form-control-label">رقم الهاتف</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
                  </div>
                  <input type="text" minlength="10" maxlength="10" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="رقم الهاتف" value="{{ $orphan->phone }}" autocomplete="phone">
                </div>
                @error('phone')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label class="form-control-label">الكفيل</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-shopping-cart text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="kafeel_id" data-live-search="true" id="kafeelname">
                    @foreach($kafeels as $kafeel)
                    <option value="{{ $kafeel->id }}" title="{{ $kafeel->name }}" @if($kafeel->name == $orphan->kafeel->name) selected @endif>{{ $kafeel->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('kafeel_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label class="form-control-label">الوصي</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-shopping-cart text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="wasi_id" data-live-search="true" id="wasiname">
                    @foreach($wasis as $wasi)
                    <option value="{{ $wasi->id }}" title="{{ $wasi->name }}" @if($wasi->name == $orphan->wasi->name) selected @endif>{{ $wasi->name }}</option>
                    @endforeach
                  </select>
                </div>
                @error('wasi_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label class="form-control-label">الحالة</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i
                        class="fa fa-heart text-info"></i></span>
                  </div>
                  <select class="form-control selectpicker" name="status">
                    <option value="1" @if($orphan->status == 1) selected @endif>مستمر</option>
                    <option value="0" @if($orphan->status == 0) selected @endif>خلص</option>
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
                  <textarea type="text" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="(في حال كان فارغ : لايوجد) ملاحظات" autocomplete="notes" rows="3">{{ $orphan->notes }}</textarea>
                </div>
                @error('notes')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>

          <div class="modal-footer justify-content-center mt--3">
            <a href="{{ URL('/orphans') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
            <button class="btn btn-icon btn-primary" type="submit"><i class="fa fa-plus mr-1"></i>حفظ</button>
          </div>

        </form>
      </div>
    </div>
  </div>
  <!-- table to display orphans payments -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">المدفوعات</h3>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center table-flush" id="example">
            <thead class="thead-light">
              <tr>
                <th class="display-3 text-center" scope="col">خيارات</th>
                <th class="display-3 text-center" scope="col">ملاحظات</th>
                <th class="display-3 text-center" scope="col">الصندوق</th>
                <th class="display-3 text-center" scope="col">بواسطة</th>
                <th class="display-3 text-center" scope="col">المبلغ</th>
                <th class="display-3 text-center" scope="col">التاريخ</th>
                <th class="display-3 text-center" scope="col">#</th>
              </tr>
            </thead>
            <tbody>
              @foreach($orphan->payment as $payment)
              <tr>
                <td class="display-3 text-center">
                  <a href="#" data-dataid="{{ $payment->id }}" id="delete_payment_button" class="btn btn-danger btn-sm" onclick="event.preventDefault(); document.getElementById('delete_payment_form').removeAttribute('action'); document.getElementById('delete_payment_form').setAttribute('action','{{ url('/orphan/payment_delete' . '/' . $payment->id) }}'); document.getElementById('delete_payment_form').submit();"><i class="fa fa-trash"></i></a>
                </td>
                <td class="display-3 text-center">{{ $payment->notes }}</td>
                <td class="display-3 text-center">{{ $payment->box->name }}</td>
                <td class="display-3 text-center">{{ $payment->user->name }}</td>
                <td class="display-3 text-center">{{ $payment->amount }}</td>
                <td class="display-3 text-center">{{ $payment->date_created }}</td>
                <td class="display-3 text-center">{{ $loop->iteration }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      </div>

    </div>
    <!-- Footer -->
    @include('includes.footer')

  </div>

  @endsection
