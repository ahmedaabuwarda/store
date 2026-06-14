@csrf

<div class="col-xl-6 col-md-12">
  <div class="form-group">
    <label class="form-control-label">رصيد الرسائل</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-box"></i></span>
      </div>
      <input type="text" class="form-control @error('sms_balance') is-invalid @enderror" name="sms_balance" placeholder="رصيد الرسائل" value="{{ $sms_balance }}" autocomplete="sms_balance" disabled>
    </div>
    @error('reciever')
    <span class="text-danger">{{ $message }}</span>
    @enderror
  </div>
</div>

<div class="col-xl-6 col-md-12">
  <div class="form-group">
    <label class="form-control-label">المرسل له</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-box"></i></span>
      </div>
      <input type="text" class="form-control @error('reciever') is-invalid @enderror" name="reciever" placeholder="المرسل له" value="{{ $customer->phone }}" autocomplete="reciever" autofocus>
      <input type="hidden" name="customer_id" value="{{ $customer->id }}">
    </div>
    @error('reciever')
    <span class="text-danger">{{ $message }}</span>
    @enderror
  </div>
</div>

<div class="col-xl-12 col-md-12">

  <div class="form-group">
    <label class="form-control-label">قالب الرسالة</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-user text-primary"></i></span>
      </div>
      <select class="form-control @error('sms_body_id') is-invalid @enderror" name="sms_body_id" id="sms_body_id" required>
        @foreach($smses as $sms)
        <option value="{{ $sms->id }}">{{ $sms->body }}</option>
        @endforeach
      </select>
    </div>
    @error('sms_body_id')
    <span class="text-danger">{{ $message }}</span>
    @enderror
  </div>

</div>

<!-- div for checkbox toselect all customers to send sms -->
 <div class="col-xl-12 col-md-12">
  <div class="form-group">
    <div class="custom-control custom-checkbox">
      <input type="checkbox" class="custom-control-input" id="select_all_customers" name="select_all_customers">
      <label class="custom-control-label" for="select_all_customers">اختر جميع الزبائن</label>
    </div>
  </div>
</div>
