<div class="row">
  <div class="col-xl-6 col-md-12">

    <div class="form-group">
      <label class="form-control-label">اختار التاريخ</label>
      <div class="input-group">
          <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
          </div>
          <input class="form-control datepicker" placeholder="اختار التاريخ" type="text" value="{{ $bill->date_created }}" disabled>
      </div>
    </div>

  </div>  

  <div class="col-xl-6 col-md-12">
    
    <div class="form-group">
      <label class="form-control-label">رقم الفاتورة</label>
      <div class="input-group">
          <div class="input-group-prepend">
              <span class="input-group-text" id="basic-addon1"><i class="fa fa-university text-primary"></i></span>
          </div>
          <input type="number" class="form-control" placeholder="رقم الفاتورة" value="{{ $bill->number }}" disabled>
      </div>
    </div>

  </div>
</div>

<div class="row">

  <table class="table table-hover">
    <thead>
        <tr>
            <th class="text-center">الرقم</th>
            <th class="text-center">اسم المنتج</th>
            <th class="text-center">الكمية</th>
            <th class="text-center">السعر</th>
            <th class="text-center">الاجمالي</th>
        </tr>
    </thead>
    <tbody>
      @php
        $i = 1; $s = 0;
        if($bill->buyed_product != null) {
          $products = $bill->buyed_product; $s = 0;
        } else if($bill->sold_product != null) {
          $products = $bill->sold_product; $s = 1;
        }
      @endphp
      @foreach($products as $product)
      <tr>
        <td class="disblay-3 text-center">{{ $i }}</td>
        <td class="disblay-3 text-center">{{ $product->product->name }}</td>
        <td class="disblay-3 text-center">{{ $product->quantity }}</td>
        <td class="disblay-3 text-center">@if($s == 0) {{ $product->buy_price }} @elseif($s == 1) {{ $product->sell_price }} @endif<i class="fa fa-shekel-sign ml-1"></i></td>
        <td class="disblay-3 text-center">{{ $product->total_price }}<i class="fa fa-shekel-sign ml-1"></i></td>
      </tr>
      @php $i++; @endphp
      @endforeach
    </tbody>
  </table>
  <table class="table table-hover mb-2">
    <thead>
      <th class="text-center">Total | الاجمالي</th>
      <th class="disblay-3 text-center">@if($s == 0) {{ $bill->original_balance }} @elseif($s == 1) {{ $bill->total_balance }} @endif<i class="fa fa-shekel-sign ml-1"></i></th>
    </thead>
  </table>

</div>

<div class="row">
  <div class="col-xl-6 col-md-12">

    <div class="mt-5 mb-5 text-center">
      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
        <input class="custom-control-input" id="customRadio1" type="radio" value="providers" disabled @if($bill->provider_id > 0) checked @endif/>
        <label class="custom-control-label" for="customRadio1">الموردون</label>
      </div>
      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
        <input class="custom-control-input" id="customRadio2" type="radio" value="customers" disabled @if($bill->customer_id > 0) checked @endif/>
        <label class="custom-control-label" for="customRadio2">الزبائن</label>
      </div>
      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
        <input class="custom-control-input" id="customRadio3" type="radio" value="workers" disabled @if($bill->worker_id > 0) checked @endif/>
        <label class="custom-control-label" for="customRadio3">الموظفون</label>
      </div>
    </div>

    <div class="form-group mt--3">
      <label class="form-control-label">المستهلك</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
            </div>
            @if($bill->customer_id > 0)
              <input type="text" class="form-control" placeholder="المستهلك" value="{{ $bill->customer->name }}" disabled>
            @elseif($bill->worker_id > 0)
              <input type="text" class="form-control" placeholder="المستهلك" value="{{ $bill->worker->name }}" disabled>
            @elseif($bill->provider_id > 0)
              <input type="text" class="form-control" placeholder="المستهلك" value="{{ $bill->provider->name }}" disabled>
            @endif
        </div>
    </div>

    <div class="form-group">
      <label class="form-control-label">البيان</label>
        <div class="input-group">
            <textarea type="text" class="form-control" name="byan" placeholder="البيان" autocomplete="byan" rows="2" disabled>{{ $bill->byan }}</textarea>
        </div>
    </div>

  </div>

  <div class="col-xl-6 col-md-12">

    <div class="form-group">
      <label class="form-control-label">خصم</label>
      <div class="input-group">
          <div class="input-group-prepend">
              <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-yellow"></i></span>
          </div>
          <input type="number" class="form-control" placeholder="خصم" value="{{ $bill->discount }}" disabled>
      </div>
    </div>
    
    <div class="form-group">
      <label class="form-control-label">المبلغ المدفوع</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-success"></i></span>
            </div>
            <input type="number" class="form-control" placeholder="المبلغ المدفوع" value="{{ $bill->paid_balance }}" disabled>
        </div>
    </div>

    <div class="form-group">
      <label class="form-control-label">المبلغ المتبقي</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-danger"></i></span>
            </div>
            <input type="number" class="form-control" placeholder="المبلغ المتبقي" value="{{ $bill->remaining_balance }}" disabled>
        </div>
    </div>

  </div>

</div>