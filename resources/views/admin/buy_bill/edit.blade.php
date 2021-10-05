@extends('layouts.main')
@section('content')
  <!-- Header -->
  <div class="header pb-6" style="background-color:#420516;">
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
                  <h5 class="card-title text-uppercase text-muted mb-0">فاتورة شراء</h5>
                  <span class="h2 font-weight-bold mb-0">تعديل</span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                    <i class="fa fa-pen"></i>
                  </div>
                </div>
              </div>
            </div>
            <!-- Card body -->
            <div class="card-body bg-secondary">

              <form action="{{ URL('/buy_bill/update/' . $buy_bill->id) }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-xl-6 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">اختار التاريخ</label>
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                          </div>
                          <input class="form-control datepicker" placeholder="اختار التاريخ" type="text" value="{{ $buy_bill->date_created }}" disabled>
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
                          <input type="number" class="form-control" placeholder="رقم الفاتورة" value="{{ $buy_bill->number }}" disabled>
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
                            <th class="text-center">سعر التكلفة</th>
                            <th class="text-center">الاجمالي</th>
                            <th class="text-center">حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                      @php $i = 1; @endphp
                      @foreach($buy_bill->buyed_product as $product)
                        <tr>
                          <td class="disblay-3 text-center">{{ $i }}</td>
                          <td class="disblay-3 text-center">{{ $product->product->id }}</td>
                          <td class="disblay-3 text-center">{{ $product->product->name }}</td>
                          <td class="disblay-3 text-center">{{ $product->quantity }}</td>
                          <td class="disblay-3 text-center">{{ $product->buy_price }}</td>
                          <td class="disblay-3 text-center">{{ $product->total_price }}</td>
                          <td class="disblay-3 text-center">
                            <a href="#" data-dataid="{{ $product->id }}" id="delete_product_button" class="btn btn-danger btn-sm" onclick="event.preventDefault(); document.getElementById('delete_product_form').removeAttribute('action'); document.getElementById('delete_product_form').setAttribute('action','{{ url('/buy_bill/delete_product' . '/' . $product->id) }}'); document.getElementById('delete_product_form').submit();"><i class="fa fa-trash"></i></a>
                          </td>
                        </tr>
                        @php $i++; @endphp
                      @endforeach
                    </tbody>
                  </table>
                  <table class="table table-hover mb-2" id="productTableTotal">
                    <thead>
                      <th class="text-center">الاجمالي</th>
                      <th class="text-center" id="total">{{ $buy_bill->original_balance }}<i class='fa fa-shekel-sign ml-1'></i></th>
                    </thead>
                  </table>

                </div>

                <div class="row">
                  <div class="col-xl-5 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">الاصناف</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                              </div>
                              <select class="form-control selectpicker" name="product_id" data-live-search="true" id="productname">
                                @foreach($products as $product)
                                  <option value="{{ $product->id }}" title="{ {{ $product->original_price }} } { {{ $product->quantity }} } {{ $product->name }}" @if($product->quantity > 0) class="text-success" @endif>{ {{ $product->original_price }} } { {{ $product->quantity }} } {{ $product->name }}</option>
                                @endforeach
                              </select>
                          </div>
                          @error('product_id')
                            <span class="text-danger">{{ $message }}</span>
                          @enderror
                    </div>

                  </div>

                  <div class="col-xl-3 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">الكمية</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-shopping-cart text-primary"></i></span>
                            </div>
                            <input type="number" id="quantity" class="form-control @error('quantity') is-invalid @enderror" name="quantity" placeholder="الكمية" value="{{ old('quantity') }}" autocomplete="quantity">
                        </div>
                        @error('quantity')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                  </div>

                  <div class="col-xl-3 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">سعر التكلفة</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-success"></i></span>
                            </div>
                            <input type="number" id="price" class="form-control @error('original_price') is-invalid @enderror" name="original_price" placeholder="سعر التكلفة" value="{{ old('original_price') }}" autocomplete="original_price">
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

                    <div class="mt-5 mb-5 text-center">
                      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
                        <input name="target" class="custom-control-input" id="customRadio1" type="radio" value="providers" @if($buy_bill->provider_id > 0) checked @endif disabled>
                        <label class="custom-control-label" for="customRadio1">الموردون</label>
                      </div>
                      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
                        <input name="target" class="custom-control-input" id="customRadio2" type="radio" value="customers" @if($buy_bill->customer_id > 0) checked @endif disabled>
                        <label class="custom-control-label" for="customRadio2">الزبائن</label>
                      </div>
                      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
                        <input name="target" class="custom-control-input" id="customRadio3" type="radio" value="workers" @if($buy_bill->worker_id > 0) checked @endif disabled>
                        <label class="custom-control-label" for="customRadio3">الموظفون</label>
                      </div>
                    </div>

                    @if($buy_bill->provider_id > 0)

                    <div class="form-group mt--3">
                      <label class="form-control-label">الموردون</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                              </div>
                              <input type="text" class="form-control" placeholder="الموردون" value="{{ $buy_bill->provider->name }}" disabled>
                              <input type="hidden" name="provider_id" value="{{ $buy_bill->provider_id }}">
                          </div>
                          @error('provider_id')
                            <span class="text-danger">{{ $message }}</span>
                          @enderror
                    </div>

                    @elseif($buy_bill->customer_id > 0)

                    <div class="form-group mt--3">
                      <label class="form-control-label">Customers | الزبائن</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                              </div>
                              <input type="text" class="form-control" placeholder="Customers | الزبائن" value="{{ $buy_bill->customer->name }}" disabled>
                              <input type="hidden" name="customer_id" value="{{ $buy_bill->customer_id }}">
                          </div>
                          @error('customer_id')
                            <span class="text-danger">{{ $message }}</span>
                          @enderror
                    </div>

                    @elseif($buy_bill->worker_id > 0)

                    <div class="form-group mt--3">
                      <label class="form-control-label">Workers | الموظفون</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                              </div>
                              <input type="text" class="form-control" placeholder="Customers | الزبائن" value="{{ $buy_bill->worker->name }}" disabled>
                              <input type="hidden" name="worker_id" value="{{ $buy_bill->worker_id }}">
                          </div>
                          @error('customer_id')
                            <span class="text-danger">{{ $message }}</span>
                          @enderror
                    </div>

                    @endif

                    <div class="form-group">
                      <label class="form-control-label">البيان</label>
                        <div class="input-group">
                            <textarea type="text" class="form-control @error('byan') is-invalid @enderror" name="byan" placeholder="البيان" autocomplete="byan" rows="2">{{ $buy_bill->byan }}</textarea>
                        </div>
                        @error('byan')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                  </div>

                  <div class="col-xl-6 col-md-12">

                  <div class="form-group">
                      <label class="form-control-label">(قديم لايحسب)خصم</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-yellow"></i></span>
                            </div>
                            <input type="number" id="discount" class="form-control" placeholder="خصم" value="{{ $buy_bill->discount }}" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                      <label class="form-control-label">المبلغ المدفوع</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-primary"></i></span>
                            </div>
                            <input type="number" id="paid_balance" class="form-control @error('paid_balance') is-invalid @enderror" name="paid_balance" placeholder="المبلغ المدفوع" value="{{ $buy_bill->paid_balance }}" autocomplete="paid_balance" required>
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
                            <input type="number" id="remaining_balance" class="form-control @error('remaining_balance') is-invalid @enderror" name="remaining_balance" placeholder="المبلغ المتبقي" value="{{ $buy_bill->remaining_balance }}" autocomplete="remaining_balance" required>
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
                    <a href="{{ URL('/buy_bills') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
                  </div>

                </div>
              </form>

              <form action="{{ URL('/buy_bill/delete_product/') }}" method="POST" id="delete_product_form">@csrf</form>

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
  var i = 1;
  var total = {{ $buy_bill->original_balance }};
  $(document).ready(function () {
    $('#updateButton').click(function () {
        if ($("#productname").val() != null && $("#productname").val() != '' && $("#quantity").val() != '' && $("#quantity").val() != null && $("#price").val() != null && $("#price").val() != '') {
          var tota = $("#quantity").val() * $("#price").val();
          total += tota;
          $("#productTable tbody").append("<tr>" +
          "<td class='text-center'>" + i + "</td>" +
          "<td class='text-center imp'>" + $("#productname").val() + "</td>" +
          "<td class='text-center'>" + $('#productname').find(":selected").text() + "</td>" +
          "<td class='text-center imp'>" + $("#quantity").val() + "</td>" +
          "<td class='text-center imp'>" + $("#price").val() + "</td>" +
          "<td class='text-center imp'>" + tota + "</td>" +
          "<td class='disblay-3 text-center'><a data-data='" + tota + "' class='btn btn-dark btn-sm text-white' id='delete_product_button'><i class='fa fa-trash'></i></a></td>" +
          "</tr>");
          $("#total").text(total);
          $("#productname").val("");
          $("#quantity").val("");
          $("#price").val("");
          i = i + 1;
        }
    });
    $('#paid_balance').keyup(function () {
      $('#remaining_balance').val(total - $('#paid_balance').val());
    });
    $('#paid_balance').ready(function () {
      $('#remaining_balance').val(total - $('#paid_balance').val());
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
