@extends('layouts.main')
@section('content')
<!-- Header -->
<div class="header pb-6" style="background-color:#222222;">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">

      </div>
    </div>
  </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--6">

  <!-- All products -->
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">نتائج البحث</h3>
            </div>
            <div class="col-xl-8 col-md-12 text-center">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="...ابحث في نتائج البحث">
            </div>
            <div class="col-xl-2 col-md-12 text-right">
              <a href="{{ URL('/product/to_excel') }}" class="btn btn-success disabled" data-toggle="tooltip" data-placement="top" title="تصدير excel"><i class="fas fa-file-excel fa-lg mr-1"></i></a>
              <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="تصدير pdf" data-fromto="0"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- Projects table -->
          <table class="table tablee align-items-center table-flush table-hover" id="product_table">
            @php $providers = ''; @endphp
            @if($target == 'providers')
              @php $providers = $result; @endphp
              @include('admin.provider.table')
            @elseif($target == 'customers')
              @php $customers = $result; @endphp
              @include('admin.customer.table')
            @elseif($target == 'products')
              @php $products = $result; @endphp
              @include('website.table')
            @elseif($target == 'sell_bills')
              @php $sell_bills = $result; @endphp
              @include('admin.sell_bill.table')
            @endif
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- paginate -->
  <nav aria-label="..." class="justify-content-center">
    <ul class="pagination justify-content-center">
      <li class="page-item">
        <a class="page-link" href="{{ Request::fullUrl(); }}" tabindex="-1">
        <i class="fa fa-angle-left"></i>
        <span class="sr-only">Previous</span>
        </a>
      </li>
      @for($p = 1; $p <= $pages; $p++)
      <li class="page-item @if(Request::fullUrl() == URL('/search?page=' . $p)) active @endif"><a class="page-link" href="{{ URL('/search?page=' . $p) }}">{{ $p }}</a></li>
      @endfor
      <li class="page-item">
        <a class="page-link" href="{{ Request::fullUrl(); }}">
        <i class="fa fa-angle-right"></i>
        <span class="sr-only">Next</span>
        </a>
      </li>
    </ul>
  </nav>

  <!-- Footer -->
  @include('includes.footer')

</div>

<!-- Modal::create new product -->
<div class="modal fade" id="create_product_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">اضافة صنف جديد</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="create_product_form">
                <div class="modal-body">
                    @csrf
                    <div class="row">

                    </div>
                </div>

                <div class="modal-footer justify-content-center mt--3">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                            class="fa fa-door-open mr-1"></i>الغاء</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>اضافة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal::product to pdf -->
@include('includes.from_to')

