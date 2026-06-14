@csrf

<div class="col-xl-6 col-md-12">
    <div class="form-group">
        <label class="form-control-label">رصيد الرسائل</label>
        <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-box"></i></span>
        </div>
        <input type="text" class="form-control @error('sms_balance') is-invalid @enderror" name="reciever" placeholder="رصيد الرسائل" value="{{ $sms_balance }}" autocomplete="sms_balance" disabled>
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
        </div>
        @error('reciever')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="col-xl-12 col-md-12">

    <div class="form-group">
    <label class="form-control-label">الرسالة</label>
    <div class="input-group">
        <textarea type="text" class="form-control @error('sms_body') is-invalid @enderror"
        name="sms_body" placeholder="اكتب الرسالة المراد ارسالها..." autocomplete="sms_body"
        rows="4">{{ $sms_body }}</textarea>
    </div>
    @error('sms_body')
    <span class="text-danger">{{ $message }}</span>
    @enderror
    </div>

</div>