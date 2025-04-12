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
                  <h5 class="card-title text-uppercase text-muted mb-0">فاتورة عينيات واردة</h5>
                  <span class="h2 font-weight-bold mb-0">اضافة جديد</span>
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

              <form action="{{ URL('/buy_bill/store') }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-xl-6 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">اختار التاريخ</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                        </div>
                        <input class="form-control datepicker @error('date_created') is-invalid @enderror" placeholder="اختار التاريخ" type="text" name="date_created" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
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
                        <input type="number" class="form-control @error('number') is-invalid @enderror" name="number" placeholder="رقم الفاتورة" value="{{ date('ymdhis') }}" autocomplete="number" required>
                      </div>
                      @error('number')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                  </div>
                </div>

                <div class="row" id="productsCart">

                  <table class="table table-hover" id="productTable">
                    <thead>
                      <tr>
                        <th class="text-center">الرقم</th>
                        <th class="text-center">رقم العينية</th>
                        <th class="text-center">اسم العينية</th>
                        <th class="text-center">الكمية</th>
                        <th class="text-center">سعر التكلفة</th>
                        <th class="text-center">الاجمالي</th>
                        <th class="text-center">حذف</th>
                      </tr>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
                  <table class="table table-hover mb-2" id="productTableTotal">
                    <thead>
                      <th class="text-center">الاجمالي</th>
                      <th class="text-center" id="total"><i class='fa fa-shekel-sign ml-1'></i></th>
                    </thead>
                  </table>

                </div>

                <div class="row">

                  <div class="col-xl-5 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">العينيات</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-shopping-cart text-info"></i></span>
                        </div>
                        <select class="form-control selectpicker" name="product_id" data-live-search="true" id="productname">
                          @foreach($products as $product)
                          <option value="{{ $product->id }}" title="{ {{ $product->quantity }} } {{ $product->name }}" @if($product->quantity > 0) class="text-success" @endif>{ {{ $product->quantity }} } {{ $product->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      @error('product_id')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                  </div>

                  <!-- <div class="col-xl-3 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">الاسعار</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-shopping-cart text-info"></i></span>
                              </div>
                              <select class="form-control selectpicker" name="product_quantity" data-live-search="true" id="product_quantity">

                              </select>
                          </div>
                          @error('product_quantity')
                            <span class="text-danger">{{ $message }}</span>
                          @enderror
                    </div>

                  </div> -->

                  <div class="col-xl-3 col-md-12">

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
                      <label class="form-control-label">سعر التكلفة</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-success"></i></span>
                        </div>
                        <input type="number" id="price" class="form-control @error('original_price') is-invalid @enderror" name="original_price" placeholder="سعر التكلفة" step="0.0001" value="{{ old('original_price') }}" autocomplete="original_price">
                      </div>
                      @error('original_price')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                  </div>

                  <div class="col-xl-1 col-md-12 m-auto">
                    <a class="btn btn-primary btm-sm" data-toggle="tooltip" data-placement="top" title="اضافة" id="updateButton">
                      <i class="fa fa-plus text-white"></i>
                    </a>
                  </div>
                </div>

                <div class="row">
                  <div class="col-xl-6 col-md-12">

                    <div class="mt-5 mb-5 text-center">
                      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
                        <input name="target" class="custom-control-input" id="customRadio1" type="radio" value="providers" checked>
                        <label class="custom-control-label" for="customRadio1">الداعمون</label>
                      </div>
                      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
                        <input name="target" class="custom-control-input" id="customRadio2" type="radio" value="customers">
                        <label class="custom-control-label" for="customRadio2">المستفيدون</label>
                      </div>
                      <div class="custom-control custom-radio mb-3 mr-4 d-inline">
                        <input name="target" class="custom-control-input" id="customRadio3" type="radio" value="workers">
                        <label class="custom-control-label" for="customRadio3">الموظفون</label>
                      </div>
                    </div>

                    <div class="form-group mt--3">
                      <label class="form-control-label">الداعمون</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                        </div>
                        <select class="form-control selectpicker" name="provider_id" data-live-search="true">
                          @foreach($providers as $provider)
                          <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      @error('provider_id')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label class="form-control-label">المستفيدون</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                        </div>
                        <select class="form-control selectpicker" name="customer_id" data-live-search="true">
                          @foreach($customers as $customer)
                          <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      @error('customer_id')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label class="form-control-label">الموظفون</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                        </div>
                        <select class="form-control selectpicker" name="worker_id" data-live-search="true">
                          @foreach($workers as $worker)
                          <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      @error('customer_id')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                  </div>

                  <div class="col-xl-6 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">الصندوق</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                        </div>
                        <select class="form-control selectpicker" name="box_id" data-live-search="true">
                          @foreach($boxes as $box)
                          <option value="{{ $box->id }}">{{ $box->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      @error('box_id')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label class="form-control-label">خصم</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-yellow"></i></span>
                        </div>
                        <input type="number" id="discount" class="form-control @error('discount') is-invalid @enderror" name="discount" placeholder="خصم" value="{{ old('discount') }}" autocomplete="discount" step="0.0001" required>
                      </div>
                      @error('discount')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label class="form-control-label">المبلغ المدفوع</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-shekel-sign text-primary"></i></span>
                        </div>
                        <input type="number" id="paid_balance" class="form-control @error('paid_balance') is-invalid @enderror" name="paid_balance" placeholder="Paid Balance | المبلغ المدفوع" value="{{ old('paid_balance') }}" autocomplete="paid_balance" step="0.0001" required>
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
                        <input type="number" id="remaining_balance" class="form-control @error('remaining_balance') is-invalid @enderror" name="remaining_balance" placeholder="المبلغ المتبقي" value="{{ old('remaining_balance') }}" autocomplete="remaining_balance" step="0.0001" required>
                      </div>
                      @error('remaining_balance')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                  </div>

                  <div class="col-xl-12 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">البيان</label>
                      <div class="input-group">
                        <textarea type="text" class="form-control @error('byan') is-invalid @enderror" name="byan" placeholder="(في حال كان فارغ : لايوجد) البيان" autocomplete="byan" rows="3">{{ old('byan') }}</textarea>
                      </div>
                      @error('byan')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                  </div>
                  <div class="col-xl-12 col-md-12">
                    <input type="hidden" name="tbl" id="tbl">
                  </div>

                  <div class="m-auto">
                    <button class="btn btn-icon btn-primary" type="submit"><i class="fa fa-plus mr-1"></i>حفظ</button>
                    <a href="{{ URL('/buy_bills') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
                  </div>

                </div>
              </form>

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
  var total = 0;
  $(document).ready(function() {
    $('#remaining_balance').val(0);
    $('#paid_balance').val(0);
    $('#discount').val(0);
    $('#updateButton').click(function() {
      if ($("#productname").val() != null && $("#productname").val() != '' && $("#quantity").val() != ' ' && $("#quantity").val() != null && $("#price").val() != null && $("#price").val() != '') {
        var tota = $("#quantity").val() * $("#price").val();
        total += tota;
        $("#productTable tbody").append("<tr>" +
          "<td class='disblay-3 text-center'>" + i + "</td>" +
          "<td class='disblay-3 text-center imp'>" + $("#productname").val() + "</td>" +
          "<td class='disblay-3 text-center'>" + $('#productname').find(":selected").text() + "</td>" +
          "<td class='disblay-3 text-center imp'>" + $("#quantity").val() + "</td>" +
          "<td class='disblay-3 text-center imp'>" + $("#price").val() + "</td>" +
          "<td class='disblay-3 text-center imp'>" + tota + "</td>" +
          "<td class='disblay-3 text-center'><a data-data='" + tota + "' class='btn btn-danger btn-sm text-white' id='delete_product_button'><i class='fa fa-trash'></i></a></td>" +
          "</tr>");
        $("#total").text(total);
        $("#productname").val("");
        $("#quantity").val("");
        $("#paid_balance").val(total);
        $("#price").val("");
        i = i + 1;
      }
    });
    $('#paid_balance').keyup(function() {
      $('#remaining_balance').val((total - $('#discount').val()) - $('#paid_balance').val());
    });
    $('#paid_balance').ready(function() {
      $('#remaining_balance').val((total - $('#discount').val()) - $('#paid_balance').val());
    });
  });
  $(document).ready(function() {
    $('#updateButton').click(function() {
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
