<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Iyad Store</title>
    @include('includes.css')
</head>

<body style="font-family: 'Cairo', sans-serif; background-color: #CCCBC3;">
    <!-- Main content -->
    <div class="main-content" id="panel">
        <!-- Topnav -->
        <nav class="navbar navbar-top navbar-expand navbar-dark  border-bottom" style="background-color:#2F2A34;">
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Search form -->
                    <form class="navbar-search navbar-search-light form-inline mr-sm-3" id="navbar-search-main"
                        method="GET" action="{{ url('/search') }}">
                        <div class="form-group mb-0">
                            <div class="input-group input-group-alternative input-group-merge">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                @csrf
                                <input class="form-control" name="search_field"
                                    placeholder="...ابحث عن اسم مورد او زبون او صنف" type="text">
                                <div class="input-group-append align-items-center">
                                    <div class="dropdown">
                                        <button class="btn btn-gray btn-sm btn-round dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false"></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#">
                                                <div class="custom-control custom-radio">
                                                    <input name="target" class="custom-control-input" id="customRadio4"
                                                        type="radio" value="providers" checked>
                                                    <label class="custom-control-label"
                                                        for="customRadio4">الموردون</label>
                                                </div>
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <div class="custom-control custom-radio">
                                                    <input name="target" class="custom-control-input" id="customRadio5"
                                                        type="radio" value="customers">
                                                    <label class="custom-control-label"
                                                        for="customRadio5">الزبائن</label>
                                                </div>
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <div class="custom-control custom-radio">
                                                    <input name="target" class="custom-control-input" id="customRadio6"
                                                        type="radio" value="products">
                                                    <label class="custom-control-label"
                                                        for="customRadio6">الاصناف</label>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="close" data-action="search-close"
                            data-target="#navbar-search-main" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </form>
                    <!-- Navbar links -->
                    <ul class="navbar-nav align-items-center ml-md-auto ">
                        <li class="nav-item d-sm-none">
                            <a class="nav-link" href="#" data-action="search-show"
                                data-target="#navbar-search-main">
                                <i class="fas fa-search"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fas fa-university"></i> كشوفات وسندات وفواتير
                            </a>
                            <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right py-0 overflow-hidden">
                                <!-- Dropdown header -->
                                <div class="px-3 py-3">
                                    <h6 class="text-sm text-muted m-0"><strong class="text-primary">Choose |
                                            اختار</strong></h6>
                                </div>
                                <!-- List group -->
                                <div class="list-group list-group-flush">
                                    <a href="{{ URL('/sanadat_sarfs') }}"
                                        class="btn list-group-item list-group-item-action">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <i class="fa fa-book text-danger"></i>
                                            </div>
                                            <div class="col ml--2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 text-sm">Bills Of Exchange | سندات الصرف</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="{{ URL('/sanadat_qapds') }}"
                                        class="btn list-group-item list-group-item-action">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <i class="fa fa-book text-success"></i>
                                            </div>
                                            <div class="col ml--2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 text-sm">Bills Of Receipt | سندات القبض</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="{{ URL('/buy_bills') }}"
                                        class="btn list-group-item list-group-item-action">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <i class="fa fa-shekel-sign text-danger"></i>
                                            </div>
                                            <div class="col ml--2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 text-sm">Buy Bills | فواتير الشراء</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="{{ URL('/sell_bills') }}"
                                        class="btn list-group-item list-group-item-action">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <i class="fa fa-shekel-sign text-success"></i>
                                            </div>
                                            <div class="col ml--2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 text-sm">Sell Bills | فواتير البيع</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fas fa-book-open"></i> القائمة الرئيسية
                            </a>
                            <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right py-0 overflow-hidden">
                                <!-- Dropdown header -->
                                <div class="px-3 py-3">
                                    <h6 class="text-sm text-muted m-0"><strong class="text-primary">Choose |
                                            اختار</strong></h6>
                                </div>
                                <!-- List group -->
                                <div class="list-group list-group-flush">
                                    <a class="btn list-group-item list-group-item-action" data-toggle="modal"
                                        data-target="#add_box_modal">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <i class="fa fa-wallet text-orange"></i>
                                            </div>
                                            <div class="col ml--2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 text-sm">Add Box | اضافة للصندوق</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="btn list-group-item list-group-item-action" data-toggle="modal"
                                        data-target="#show_box_modal">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <i class="fa fa-box-open text-success"></i>
                                            </div>
                                            <div class="col ml--2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 text-sm">Box | الصندوق</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="{{ URL('/discounts') }}"
                                        class="btn list-group-item list-group-item-action">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <i class="fa fa-dollar-sign text-danger"></i>
                                            </div>
                                            <div class="col ml--2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 text-sm">Discounts | المصاريف</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="{{ URL('/providers') }}"
                                        class="btn list-group-item list-group-item-action">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <i class="fa fa-plus text-info"></i>
                                            </div>
                                            <div class="col ml--2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 text-sm">Providers | الموردون</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="{{ URL('/customers') }}"
                                        class="btn list-group-item list-group-item-action">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <i class="fa fa-user text-yellow"></i>
                                            </div>
                                            <div class="col ml--2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 text-sm">Customers | الزبائن</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ URL('/') }}" role="button">
                                <i class="fas fa-home"></i> الصفحة الرئيسية
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav align-items-center ml-auto ml-md-0">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item dropdown">
                                    <a class="nav-link" href="{{ route('login') }}" role="button">
                                        <i class="fa fa-lock"></i> Login | تسجيل الدخول
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <div class="media align-items-center">
                                        <span class="avatar avatar-sm rounded-circle">
                                            <img alt="Image placeholder" src="{{ URL('assets/img/theme/team-5.jpg') }}">
                                        </span>
                                        <div class="media-body  ml-2  d-none d-lg-block">
                                            <span class="mb-0 text-sm  font-weight-bold">{{ Auth::user()->name }}</span>
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-menu  dropdown-menu-right ">
                                    <div class="dropdown-header noti-title">
                                        <h6 class="text-overflow m-0">Welcome!</h6>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a href="{{ route('logout') }}" class="dropdown-item"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fa fa-running text-danger"></i>
                                        <span>Logout | تسجيل الخروج</span>
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">@csrf</form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        @yield('content')
    </div>

    <!-- Argon Scripts -->
    @include('includes.js')

    <!-- Argon JS -->
    <script src="{{ asset('/assets/js/argon.js?v=1.2.0') }}"></script>
</body>

</html>