@if($target == 'sell_bills')
<!-- Modal::show bill -->
@include('includes.show_bill')
@endif

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>

  $(document).ready(function(){
    $("#search_input").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $(".tablee tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });

  @if($target == 'providers')

    // show provider kashf to pdf modal
    $('#provider_table').on('click', '.from_to_pdf_button', function(e){
      let from_to = $(this).data('fromto');
      $('#from_to_pdf_modal').modal('show');
      $('#from_to').val(from_to);
    });
    
    // show provider to pdf modal
    $('.from_to_pdf_button').click(function(e){
      let from_to = $(this).data('fromto');
      $('#from_to_pdf_modal').modal('show');
      $('#from_to').val(from_to);
    });

    // create provider to pdf form
    $('#from_to_pdf_form').submit(function(e){
      e.preventDefault();
      let from = $('input[name="from"]').val();
      let to = $('input[name="to"]').val();
      let from_to = $('#from_to').val();
      let _token = $('input[name="_token"]').val();
      if(from_to == '0'){
        $.ajax({
          url: "/provider/to_pdf",
          type: "POST",
          data: {
            from: from,
            to: to,
            _token: _token
          },
          success: function(response){
            $('#from_to_pdf_modal').modal('hide');
          }
        });
      } else {
        $.ajax({
          url: "/provider/kashf_to_pdf",
          type: "POST",
          data: {
            from: from,
            to: to,
            id: from_to,
            _token: _token
          },
          success: function(response){
            $('#from_to_pdf_modal').modal('hide');
          }
        });
      }
      $('#from_to_pdf_form')[0].reset();
      $('#from_to_pdf_modal').modal('hide');
    });

  @elseif($target == 'customers')

    // show customer kashf to pdf modal
    $('#customer_table').on('click', '.from_to_pdf_button', function(e){
      let from_to = $(this).data('fromto');
      $('#from_to_pdf_modal').modal('show');
      $('#from_to').val(from_to);
    });

    // show customer to pdf modal
    $('.from_to_pdf_button').click(function(e){
      let from_to = $(this).data('fromto');
      $('#from_to_pdf_modal').modal('show');
      $('#from_to').val(from_to);
    });

    // create provider to pdf form
    $('#from_to_pdf_form').submit(function(e){
      e.preventDefault();
      let from = $('input[name="from"]').val();
      let to = $('input[name="to"]').val();
      let from_to = $('#from_to').val();
      let _token = $('input[name="_token"]').val();
      if(from_to == '0'){
        $.ajax({
          url: "/customer/to_pdf",
          type: "POST",
          data: {
            from: from,
            to: to,
            _token: _token
          },
          success: function(response){
            $('#from_to_pdf_modal').modal('hide');
          }
        });
      } else {
        $.ajax({
          url: "/customer/kashf_to_pdf",
          type: "POST",
          data: {
            from: from,
            to: to,
            id: from_to,
            _token: _token
          },
          success: function(response){
            $('#from_to_pdf_modal').modal('hide');
          }
        });
      }
      $('#from_to_pdf_form')[0].reset();
      $('#from_to_pdf_modal').modal('hide');
    });

  @elseif($target == 'products')

    // show product to pdf modal
    $('#product_table').on('click', '.from_to_pdf_button', function(e) {
        let from_to = $(this).data('fromto');
        $('#from_to_pdf_modal').modal('show');
        $('#from_to').val(from_to);
    });

    // edit product modal
    $('#product_table').on('click', '.edit_product_button', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        $.ajax({
            url: "/product/edit",
            type: "GET",
            data: {id:id},
            success: function(response) {
                if (response.status == "success") {
                    $('#create_product_modal').modal('show');
                    $('#exampleModalLabel').text('تعديل منتج');
                    $('#create_product_modal .row').html(response.modal);
                } else {
                    Swal.fire(
                        'عفواً',
                        'حدث خطأ ما',
                        'error'
                    );
                }
            },
            error: function(response) {
                Swal.fire(
                    'عفواً',
                    'حدث خطأ ما',
                    'error'
                );
            }
        });
    });

    // create new product form
    $('#create_product_form').submit(function(e) {
        e.preventDefault();
        let data = new FormData(this);
        $.ajax({
            url: "/product/update",
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function(response) {
                if (response.status == "success") {
                    Swal.fire(
                        'تم!',
                        'تمت العملية بنجاح',
                        'success'
                    );
                    // refresh the page
                    @if(url()->current() == url('/search'))
                      window.location.reload();
                    @endif
                    $('#create_product_form')[0].reset();
                    $('#create_product_modal').modal('hide');
                } else {
                    Swal.fire(
                        'عفواً',
                        'حدث خطأ مااثناء تنفيذ العملية',
                        'error'
                    );
                }
            },
            error: function(response) {
                Swal.fire(
                    'عفواً',
                    'حدث خطأ مااثناء تنفيذ العملية',
                    'error'
                );
            }
        });
    });

    
    $("#product_table").on("click", ".delete_product_button", function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        Swal.fire({
            title: 'هل انت متاكد من الحذف ؟',
            text: "!لا يمكنك التراجع بعد هذه الخطوة",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم ، احذف!',
            cancelButtonText: 'الغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                let _token = $('input[name=_token]').val();
                $.ajax({
                    url: "/product/delete",
                    type: "POST",
                    data: {
                        id: id,
                        _token: _token
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire(
                                'تم الحذف!',
                                'تم الحذف بنجاح',
                                'success'
                            );
                            // refresh the page
							@if(url()->current() == url('/search'))
							  window.location.reload();
							@endif
                        } else {
                            Swal.fire(
                                'خطأ',
                                'حدث خطأ أثناء الحذف',
                                'error'
                            );
                        }
                    },
                    error: function(response) {
                        Swal.fire(
                            'خطأ',
                            'حدث خطأ أثناء الحذف',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // show product to pdf modal
    $('.from_to_pdf_button').click(function(e) {
        let from_to = $(this).data('fromto');
        $('#from_to_pdf_modal').modal('show');
        $('#from_to').val(from_to);
    });

    // create product to pdf form
    $('#from_to_pdf_form').submit(function(e) {
        e.preventDefault();
        let from = $('input[name="from"]').val();
        let to = $('input[name="to"]').val();
        let from_to = $('#from_to').val();
        let _token = $('input[name="_token"]').val();
        // from to == -1 means: all box movements
        if (from_to == '-1') {
            $.ajax({
                url: "/box/to_pdf",
                type: "POST",
                data: {
                    from: from,
                    to: to,
                    _token: _token
                },
                success: function(response) {
                    $('#from_to_pdf_modal').modal('hide');
                }
            });
            // from to == 0 means all products
        } else if (from_to == '0') {
            $.ajax({
                url: "/product/to_pdf",
                type: "POST",
                data: {
                    from: from,
                    to: to,
                    _token: _token
                },
                success: function(response) {
                    $('#from_to_pdf_modal').modal('hide');
                }
            });
        } else {
            $.ajax({
                url: "/product/jard_to_pdf",
                type: "POST",
                data: {
                    from: from,
                    to: to,
                    id: from_to,
                    _token: _token
                },
                success: function(response) {
                    $('#from_to_pdf_modal').modal('hide');
                }
            });
        }
        $('#from_to_pdf_form')[0].reset();
        $('#from_to_pdf_modal').modal('hide');
    });

  @elseif($target == 'sell_bills')
  
    // show sell bill
    $('#product_table').on('click', '.show_button', function() {
        let id = $(this).data('dataid');
        let movement = $(this).data('movement');
        let _token = $('input[name="_token"]').val();
        if (movement == 'show_sell_bill') {
            $.ajax({
                url: 'sell_bill/show',
                type: 'GET',
                data: {
                    id: id,
                    _token: _token
                },
                success: function(response) {
                    $('#show_bill_form .modal-body').html('');
                    $('#show_bill_form .modal-body').html(response.bill_data);
                    $('#show_bill_modal #show_bill_modalLabel').text('عرض فاتورة بيع');
                    $('#show_bill_modal').modal('show');
                }
            });
        }
    });

  @endif

</script>

@endsection