<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags-->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Colorlib Templates">
  <meta name="author" content="Colorlib">
  <meta name="keywords" content="Colorlib Templates">

  <!-- Title Page-->
  <title>Register Form</title>

  <!-- Icons font CSS-->
  <link href="{{ asset('assets/vendor/mdi-font/css/material-design-iconic-font.min.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('assets/vendor/font-awesome-4.7/css/font-awesome.min.css') }}" rel="stylesheet" media="all">
  <!-- Font special for pages-->
  <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Vendor CSS-->
  <link href="{{ asset('assets/vendor/select2/select2.min.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('assets/vendor/datepicker/daterangepicker.css') }}" rel="stylesheet" media="all">

  <!-- Main CSS-->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet" media="all">
</head>

<body>
  <div class="page-wrapper bg-gra-02 p-t-130 p-b-100 font-poppins">
    <div class="wrapper wrapper--w540">
      <div class="card card-4">
        <div class="card-body">
          <h2 class="title">Registration Form</h2>

          @if ($errors->any())
          <div class="alert">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            <strong>Danger!</strong>
            <ul>
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif
          <form method="POST" action="{{ route('subuser.register.store', $token) }}">
            @csrf
            <div class="row row-space">
              <div class="col-6">
                <div class="input-group">
                  <label class="label">Name</label>
                  <input class="input--style-4" type="text" name="name" maxlength="200" required>
                </div>
              </div>
              <div class="col-6">
                <div class="input-group">
                  <label class="label">Email</label>
                  <input class="input--style-4" type="email" name="email" required>
                </div>
              </div>
              <div class="col-6">
                <div class="input-group">
                  <label class="label">Password</label>
                  <input class="input--style-4" type="password" name="password" required>
                </div>
              </div>
              <div class="col-6">
                <div class="input-group">
                  <label class="label">Password Confirmation</label>
                  <input class="input--style-4" type="password" name="password_confirmation" required>
                </div>
              </div>
            </div>
            <div class="p-t-15">
              <button class="btn btn--radius-2 btn--blue" type="submit">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Jquery JS-->
  <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
  <!-- Vendor JS-->
  <script src="{{ asset('assets/vendor/select2/select2.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/datepicker/moment.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/datepicker/daterangepicker.js') }}"></script>

  <!-- Main JS-->
  <script src="{{asset('assets/js/global.js')}}"></script>

</body>

</html>
<!-- end document-->