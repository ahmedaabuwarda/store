<div class="form-group">
    <label class="form-control-label">الاسعار</label>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon1"><i
                    class="fa fa-shopping-cart text-info"></i></span>
        </div>
        <select class="form-control" name="product_pr" data-live-search="true"
            id="product_pr">
            @foreach($quantities as $quantity)
                @if ($quantity->quantity > 0)
                    <option value="{{ $quantity->id }}" title="{ {{ $quantity->buy_price }} &#8362;} { {{ $quantity->quantity }} }" @if($quantity->quantity > 0) class="text-success" @endif data-original="{{ $quantity->buy_price }}">{ {{ $quantity->buy_price }} &#8362;} { {{ $quantity->quantity }} }</option>
                @endif
            @endforeach
        </select>
    </div>
</div>