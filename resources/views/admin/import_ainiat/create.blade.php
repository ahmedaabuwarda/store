@extends('layouts.main')
@section('content')
<!-- Header -->
<div class="header pb-6" style="background-color:#222222;">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-5">
        @include('includes.alert')
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

              <form action="{{ URL('/import_ainiat/store') }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-xl-4 col-md-12">

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

                  <div class="col-xl-4 col-md-12">

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

                  <div class="col-xl-4 col-md-12">

                    <div class="form-group">
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

                  <div class="col-xl-5 col-md-12">

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

                  <div class="col-xl-2 col-md-12 m-auto">
                    <a class="btn btn-primary btm-sm" data-toggle="tooltip" data-placement="top" title="اضافة" id="updateButton">
                      <i class="fa fa-plus text-white"></i>
                    </a>
                  </div>
                </div>

                <div class="row">

                  <div class="col-xl-12 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">الملاحظات</label>
                      <div class="input-group">
                        <textarea type="text" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="(في حال كان فارغ : لايوجد) الملاحظات" autocomplete="notes" rows="3">{{ old('notes') }}</textarea>
                      </div>
                      @error('notes')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                  </div>
                  <div class="col-xl-12 col-md-12">
                    <input type="hidden" name="tbl" id="tbl">
                  </div>

                  <div class="m-auto">
                    <a href="{{ URL('/import_ainiats') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
                    <button class="btn btn-icon btn-primary" type="submit"><i class="fa fa-plus mr-1"></i>حفظ</button>
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
  var tota = 0;
  $(document).ready(function() {
    $('#updateButton').click(function() {
      if ($("#productname").val() != null && $("#productname").val() != '' && $("#quantity").val() != ' ' && $("#quantity").val() != null) {
        // var tota = $("#quantity").val() * $("#price").val();
        total += tota;
        $("#productTable tbody").append("<tr>" +
          "<td class='disblay-3 text-center'>" + i + "</td>" +
          "<td class='disblay-3 text-center imp'>" + $("#productname").val() + "</td>" +
          "<td class='disblay-3 text-center'>" + $('#productname').find(":selected").text() + "</td>" +
          "<td class='disblay-3 text-center imp'>" + $("#quantity").val() + "</td>" +
          "<td class='disblay-3 text-center'><a data-data='" + tota + "' class='btn btn-danger btn-sm text-white' id='delete_product_button'><i class='fa fa-trash'></i></a></td>" +
          "</tr>");
        $("#total").text(total);
        $("#productname").val("");
        $("#quantity").val("");
        i = i + 1;
      }
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
      i--;
    });
  });
</script>
@endsection
