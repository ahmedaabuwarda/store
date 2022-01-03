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

        <!-- statistics -->
        <div class="row" id="statistics_table">     
            @include('includes.statistics')
        </div>

        <!-- All products -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">الاصناف</h3>
                            </div>
                            <div class="col-xl-7 col-md-9 text-center">
                                <input type="text" name="search_input" id="search_input" class="form-control"
                                    placeholder="...ابحث عن صنف">
                            </div>
                            <div class="col-xl-4 col-md-12 text-right">
                                <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top"
                                    title="تصدير pdf" data-fromto="0"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
                                @if(Auth::user()->id == 1)
                                <button class="btn btn-info multi_button" data-toggle="tooltip" data-placement="top" title="خيارات" data-movement="create_permission">خيارات</button>
                                @endif
                                @can('add_products')
                                <a class="btn text-white btn-dark" data-toggle="modal"
                                    data-target="#create_product_modal"><i class="fa fa-plus"></i> صنف</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <!-- Projects table -->
                        <table class="table tablee align-items-center table-flush table-hover" id="product_table">
                            @include('website.table')
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
                    <li class="page-item @if (Request::fullUrl() == URL('/home?page=' . $p)) active @endif"><a class="page-link"
                            href="{{ URL('/home?page=' . $p) }}">{{ $p }}</a></li>
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
                            <div class="col-xl-12 col-md-12">

                                <div class="form-group">
                                    <label class="form-control-label">اسم المنتج</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i
                                                    class="fa fa-box"></i></span>
                                        </div>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            name="name" placeholder="اسم المنتج" value="{{ old('name') }}"
                                            autocomplete="name" required autofocus>
                                    </div>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <div class="col-xl-12 col-md-12">

                                <div class="form-group">
                                    <label class="form-control-label">النوع</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i
                                                    class="fa fa-heart"></i></span>
                                        </div>
                                        <select class="form-control selectpicker" name="type" required>
                                            <option value="وحدة">وحدة</option>
                                            <option value="غرام">غرام</option>
                                            <option value="متر">متر</option>
                                            <option value="شيكل">شيكل</option>
                                        </select>
                                    </div>
                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

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

    <!-- Modal::Add to box-->
    <div class="modal fade" id="add_box_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center ">
                    <h5 class="modal-title" id="exampleModalCenterTitle">اضافة للصندوق</h5>
                    <div class="col-xl-4 col-md-12 text-right">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="add_box_form">
                    @csrf
                    <div class="modal-body">
                        <!-- All salaries -->
                        <div class="row">

                            <div class="col-xl-12 col-md-12">

                                <div class="form-group">
                                    <label class="form-control-label">المبلغ</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i
                                                    class="fa fa-box text-dark"></i></span>
                                        </div>
                                        <input type="number" class="form-control @error('balance') is-invalid @enderror"
                                            id="balance" name="balance" placeholder="المبلغ" value="{{ old('balance') }}"
                                            autocomplete="balance" required>
                                    </div>
                                    @error('balance')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-xl-12 col-md-12">

                                <div class="form-group">
                                    <label class="form-control-label">ملاحظات</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" rows="4"
                                        name="notes" placeholder="ملاحظات"
                                        autocomplete="notes">{{ old('notes') }}</textarea>
                                </div>

                            </div>


                        </div>
                    </div>
                    <div class="modal-footer justify-content-center mt--3">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                class="fa fa-door-open mr-1"></i>الغاء</button>
                        <button type="submit" class="btn btn-primary"><i
                                class="fa fa-plus mr-1"></i>حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal::Show box-->
    <div class="modal fade" id="show_box_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center ">
                    <h5 class="modal-title" id="exampleModalCenterTitle">كشف حركات الصندوق</h5>
                    <div class="col-xl-4 col-md-12 text-right">
                        <button class="btn btn-danger from_to_pdf_button" data-toggle="tooltip" data-placement="top"
                            title="تصدير pdf" data-fromto="-1"><i class="fas fa-file-pdf fa-lg mr-1"></i></button>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <!-- All salaries -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">

                                <div class="table-responsive">
                                    <!-- Projects table -->
                                    <table class="table tablee align-items-center table-hover" id="box_table">
                                        @include('website.box_table')
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center mt--3">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                            class="fa fa-door-open mr-1"></i>الغاء</button>
                </div>
            </div>
        </div>
    </div>

    @include('includes.multi_modal')

    <!-- Modal::product to pdf -->
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
        // create new product form
        $('#add_box_form').submit(function(e) {
            e.preventDefault();
            let data = new FormData(this);
            $.ajax({
                url: "/box/store",
                type: "POST",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    if (response.status == "success") {
                        Swal.fire(
                            'تم!',
                            'تم اضافة المبلغ بنجاح',
                            'success'
                        );
                        // refresh the table
                        get_products();
                        $('#add_box_form')[0].reset();
                        $('#add_box_modal').modal('hide');
                    } else {
                        Swal.fire(
                            'عفواً',
                            'حدث خطأ ما، اثناء عملية الاضافة',
                            'error'
                        );
                    }
                },
                error: function(response) {
                    Swal.fire(
                        'عفواً',
                        'حدث خطأ ما، اثناء عملية الاضافة',
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
                url: "/product/store",
                type: "POST",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    if (response.status == "success") {
                        Swal.fire(
                            'تم!',
                            'تم اضافة المنتج بنجاح',
                            'success'
                        );
                        // refresh the table
                        get_products();
                        $('#create_product_form')[0].reset();
                        $('#create_product_modal').modal('hide');
                    } else {
                        Swal.fire(
                            'عفواً',
                            'حدث خطأ ما، قد يكون المنتج موجوداً بالفعل',
                            'error'
                        );
                    }
                },
                error: function(response) {
                    Swal.fire(
                        'عفواً',
                        'حدث خطأ ما، قد يكون المنتج موجوداً بالفعل',
                        'error'
                    );
                }
            });
        });
        // show product to pdf modal
        $('#product_table').on('click', '.from_to_pdf_button', function(e) {
            let from_to = $(this).data('fromto');
            $('#from_to_pdf_modal').modal('show');
            $('#from_to').val(from_to);
        });
        // show product to pdf modal
        $('.from_to_pdf_button').click(function(e) {
            let from_to = $(this).data('fromto');
            $('#from_to_pdf_modal').modal('show');
            $('#from_to').val(from_to);
        });
        @if(url()->current() == url('/home'))
        $('.multi_button').click(function(e){
            e.preventDefault();

        });
        @endif
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
        // get all product
        function get_products() {
            $.ajax({
                url: "/home",
                type: "GET",
                success: function(response) {
                    $('#product_table').html('');
                    $('#product_table').append(response.table);
                    $('#statistics_table').html('');
                    $('#statistics_table').append(response.statistics);
                },
                error: function(response) {
                    Swal.fire(
                        'خطأ',
                        'حدث خطأ أثناء جلب البيانات',
                        'error'
                    );
                }
            });
        }
    </script>
@endsection
