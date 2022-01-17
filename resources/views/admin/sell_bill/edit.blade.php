@extends('layouts.main')
@section('content')
  <!-- Header -->
  <div class="header pb-6" style="background-color:#222222;">
    <div class="container-fluid">
      <div class="header-body">
        <div class="row align-items-center py-5">

        </div>
      </div>
    </div>
  </div>
  <!-- Page content -->
  <div class="container-fluid mt--8">

    <div class="row">
      <div class="col-xl-12">

      <div class="row m-auto">
        <div class="col-xl-9 col-md-9 m-auto">
          <div class="card card-stats">
            <!-- Card header -->
            <div class="card-header">
              <div class="row">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">فاتورة بيع</h5>
                  <span class="h2 font-weight-bold mb-0">تعديل</span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                    <i class="fa fa-plus"></i>
                  </div>
                </div>
              </div>
            </div>
            <!-- Card body -->
            <div class="card-body bg-secondary">

              <form action="{{ URL('/sell_bill/update/' . $sell_bill->id) }}" method="POST" id="daily_sell_update_form">
                @csrf
                <div class="row">
                  <div class="col-xl-6 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">اختار التاريخ</label>
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                          </div>
                          <input class="form-control datepicker" placeholder="اختار التاريخ" type="text" value="{{ $sell_bill->date_created }}" disabled>
                      </div>
                      @error('date_created')
                        <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                  </div>

                  <div class="col-xl-6 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">رقم الفاتورة</label>
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <span class="input-group-text" id="basic-addon1"><i class="fa fa-university text-primary"></i></span>
                          </div>
                          <input type="number" class="form-control" placeholder="رقم الفاتورة" value="{{ $sell_bill->number }}" disabled>
                      </div>
                    </div>

                  </div>
                </div>

                <div class="row" id="productsCart">

                  <table class="table table-hover" id="productTable">
                    <thead>
                        <tr>
                            <th class="text-center">الرقم</th>
                            <th class="text-center">رقم المنتج</th>
                            <th class="text-center">اسم المنتج</th>
                            <th class="text-center">الكمية</th>
                            <th class="text-center">سعر البيع</th>
                            <th class="text-center">الاجمالي</th>
                            <th class="text-center">المربح</th>
                            <th class="text-center">حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                      @php $i = 1; @endphp
                      @foreach($sell_bill->sold_product as $product)
                        <tr>
                          <td class="disblay-3 text-center">{{ $i }}</td>
                          <td class="disblay-3 text-center">{{ $product->id }}</td>
                          <td class="disblay-3 text-center">{{ $product->product->name }}</td>
                          <td class="disblay-3 text-center">{{ $product->quantity }}</td>
                          <td class="disblay-3 text-center">{{ $product->sell_price }}</td>
                          <td class="disblay-3 text-center">{{ $product->total_price }}</td>
                          <td class="disblay-3 text-center">{{ $product->profit }}</td>
                          <td class="disblay-3 text-center">
                            <a href="#" data-dataid="{{ $product->id }}" id="delete_product_button" class="btn btn-danger btn-sm" onclick="event.preventDefault(); document.getElementById('delete_product_form').removeAttribute('action'); document.getElementById('delete_product_form').setAttribute('action','{{ url('/daily_sell/delete_product' . '/' . $product->id) }}'); document.getElementById('delete_product_form').submit();"><i class="fa fa-trash"></i></a>
                          </td>
                        </tr>
                        @php $i++; @endphp
                      @endforeach
                    </tbody>
                  </table>
                  <table class="table table-hover mb-2" id="productTableTotal">
                    <thead>
                      <th class="text-center">الاجمالي</th>
                      <th class="text-center" id="total">{{ $sell_bill->total_balance }}</th>
                      <th class="text-center" id="profit">{{ $sell_bill->total_profit }}</th>
                    </thead>
                  </table>

                </div>

                <div class="row">
                  <div class="col-xl-3 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">الاصناف</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-shopping-cart text-info"></i></span>
                              </div>
                              <select class="form-control selectpicker" name="product_id" data-live-search="true" id="productname">
                                @foreach($products as $product)
                                  @if($product->quantity > 0)
                                    <option value="{{ $product->id }}" data-original="{{ $product->original_price }}" title="{{ $product->name }}" onchange="alert(this.value);">{{ $product->name }}</option>
                                  @endif
                                @endforeach
                              </select>
                          </div>
                          @error('product_id')
                            <span class="text-danger">{{ $message }}</span>
                          @enderror
                    </div>

                  </div>

                  <div class="col-xl-3 col-md-12 m-auto" id="product_prices">
                    <a class="btn btn-primary btn-block btm-sm" data-toggle="tooltip" data-placement="top" title="بحث عن السعر" id="search_price_button">
                      <i class="fa fa-search text-white"></i>
                    </a>
                  </div>

                  <div class="col-xl-2 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">الكمية</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-database text-orange"></i></span>
                            </div>
                            <input type="number" id="quantity" class="form-control @error('quantity') is-invalid @enderror" name="quantity" placeholder="الكمية" step="0.0001" value="{{ old('quantity') }}" autocomplete="quantity">
                        </div>
                        @error('quantity')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                  </div>

                  <div class="col-xl-3 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">سعر البيع</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-success"></i></span>
                            </div>
                            <input type="number" id="price" class="form-control @error('original_price') is-invalid @enderror" name="original_price" placeholder="سعر التكلفة" value="{{ old('original_price') }}" step="0.0001" autocomplete="original_price">
                        </div>
                        @error('original_price')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                  </div>

                  <div class="col-xl-1 m-auto">
                    <a class="btn btn-primary btm-sm" data-toggle="tooltip" data-placement="top" title="اضافة" id="updateButton">
                      <i class="fa fa-plus text-white"></i>
                    </a>
                  </div>

                </div>

                <div class="row">
                  <div class="col-xl-6 col-md-12">

                    <div class="mt-5 mb-5 text-center mb-3">
                      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
                        <input name="target" class="custom-control-input" id="customRadio1" type="radio" value="providers" @if($sell_bill->provider_id > 0) checked @endif disabled>
                        <label class="custom-control-label" for="customRadio1">الموردون</label>
                      </div>
                      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
                        <input name="target" class="custom-control-input" id="customRadio2" type="radio" value="customers" @if($sell_bill->customer_id > 0) checked @endif disabled>
                        <label class="custom-control-label" for="customRadio2">الزبائن</label>
                      </div>
                      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
                        <input name="target" class="custom-control-input" id="customRadio3" type="radio" value="workers" @if($sell_bill->worker_id > 0) checked @endif disabled>
                        <label class="custom-control-label" for="customRadio3">الموظفون</label>
                      </div>
                    </div>

                    @if($sell_bill->provider_id > 0)

                    <div class="form-group mt--3">
                      <label class="form-control-label"> الموردون</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                              </div>
                              <input type="text" class="form-control" placeholder="الموردون" value="{{ $sell_bill->provider->name }}" disabled>
                              <input type="hidden" name="provider_id" value="{{ $sell_bill->provider_id }}">
                          </div>
                          @error('provider_id')
                            <span class="text-danger">{{ $message }}</span>
                          @enderror
                    </div>

                    @elseif($sell_bill->customer_id > 0)

                    <div class="form-group mt--3">
                      <label class="form-control-label">الزبائن</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                              </div>
                              <input type="text" class="form-control" placeholder="الزبائن" value="{{ $sell_bill->customer->name }}" disabled>
                              <input type="hidden" name="customer_id" value="{{ $sell_bill->customer_id }}">
                          </div>
                          @error('customer_id')
                            <span class="text-danger">{{ $message }}</span>
                          @enderror
                    </div>

                    @elseif($sell_bill->worker_id > 0)

                    <div class="form-group mt--3">
                      <label class="form-control-label">الموظفون</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                              </div>
                              <input type="text" class="form-control" placeholder="الموظفون" value="{{ $sell_bill->worker->name }}" disabled>
                              <input type="hidden" name="worker_id" value="{{ $sell_bill->worker_id }}">
                          </div>
                          @error('customer_id')
                            <span class="text-danger">{{ $message }}</span>
                          @enderror
                    </div>

                    @endif

                    <div class="form-group">
                      <label class="form-control-label">البيان</label>
                        <div class="input-group">
                            <textarea type="text" class="form-control @error('byan') is-invalid @enderror" name="byan" placeholder="البيان" autocomplete="byan" rows="2">{{ $sell_bill->byan }}</textarea>
                        </div>
                        @error('byan')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                  </div>

                  <div class="col-xl-6 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">خصم</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-yellow"></i></span>
                            </div>
                            <input type="number" id="discount" class="form-control" placeholder="خصم" value="{{ $sell_bill->discount }}" step="0.0001" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                      <label class="form-control-label">المبلغ المدفوع</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-primary"></i></span>
                            </div>
                            <input type="number" id="paid_balance" class="form-control @error('paid_balance') is-invalid @enderror" name="paid_balance" placeholder="المبلغ المدفوع" value="{{ $sell_bill->paid_balance }}" autocomplete="paid_balance" step="0.0001" required>
                        </div>
                        @error('paid_balance')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                      <label class="form-control-label">المبلغ المتبقي</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-danger"></i></span>
                            </div>
                            <input type="number" id="remaining_balance" class="form-control @error('remaining_balance') is-invalid @enderror" name="remaining_balance" placeholder="المبلغ المتبقي" value="{{ $sell_bill->remaining_balance }}" autocomplete="remaining_balance" step="0.0001" required>
                        </div>
                        @error('remaining_balance')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                  </div>

                  <div class="col-xl-12 col-md-12">
                    <input type="hidden" name="tbl" id="tbl">
                  </div>

                  <div class="m-auto">
                    <button class="btn btn-icon btn-primary" type="submit"><i class="fa fa-plus mr-1"></i>تحديث</button>
                    <a href="{{ URL('/sell_bills') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
                  </div>

                </div>
              </form>

              <form action="{{ URL('/sell_bill/delete_product/') }}" method="POST" id="delete_product_form">@csrf</form>

            </div>
          </div>
        </div>
      </div>

      </div>
    </div>

    <!-- Footer -->
    @include('includes.footer')

  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>

  $('#search_price_button').click(function(){
    let val = $("#productname").val();
    console.log(val);
    $.ajax({
      url: '/product/price/' + val,
      type: 'GET',
      success: function(responce){
        if (responce.status == 'success') {
          $('#product_prices').html(responce.price);
        }
      }
    });
  });

  var i = 1;
  var total = {{ $sell_bill->total_balance }};
  var profit = {{ $sell_bill->total_profit }};
  $(document).ready(function () {
    $('#updateButton').click(function () {
        if ($("#productname").val() != null && $("#productname").val() != '' && $("#quantity").val() != '' && $("#quantity").val() != null && $("#price").val() != null && $("#price").val() != '') {
          var tota = $("#quantity").val() * $("#price").val();
          total += tota;
          var profi = $("#quantity").val() * parseFloat($('#product_pr').find(":selected").data('original'));
          profit += (tota - profi);
          $("#productTable tbody").append("<tr>" +
          "<td class='text-center'>" + i + "</td>" +
          "<td class='text-center imp'>" + $("#productname").val() + "</td>" +
          "<td class='text-center'>" + $('#productname').find(":selected").text() + "</td>" +
          "<td class='text-center imp'>" + $("#quantity").val() + "</td>" +
          "<td class='text-center imp'>" + $("#price").val() + "</td>" +
          "<td class='text-center imp'>" + tota + "</td>" +
          "<td class='text-center imp'>" + (tota - profi) + "</td>" +
          "<td class='disblay-3 text-center'><a data-data='" + tota + "' class='btn btn-dark btn-sm text-white' id='delete_product_button'><i class='fa fa-trash'></i></a></td>" +
          "</tr>");
          $("#total").text(total);
          $('#paid_balance').val(total);
          $("#profit").text(profit);
          $("#productname").val("");
          $("#quantity").val("");
          $("#price").val("");
          i = i + 1;
        }
        setTimeout(function() { 
          $( "#daily_sell_update_form" ).submit();
        }, 1000);
    });
    $('#paid_balance').keyup(function () {
      $('#remaining_balance').val($('#paid_balance').val() - total);
    });
    $('#paid_balance').ready(function () {
      $('#remaining_balance').val($('#paid_balance').val() - total);
    });
  });

  $(document).ready(function () {
    var tbl = $('#productTable tr').map(function() {
      return $(this).find('.imp').map(function() {
        return $(this).html();
      }).get();
    }).get();
    $('#tbl').val(tbl);

    $('#updateButton').click(function () {
      var tbl = $('#productTable tr').map(function() {
        return $(this).find('.imp').map(function() {
          return $(this).html();
        }).get();
      }).get();
      $('#tbl').val(tbl);
    });

    $("#productTable").on("click", "#delete_product_button", function() {
      let data = $(this).data('data');
      total = total - data;
      $("#total").text(total);
      $(this).closest("tr").remove();
      var tbl = $('#productTable tr').map(function() {
        return $(this).find('.imp').map(function() {
          return $(this).html();
        }).get();
      }).get();
      $('#tbl').val(tbl);
    });
  });

  </script>
@endsection