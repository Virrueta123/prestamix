  <!DOCTYPE html>
  <!--
Template Name: Midone - HTML Admin Dashboard Template
Author: Left4code
Website: http://www.left4code.com/
Contact: muhammadrizki@left4code.com
Purchase: https://themeforest.net/user/left4code/portfolio
Renew Support: https://themeforest.net/user/left4code/portfolio
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
  <html lang="en" class="light">
  <!-- BEGIN: Head -->

  <head>
      <meta charset="utf-8">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <link href="{{ asset('dist/images/logo/logo.svg') }}" rel="shortcut icon">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description"
          content="Midone admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities.">
      <meta name="keywords"
          content="admin template, Midone Admin Template, dashboard template, flat admin template, responsive admin template, web app">
      <meta name="author" content="LEFT4CODE">
      <title>Cash Time</title>
      <!-- BEGIN: CSS Assets-->
      <link rel="stylesheet" href="{{ asset('dist/css/app.css') }}" />

      <link rel="stylesheet" href="{{ mix('css/app.css') }}">

      <link href="{{ asset('plugins/css/datatables.min.css') }}" rel="stylesheet">


      <link rel="stylesheet" href="{{ asset('dist/css/run.css') }}" />


      <!-- END: CSS Assets-->
  </head>
  <!-- END: Head -->

  <body class="py-5">
      {{-- <div class="alert alert-pending alert-dismissible show flex items-center mb-2" role="alert">
         Produccion
    </div> --}}

{{--      <div class="preload" id="preload">--}}
{{--          <div class="central">--}}

{{--              <lottie-player style="margin-top: -50px; margin-left: -50px; width: 250px; height: 250px;" src="{{ asset('dist/images/logo/preload.json') }}" background="transparent" speed="2.5"--}}
{{--                  style="width: 250px; height: 250px;" loop autoplay></lottie-player>--}}
{{--              <p style="width: 300px; margin-left: -50px;">Cargando operacion</p>--}}
{{--          </div>--}}
{{--      </div>--}}

      <!-- BEGIN: Mobile Menu -->
      <div class="mobile-menu md:hidden">
          <div class="mobile-menu-bar">
              <a href="" class="flex mr-auto">
                  <h1 class="text-white text-lg ml-3"> Prestamix </h1>
              </a>
              <a href="javascript:;" class="mobile-menu-toggler"> <i data-lucide="bar-chart-2"
                      class="w-8 h-8 text-white transform -rotate-90"></i> </a>
          </div>
          <div class="scrollable">
              <a href="javascript:;" class="mobile-menu-toggler"> <i data-lucide="x-circle"
                      class="w-8 h-8 text-white transform -rotate-90"></i> </a>
              @include('layouts.menu_mobile')
          </div>
      </div>
      <!-- END: Mobile Menu -->
      <div class="flex mt-[4.7rem] md:mt-0">
          <!-- BEGIN: Side Menu -->
          <nav class="side-nav">
              <a href="" class="intro-x flex items-center pl-5 pt-4">
                   
                   <h1 class="text-white text-lg ml-3"> Prestamix </h1>
              </a>
              <div class="side-nav__devider my-6"></div>
              @include('layouts.menu')
          </nav>
          <!-- END: Side Menu -->
          <!-- BEGIN: Content -->
          <div class="content">
              <!-- BEGIN: Top Bar -->
              <div class="top-bar">
                  <!-- BEGIN: Breadcrumb -->
                  <nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
                      @yield('history')
                  </nav>
                  <!-- END: Breadcrumb -->


                  <!-- END: Notifications -->

                  <!-- BEGIN: Notifications -->
                  <div class="intro-x dropdown mr-auto sm:mr-6">
                      <div class="dropdown-toggle notification  cursor-pointer" role="button" aria-expanded="false"
                          data-tw-toggle="dropdown"><a href="{{ route('calendario.planilla') }}"> <i
                                  data-lucide="calendar" class="notification__icon dark:text-slate-500"></i></a>
                      </div>

                  </div>
                  <!-- END: Notifications -->
                  <!-- BEGIN: Account Menu -->
                  <div class="intro-x dropdown w-8 h-8">
                      <div class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in"
                          role="button" aria-expanded="false" data-tw-toggle="dropdown">
                          <img alt="Midone - HTML Admin Template" src="{{ asset('dist/images/profile-5.jpg') }}">
                      </div>
                      <div class="dropdown-menu w-56">
                          <ul class="dropdown-content bg-primary text-white">
                              <li class="p-2">
                                  <div class="font-medium">{{ Auth::user()->name }} | {{ Auth::user()->lastname }}
                                  </div>

                                  @foreach (Auth::user()->roles as $roles)
                                      <div class="text-xs text-white/70 mt-0.5 dark:text-slate-500">
                                          {{ $roles->name }}</div>
                                  @endforeach

                              </li>
                              <li>
                                  <hr class="dropdown-divider border-white/[0.08]">
                              </li>

                              <li>
                                  <hr class="dropdown-divider border-white/[0.08]">
                              </li>
                              <li>
                                  <a class="dropdown-item hover:bg-white/5" href="{{ route('logout') }}"
                                      onclick="event.preventDefault();
                                                  document.getElementById('logout-form').submit();">

                                      {{ __('Logout') }}
                                  </a>

                                  <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      class="d-none">
                                      @csrf
                                  </form>

                              </li>
                          </ul>
                      </div>
                  </div>
                  <!-- END: Account Menu -->
              </div>
              <!-- END: Top Bar -->

              <!-- ******** body componente ************* -->
              @yield('body')
              <!-- *********************** -->


          </div>
          <!-- END: Content -->


      </div>

      <!-- BEGIN: JS Assets-->

      <script src="plugins/js/sweetalert2.min.js"></script>

      <script src="/plugins/js/fontawesome.js" crossorigin="anonymous"></script>

      <script src="{{ asset('dist/js/app.js') }}"></script>
      <script src="{{ asset('plugins/js/pdfmake.min.js') }}"></script>
      <script src="{{ asset('plugins/js/vfs_fonts.min.js') }}"></script>
      <script src="{{ asset('plugins/js/datatables.min.js') }}"></script>

      <script src="{{ mix('js/app.js') }}"></script>
      <script>
          window.addEventListener('load', function() {


              @if (session()->has('success'))
                  Swal.fire({
                      position: 'top-end',
                      icon: 'success',
                      title: '{{ session('success') }}',
                      showConfirmButton: false,
                      timer: 1500
                  })
              @endif

              @if (session()->has('warning'))
                  Swal.fire({
                      position: 'top-end',
                      icon: 'warning',
                      title: '{{ session('warning') }}',
                      showConfirmButton: false,
                      timer: 6500
                  })
              @endif

              @if (session()->has('success_img_rechazo'))
                  Swal.fire({
                      imageUrl: "{{ asset('/') }}dist/images/Draw/rechazado.svg",
                      imageHeight: 150,
                      imageAlt: "--",
                      text: '{{ session('success_img_rechazo') }}',
                      icon: "success",
                  });
              @endif

              @if (session()->has('success_img_aprobada'))
                  Swal.fire({
                      imageUrl: "{{ asset('/') }}dist/images/Draw/aprobada.svg",
                      imageHeight: 150,
                      imageAlt: "--",
                      text: '{{ session('success_img_aprobada') }}',
                      icon: "success",
                  });
              @endif

          });
      </script>

      @yield('js')
      <!-- END: JS Assets-->
  </body>

  </html>
