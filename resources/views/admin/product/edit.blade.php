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
<div class="col-xl-12 col-md-12">
  <div class="form-group">
    <label class="form-control-label">سعر التقسيط</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-box"></i></span>
      </div>
      <input type="text" class="form-control @error('taqseet_price') is-invalid @enderror" name="taqseet_price" placeholder="سعر التقسيط" value="{{ $product->taqseet_price }}" autocomplete="taqseet_price" autofocus>
    </div>
    @error('taqseet_price')
    <span class="text-danger">{{ $message }}</span>
    @enderror
  </div>
</div>
<div class="col-xl-12 col-md-12">
  <div class="form-group">
    <label class="form-control-label">الكمية</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-box"></i></span>
      </div>
      <input type="text" class="form-control @error('quantity') is-invalid @enderror" name="quantity" placeholder="الكمية" value="{{ $product->quantity }}" autocomplete="quantity" required autofocus>
    </div>
  </div>
</div>
