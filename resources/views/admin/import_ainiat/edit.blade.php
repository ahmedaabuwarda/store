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

              <form action="{{ URL('/import_ainiat/update/' . $import_ainiat->id) }}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-xl-4 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">اختار التاريخ</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                        </div>
                        <input class="form-control datepicker" placeholder="اختار التاريخ" type="text" value="{{ $import_ainiat->date_created }}" disabled>
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
                        <input type="number" class="form-control" placeholder="رقم الفاتورة" value="{{ $import_ainiat->number }}" disabled>
                      </div>
                    </div>

                  </div>
                  <div class="col-xl-4 col-md-12">
                    <div class="form-group">
                      <label class="form-control-label">الداعمون</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="الداعمون" value="{{ $import_ainiat->provider->name }}" disabled>
                        <input type="hidden" name="provider_id" value="{{ $import_ainiat->provider_id }}">
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
                      @php $i = 1; @endphp
                      @foreach($import_ainiat->buyed_product as $product)
                      <tr>
                        <td class="disblay-3 text-center">{{ $i }}</td>
                        <td class="disblay-3 text-center">{{ $product->product->id }}</td>
                        <td class="disblay-3 text-center">{{ $product->product->name }}</td>
                        <td class="disblay-3 text-center">{{ $product->quantity }}</td>
                        <td class="disblay-3 text-center">
                          <a href="#" data-dataid="{{ $product->id }}" id="delete_product_button" class="btn btn-danger btn-sm" onclick="event.preventDefault(); document.getElementById('delete_product_form').removeAttribute('action'); document.getElementById('delete_product_form').setAttribute('action','{{ url('/import_ainiat/delete_product' . '/' . $product->id) }}'); document.getElementById('delete_product_form').submit();"><i class="fa fa-trash"></i></a>
                        </td>
                      </tr>
                      @php $i++; @endphp
                      @endforeach
                    </tbody>
                  </table>
                  <table class="table table-hover mb-2" id="productTableTotal">
                    <thead>
                      <th class="text-center">الاجمالي</th>
                      <th class="text-center" id="total">{{ $import_ainiat->original_balance }}<i class='fa fa-shekel-sign ml-1'></i></th>
                    </thead>
                  </table>

                </div>

                <div class="row">
                  <div class="col-xl-5 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">العينيات</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                        </div>
                        <select class="form-control selectpicker" name="product_id" data-live-search="true" id="productname">
                          @foreach($products as $product)
                          <option value="{{ $product->id }}" title="{ {{ $product->quantity }} } {{ $product->name }}" @if($product->quantity > 0) class="text-success" @endif> { {{ $product->quantity }} } {{ $product->name }}</option>
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
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-shopping-cart text-primary"></i></span>
                        </div>
                        <input type="number" id="quantity" class="form-control @error('quantity') is-invalid @enderror" name="quantity" placeholder="الكمية" step="0.0001" value="{{ old('quantity') }}" autocomplete="quantity">
                      </div>
                      @error('quantity')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>

                  </div>

                  <div class="col-xl-2 m-auto">
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
                        <textarea type="text" class="form-control @error('byan') is-invalid @enderror" name="byan" placeholder="الملاحظات" autocomplete="byan" rows="2">{{ $import_ainiat->byan }}</textarea>
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
                    <button class="btn btn-icon btn-primary" type="submit"><i class="fa fa-plus mr-1"></i>تحديث</button>
                    <a href="{{ URL('/import_ainiats') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
                  </div>

                </div>
              </form>

              <form action="{{ URL('/import_ainiat/delete_product/') }}" method="POST" id="delete_product_form">@csrf</form>

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
  var i = 1; var tota = 0;
  $(document).ready(function() {
    $('#updateButton').click(function() {
      if ($("#productname").val() != null && $("#productname").val() != '' && $("#quantity").val() != '' && $("#quantity").val() != null) {
        $("#productTable tbody").append("<tr>" +
          "<td class='text-center'>" + i + "</td>" +
          "<td class='text-center imp'>" + $("#productname").val() + "</td>" +
          "<td class='text-center'>" + $('#productname').find(":selected").text() + "</td>" +
          "<td class='text-center imp'>" + $("#quantity").val() + "</td>" +
          "<td class='disblay-3 text-center'><a data-data='" + tota + "' class='btn btn-dark btn-sm text-white' id='delete_product_button'><i class='fa fa-trash'></i></a></td>" +
          "</tr>");
        $("#productname").val("");
        $("#quantity").val("");
        i = i + 1;
      }
    });
  });
  $(document).ready(function() {
    var tbl = $('#productTable tr').map(function() {
      return $(this).find('.imp').map(function() {
        return $(this).html();
      }).get();
    }).get();
    $('#tbl').val(tbl);
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
