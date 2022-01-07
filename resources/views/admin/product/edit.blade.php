<div class="col-xl-12 col-md-12">

    <div class="form-group">
        <label class="form-control-label">اسم المنتج</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i
                        class="fa fa-box"></i></span>
            </div>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                name="name" placeholder="اسم المنتج" value="{{ $product->name }}"
                autocomplete="name" required autofocus>
            <input type="hidden" id="product_id" name="product_id" value="{{ $product->id }}">
            <input type="hidden" id="movement" name="movement" value="update_product">
        </div>
    </div>

</div>