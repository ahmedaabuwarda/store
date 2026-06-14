@extends('layouts.main')
@section('content')
<!-- Header -->
<div class="header pb-6" style="background-color:#222222;">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-5 m-auto">
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
                  <h5 class="card-title text-uppercase text-muted mb-0">فاتورة عينيات صادرة</h5>
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

              <form action="{{ URL('/export_ainiat/update/' . $export_ainiat->id) }}" method="POST" id="daily_sell_update_form">
                @csrf
                <div class="row">
                  <div class="col-xl-6 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">اختار التاريخ</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                        </div>
                        <input class="form-control datepicker" placeholder="اختار التاريخ" type="text" value="{{ $export_ainiat->date_created }}" disabled>
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
                        <input type="number" class="form-control" placeholder="رقم الفاتورة" value="{{ $export_ainiat->number }}" disabled>
                      </div>
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
                        <th class="text-center">الزبون</th>
                        <th class="text-center">الكمية</th>
                        <th class="text-center">حذف</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php $i = 1; @endphp
                      @foreach($export_ainiat->selective as $selective)
                      <tr>
                        <td class="disblay-3 text-center">{{ $i }}</td>
                        <td class="disblay-3 text-center">{{ $selective->id }}</td>
                        <td class="disblay-3 text-center">{{ $selective->product->name }}</td>
                        <td class="disblay-3 text-center">{{ $selective->customer->name }}</td>
                        <td class="disblay-3 text-center">{{ '1' }}</td>
                        <td class="disblay-3 text-center">
                          <a href="#" data-dataid="{{ $selective->id }}" id="delete_product_button" class="btn btn-danger btn-sm" onclick="event.preventDefault(); document.getElementById('delete_product_form').removeAttribute('action'); document.getElementById('delete_product_form').setAttribute('action','{{ url('/selective/delete' . '/' . $selective->id) }}'); document.getElementById('delete_product_form').submit();"><i class="fa fa-trash"></i></a>
                        </td>
                      </tr>
                      @php $i++; @endphp
                      @endforeach
                    </tbody>
                  </table>
                  <table class="table table-hover mb-2" id="productTableTotal">
                  </table>

                </div>

                <div class="row">
                  <div class="col-xl-12 col-md-12">

                    <div class="form-group">
                      <label class="form-control-label">الملاحظات</label>
                      <div class="input-group">
                        <textarea type="text" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="الملاحظات" autocomplete="notes" rows="2">{{ $export_ainiat->notes }}</textarea>
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
                    <a href="{{ URL('/export_ainiats') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
                    <button class="btn btn-icon btn-primary" type="submit"><i class="fa fa-plus mr-1"></i>تحديث</button>
                  </div>

                </div>
              </form>

              <form action="{{ URL('/export_ainiat/delete_product/') }}" method="POST" id="delete_product_form">@csrf</form>

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
  $('#search_price_button').click(function() {
    let val = $("#productname").val();
    console.log("1");
    $.ajax({
      url: '/product/price/' + val,
      type: 'GET',
      success: function(responce) {
        if (responce.status == 'success') {
          $('#product_prices').html(responce.price);
        }
      }
    });
  });

  var i = 1;
  var total = {{ $export_ainiat->total_balance }};
  var profit = {{ $export_ainiat->total_profit }};
  $(document).ready(function() {
    $('#updateButton').click(function() {
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
        $("#daily_sell_update_form").submit();
      }, 1000);
    });
    $('#paid_balance').keyup(function() {
      $('#remaining_balance').val($('#paid_balance').val() - total);
    });
    $('#paid_balance').ready(function() {
      $('#remaining_balance').val($('#paid_balance').val() - total);
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
