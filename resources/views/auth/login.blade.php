<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Store - Login Page</title>
  @include('includes.css')
</head>

<body style="font-family: 'Cairo', sans-serif; background-color:#222222;">
  <!-- Main content -->
  <div class="main-content">
    <!-- Header -->
    <div class="header py-7 py-lg-8 pt-lg-9" style="background-color:#222222;">
      <div class="container">
        <div class="header-body text-center mb-5">
          <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 px-5">
              <h1 class="text-white display-1">Welcome!</h1>
              <p class="text-lead text-white">login to your account</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container mt--8 pb-5">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card bg-secondary border-0 mb-0">
            <div class="card-header bg-transparent">
              <div class="text-muted text-center">
                <a href="{{ URL('/') }}">
                  <img src="{{ URL('/assets/img/brand/store.png') }}" alt="store.png" style="width:40%; height:40%;">
                </a>
              </div>
            </div>
            <div class="card-body px-lg-5 py-lg-5">
              <div class="text-center text-muted mb-4">
                <small>Sign in with credentials</small>
              </div>
              <form role="form" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group mb-3">
                  <div class="input-group input-group-merge input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa fa-envelope text-success"></i></span>
                    </div>
                    <input class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" type="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                  </div>
                  @error('email')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>

                <div class="form-group">
                  <div class="input-group input-group-merge input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa fa-lock-open text-danger"></i></span>
                    </div>
                    <input class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" type="password" value="{{ old('password') }}" required autocomplete="email">
                  </div>
                  @error('password')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>

                <div class="custom-control custom-control-alternative custom-checkbox">
                  <input class="custom-control-input" id=" customCheckLogin" type="checkbox">
                  <label class="custom-control-label" for=" customCheckLogin">
                    <span class="text-muted">Remember me</span>
                  </label>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn btn-primary my-4">Sign in</button>
                </div>

              </form>
            </div>
          </div>
          <div class="row mt-3 text-center">
            <div class="col-12">
              <a href="{{ URL('/home') }}" class="text-light"><small>Home Page</small></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer -->
  <footer class="py-5" id="footer-main">
    <div class="container">
      <div class="row align-items-center justify-content-xl-between">
        <div class="col-xl-12">
          <div class="copyright text-center text-xl-center text-white">
            &copy;2021<a href="https://www.facebook.com/" class="font-weight-bold ml-1 text-white" target="_blank">AhmedAbuwarda</a>
            <a class="ml-4 text-white nav-link-icon" href="https://www.facebook.com/ahmed.abuwarda98" target="_blank" data-toggle="tooltip" data-original-title="Like us on Facebook">
              <i class="fab fa-facebook-square"></i>
              <span class="nav-link-inner--text d-lg-none">Facebook</span>
            </a>
            <a class="ml-3 text-white nav-link-icon" href="https://www.instagram.com/ahmed_el_salam_" target="_blank" data-toggle="tooltip" data-original-title="Follow us on Instagram">
              <i class="fab fa-instagram"></i>
              <span class="nav-link-inner--text d-lg-none">Instagram</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <!-- Argon Scripts -->
  <!-- Core -->
  @include('includes.js')
  <!-- Argon JS -->
  <script src="{{ asset('/assets/js/argon.js?v=1.2.0') }}"></script>
</body>

</html>
