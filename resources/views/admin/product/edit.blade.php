<div class="col-xl-12 col-md-12">
  <div class="form-group">
    <label class="form-control-label">اسم العينية</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-box"></i></span>
      </div>
      <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم العينية" value="{{ $product->name }}" autocomplete="name" required autofocus>
      <input type="hidden" id="product_id" name="product_id" value="{{ $product->id }}">
      <input type="hidden" id="movement" name="movement" value="update_product">
    </div>
  </div>
</div>
</div>
<div class="col-xl-12 col-md-12">
  <div class="form-group">
    <label class="form-control-label">النوع</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-heart"></i></span>
      </div>
      <select class="form-control" name="type">
        <option value="{{ $product->type }}">{{ $product->type }}</option>
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
