<div class="col-xl-12 col-md-12">
  <div class="form-group">
    <label class="form-control-label">اسم العينية</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-box"></i></span>
      </div>
      <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم العينية" value="{{ old('name') }}" autocomplete="name" required autofocus>
    </div>
    @error('name')
    <span class="text-danger">{{ $message }}</span>
    @enderror
  </div>
</div>
<div class="col-xl-12 col-md-12">
  <div class="form-group">
    <label class="form-control-label">سعر التقسيط</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-box"></i></span>
      </div>
      <input type="text" class="form-control @error('taqseet_price') is-invalid @enderror" name="taqseet_price" placeholder="سعر التقسيط" value="{{ old('taqseet_price') }}" autocomplete="taqseet_price" autofocus>
    </div>
    @error('taqseet_price')
    <span class="text-danger">{{ $message }}</span>
    @enderror
  </div>
</div>
<div class="col-xl-12 col-md-12">
  <div class="form-group">
    <label class="form-control-label">النوع</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-heart"></i></span>
      </div>
      <select class="form-control" name="type" required>
        <option value="وحدة">وحدة</option>
        <option value="غرام">غرام</option>
        <option value="ملم">ملم</option>
        <option value="متر">متر</option>
        <option value="شيكل">شيكل</option>
      </select>
    </div>
    @error('type')
    <span class="text-danger">{{ $message }}</span>
    @enderror
  </div>
</div>
