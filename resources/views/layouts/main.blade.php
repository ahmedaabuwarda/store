<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Store | Home Page</title>
  @include('includes.css')
</head>

<body style="font-family: 'Cairo', sans-serif; background-color: #303030;">
  <!-- Main content -->
  <div class="main-content" id="panel">
    <!-- Topnav -->
    <nav class="navbar navbar-top navbar-expand navbar-dark  border-bottom" style="background-color:#222222;">
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
                  placeholder="...ابحث عن اسم داعم او مستفيد او عينية" type="text" value="{{ old('search_field') }}">
                <div class="input-group-append align-items-center">
                  <div class="dropdown">
                    <button class="btn btn-gray btn-sm btn-round dropdown-toggle" type="button"
                      id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                      aria-expanded="false"></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @can('add_import_ainiats')
                      <a class="dropdown-item" href="#">
                        <div class="custom-control custom-radio">
                          <input name="target" class="custom-control-input" id="customRadio2"
                            type="radio" value="import_ainiats">
                          <label class="custom-control-label"
                            for="customRadio2">عينيات واردة</label>
                        </div>
                      </a>
                      @endcan
                      @can('add_export_ainiats')
                      <a class="dropdown-item" href="#">
                        <div class="custom-control custom-radio">
                          <input name="target" class="custom-control-input" id="customRadio3"
                            type="radio" value="export_ainiats">
                          <label class="custom-control-label"
                            for="customRadio3">عينيات صادرة</label>
                        </div>
                      </a>
                      @endcan
                      @can('add_products')
                      <a class="dropdown-item" href="#">
                        <div class="custom-control custom-radio">
                          <input name="target" class="custom-control-input" id="customRadio4"
                            type="radio" value="products">
                          <label class="custom-control-label"
                            for="customRadio4">العينيات</label>
                        </div>
                      </a>
                      @endcan
                      @can('add_providers')
                      <a class="dropdown-item" href="#">
                        <div class="custom-control custom-radio">
                          <input name="target" class="custom-control-input" id="customRadio5"
                            type="radio" value="providers">
                          <label class="custom-control-label"
                            for="customRadio5">الداعمون</label>
                        </div>
                      </a>
                      @endcan
                      @can('add_customers')
                      <a class="dropdown-item" href="#">
                        <div class="custom-control custom-radio">
                          <input name="target" class="custom-control-input" id="customRadio6"
                            type="radio" value="customers" checked>
                          <label class="custom-control-label"
                            for="customRadio6">المستفيدون</label>
                        </div>
                      </a>
                      @endcan
                      @can('add_selectives')
                      <a class="dropdown-item" href="#">
                        <div class="custom-control custom-radio">
                          <input name="target" class="custom-control-input" id="customRadio7"
                            type="radio" value="selectives">
                          <label class="custom-control-label"
                            for="customRadio7">المرشحون</label>
                        </div>
                      </a>
                      @endcan
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
                <i class="fas fa-university"></i> السندات والعينيات
              </a>
              <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right py-0 overflow-hidden">
                <!-- Dropdown header -->
                <div class="px-3 py-3">
                  <h6 class="text-sm text-muted m-0"><strong class="text-primary">Choose |
                      اختار</strong></h6>
                </div>
                <!-- List group -->
                <div class="list-group list-group-flush">
                  @can('add_sanadat_sarfs')
                  <a href="{{ URL('/sanadat_sarfs') }}"
                    class="btn list-group-item list-group-item-action">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <i class="fa fa-minus text-danger"></i>
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
                  @endcan
                  @can('add_sanadat_qapds')
                  <a href="{{ URL('/sanadat_qapds') }}"
                    class="btn list-group-item list-group-item-action">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <i class="fa fa-plus text-success"></i>
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
                  @endcan
                  @can('add_import_ainiats')
                  <a href="{{ URL('/import_ainiats') }}"
                    class="btn list-group-item list-group-item-action">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <i class="fa fa-car text-primary"></i>
                      </div>
                      <div class="col ml--2">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <h4 class="mb-0 text-sm">Buy Bills | عينيات واردة</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                  </a>
                  @endcan
                  @can('add_export_ainiats')
                  <a href="{{ URL('/export_ainiats') }}"
                    class="btn list-group-item list-group-item-action">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <i class="fa fa-smile text-info"></i>
                      </div>
                      <div class="col ml--2">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <h4 class="mb-0 text-sm">Export Ainiat | عينيات صادرة</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                  </a>
                  @endcan
                </div>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="fas fa-university"></i> الاقسام
              </a>
              <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right py-0 overflow-hidden">
                <!-- Dropdown header -->
                <div class="px-3 py-3">
                  <h6 class="text-sm text-muted m-0"><strong class="text-primary">Choose |
                      اختار</strong></h6>
                </div>
                <!-- List group -->
                @can('add_mosques')
                <a href="{{ URL('/mosques') }}"
                  class="btn list-group-item list-group-item-action">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <i class="fa fa-mosque text-success"></i>
                    </div>
                    <div class="col ml--2">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h4 class="mb-0 text-sm">Mosques | قسم المساجد</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
                @endcan
                @can('add_providers')
                <a href="{{ URL('/providers') }}"
                  class="btn list-group-item list-group-item-action">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <i class="fa fa-plus text-info"></i>
                    </div>
                    <div class="col ml--2">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h4 class="mb-0 text-sm">Providers | قسم الداعمون</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
                @endcan
                @can('add_customers')
                <a href="{{ URL('/customers') }}"
                  class="btn list-group-item list-group-item-action">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <i class="fa fa-user text-yellow"></i>
                    </div>
                    <div class="col ml--2">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h4 class="mb-0 text-sm">Customers | قسم المستفيدون</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
                @endcan
                @can('add_workers')
                <a href="{{ URL('/workers') }}"
                  class="btn list-group-item list-group-item-action">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <i class="fa fa-box text-dark"></i>
                    </div>
                    <div class="col ml--2">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h4 class="mb-0 text-sm">Workers | قسم الموظفون</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
                @endcan
                @can('add_selectives')
                <a href="{{ URL('/selectives') }}"
                  class="btn list-group-item list-group-item-action">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <i class="fa fa-box-open text-danger"></i>
                    </div>
                    <div class="col ml--2">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h4 class="mb-0 text-sm">Selectives | قسم المرشحين</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
                @endcan
                <!-- </div> -->
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
                  @can('add_currencies')
                  <a href="{{ URL('/currencies') }}" class="btn list-group-item list-group-item-action">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <i class="fa fa-arrow-up text-primary"></i>
                      </div>
                      <div class="col ml--2">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <h4 class="mb-0 text-sm">Currencies | العملات</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                  </a>
                  @endcan
                  @can('show_boxes')
                  <a href="{{url('/boxes')}}" class="btn list-group-item list-group-item-action">
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
                  @endcan
                  <a href="{{url('/movements')}}" class="btn list-group-item list-group-item-action">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <i class="fa fa-home text-dark"></i>
                      </div>
                      <div class="col ml--2">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <h4 class="mb-0 text-sm">Movements | الحركات المالية</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                  </a>
                  @can('add_expenses')
                  <a href="{{ URL('/expenses') }}"
                    class="btn list-group-item list-group-item-action">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <i class="fa fa-dollar-sign text-danger"></i>
                      </div>
                      <div class="col ml--2">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <h4 class="mb-0 text-sm">expenses | المصاريف</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                  </a>
                  @endcan
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
                  <h6 class="text-overflow m-0">مرحبا!</h6>
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ url('/settings') }}" class="dropdown-item">
                  <i class="fa fa-box text-success"></i>
                  <span>Settings | الاعدادات</span>
                </a>
                <a href="{{ url('/permissions') }}" class="dropdown-item">
                  <i class="fa fa-edit text-dark"></i>
                  <span>Permissions | الصلاحيات</span>
                </a>
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
