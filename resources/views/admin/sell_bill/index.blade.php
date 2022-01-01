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

        <!-- All sell bills -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card" id="sell_bill_table_card">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-xl-2 col-md-12 text-md-center text-xl-left">
                                <h3 class="mb-0">فواتير البيع</h3>
                            </div>
                            <div class="col-xl-7 col-md-12 text-xl-left text-md-center">
                                <input type="text" name="search_input" id="search_input" class="form-control"
                                    placeholder="...ابحث عن فاتورة بيع">
                            </div>
                            <div class="col-xl-3 col-md-12 text-xl-right text-md-center">
                                <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top"
                                    title="تصدير pdf"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
                                <a class="btn btn-dark text-white multi_button" data-movement="create_sell_bill">
                                <span class="badge text-white bg-warning mr-0">{{ $box[0]->remaining }} &#8362;</span>
                                <span class="badge text-white bg-success mr-1">{{ $box[1]->remaining }} &#8362;</span> فاتورة بيع
                            </a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <!-- Projects table -->
                        <table class="table tablee align-items-center table-flush table-hover" id="sell_bill_table">
                            @include('admin.sell_bill.table')
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- paginate -->
        <nav aria-label="..." class="justify-content-center">
            <ul class="pagination justify-content-center">
                <li class="page-item">
                    <a class="page-link" href="{{ Request::fullUrl() }}" tabindex="-1">
                        <i class="fa fa-angle-left"></i>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
                @for ($p = 1; $p <= $pages; $p++)
                    <li class="page-item @if (Request::fullUrl() == URL('/sell_bills?page=' . $p)) active @endif"><a class="page-link"
                            href="{{ URL('/sell_bills?page=' . $p) }}">{{ $p }}</a></li>
                @endfor
                <li class="page-item">
                    <a class="page-link" href="{{ Request::fullUrl() }}">
                        <i class="fa fa-angle-right"></i>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Footer -->
        @include('includes.footer')

    </div>

    <!-- Modal::show bill -->
    @include('includes.show_bill')

    <!-- Modal::multi_modal -->
    @include('includes.multi_modal')

    <!-- Modal::bill to pdf-->
    @include('includes.from_to')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#search_input").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tablee tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
        // create sell bill
        $('#sell_bill_table_card').on('click', '.multi_button', function() {
            var movement = $(this).data('movement');
            if (movement == 'create_sell_bill') {
                $.ajax({
                    url: 'sell_bill/create',
                    type: 'GET',
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#show_bill_form .modal-body').html('');
                            $('#show_bill_form .modal-body').html(response.modal);
                            $('#show_bill_modal #show_bill_modalLabel').text('انشاء فاتورة بيع جديدة');
                            $('#show_bill_modal #movement').val(movement);
                            $('#show_bill_modal').modal('show');
                            $('.selectpicker').selectpicker();
                        } else {
                            Swal.fire(
                                'عفواً',
                                'حدث خطأ ما.',
                                'error'
                            );
                        }
                    }
                });
            }
        });
        $('#show_bill_modal #show_bill_form').submit(function(e) {
            e.preventDefault();
            var data = new FormData(this);
            var movement = data.get('movement');
            $.ajax({
                url: 'sell_bill/store',
                type: 'POST',
                processData: false,
                contentType: false,
                cache: false,
                data: data,
                success: function(response) {
                    if (response.status == 'success') {
                        Swal.fire({
                            title: 'تم!',
                            text: 'تم إضافة الفاتورة بنجاح',
                            icon: 'success',
                            confirmButtonText: 'حسنا',
                            confirmButtonColor: '#3085d6',
                        });
                        get_tables();
                        $('#show_bill_modal').modal('hide');
                    } else {
                        Swal.fire({
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء إضافة الفاتورة',
                            icon: 'error',
                            confirmButtonText: 'حسنا',
                            confirmButtonColor: '#3085d6',
                        });
                    }
                }
            });
        });
        // show sell bill
        $('#sell_bill_table').on('click', '.show_button', function() {
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
        $('#show_bill_modal #show_bill_form').submit(function(e) {
            e.preventDefault();
            var data = new FormData(this);
            var movement = $('#show_bill_modal #movement').val();

        });
        // show bill to pdf modal
        $('.from_to_pdf_button').click(function(e) {
            $('#from_to_pdf_modal').modal('show');
        });
        // sanadat qapd to pdf form
        $('#from_to_pdf_form').submit(function(e) {
            e.preventDefault();
            let from = $('input[name="from"]').val();
            let to = $('input[name="to"]').val();
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "/sell_bill/to_pdf",
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
            $('#from_to_pdf_form')[0].reset();
            $('#from_to_pdf_modal').modal('hide');
        });

        function get_tables() {
            $.ajax({
                url: 'sell_bills',
                type: 'GET',
                success: function(response) {
                    $('#sell_bill_table').html('');
                    $('#sell_bill_table').html(response.table);
                }
            });
        }
    </script>
@endsection
