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
      <link href="{{ asset('images/isotipo_horizon.png') }}" rel="shortcut icon" type="image/png">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description"
          content="Midone admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities.">
      <meta name="keywords"
          content="admin template, Midone Admin Template, dashboard template, flat admin template, responsive admin template, web app">
      <meta name="author" content="LEFT4CODE">
      <title>Horizon Finance</title>
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
              <a href="" class="flex mr-auto pl-4">
                  <img alt="Horizon Finance" class="w-44" src="{{ asset('images/logo_horizon.png') }}">
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
              <a href="" class="intro-x flex items-center px-4 pt-5 w-full">
                   <img alt="Horizon Finance" class="w-full max-w-[200px]" src="{{ asset('images/logo_horizon.png') }}">
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
                      <div class="dropdown-toggle notification cursor-pointer relative" role="button"
                          aria-expanded="false" data-tw-toggle="dropdown" id="btn-notificaciones-prestamos">
                          <i data-lucide="bell" class="notification__icon dark:text-slate-500"></i>
                          <span id="badge-notificaciones"
                              class="notif-badge absolute -top-1 -right-1 hidden text-xs bg-danger text-white rounded-full min-w-[1.25rem] h-5 px-1 flex items-center justify-center font-semibold shadow-md">0</span>
                      </div>
                      <div class="dropdown-menu notif-panel-dropdown">
                          <div class="dropdown-content notif-panel p-0 overflow-hidden">
                              <div class="notif-panel__header">
                                  <div class="notif-panel__header-icon">
                                      <i data-lucide="bell-ring"></i>
                                  </div>
                                  <div class="min-w-0">
                                      <div class="notif-panel__title">Alertas de cobranza</div>
                                      <div id="notif-resumen" class="notif-panel__subtitle">Cargando préstamos...</div>
                                  </div>
                              </div>
                              <div id="notif-stats" class="notif-panel__stats"></div>
                              <div class="notif-panel__section-title">Próximos vencimientos</div>
                              <div id="notif-lista" class="notif-panel__lista"></div>
                              <div class="notif-panel__footer">
                                  <a href="{{ route('calendario.planilla') }}" class="notif-panel__cta">
                                      <span>Ver calendario completo</span>
                                      <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                  </a>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="intro-x dropdown mr-auto sm:mr-6">
                      <div class="dropdown-toggle notification cursor-pointer" role="button" aria-expanded="false"
                          data-tw-toggle="dropdown">
                          <a href="{{ route('calendario.planilla') }}">
                              <i data-lucide="calendar" class="notification__icon dark:text-slate-500"></i>
                          </a>
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

          (function cargarNotificacionesPrestamos() {
              const token = document.querySelector('meta[name="csrf-token"]');
              if (!token) return;

              const estadoConfig = {
                  vencido: { label: 'Vencido', badge: 'notif-item__badge--danger', card: 'notif-stat-card--danger' },
                  hoy: { label: 'Vence hoy', badge: 'notif-item__badge--warning', card: 'notif-stat-card--warning' },
                  pendiente: { label: 'Pendiente', badge: 'notif-item__badge--info', card: 'notif-stat-card--info' },
              };

              const formatMonto = (valor) => {
                  const numero = Number(valor || 0);
                  return numero.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
              };

              const iniciales = (nombre) => {
                  if (!nombre) return '?';
                  const partes = nombre.trim().split(/\s+/).filter(Boolean);
                  if (!partes.length) return '?';
                  if (partes.length === 1) return partes[0].charAt(0).toUpperCase();
                  return (partes[0].charAt(0) + partes[partes.length - 1].charAt(0)).toUpperCase();
              };

              const renderStats = (d) => {
                  const stats = [
                      { key: 'activos', value: d.total_activos, label: 'Activos', card: 'notif-stat-card--primary' },
                      { key: 'hoy', value: d.cuota_hoy, label: 'Hoy', card: 'notif-stat-card--warning' },
                      { key: 'vencidos', value: d.cliente_moroso, label: 'Vencidos', card: 'notif-stat-card--danger' },
                  ];

                  return stats.map((stat) => `
                      <div class="notif-stat-card ${stat.card}">
                          <div class="notif-stat-card__value">${stat.value}</div>
                          <div class="notif-stat-card__label">${stat.label}</div>
                      </div>
                  `).join('');
              };

              const renderLista = (prestamos) => {
                  if (!prestamos.length) {
                      return `
                          <div class="notif-empty">
                              <div class="notif-empty__icon">
                                  <i data-lucide="check-circle-2"></i>
                              </div>
                              <div class="notif-empty__title">Todo al día</div>
                              <div class="notif-empty__text">No hay préstamos activos por cobrar.</div>
                          </div>
                      `;
                  }

                  return prestamos.map((p) => {
                      const cfg = estadoConfig[p.estado] || estadoConfig.pendiente;
                      const nombre = p.cliente || 'Sin nombre';
                      return `
                          <div class="notif-item">
                              <div class="notif-item__avatar">${iniciales(nombre)}</div>
                              <div class="notif-item__body">
                                  <div class="notif-item__top">
                                      <span class="notif-item__code">#${p.code}</span>
                                      <span class="notif-item__badge ${cfg.badge}">${cfg.label}</span>
                                  </div>
                                  <div class="notif-item__name">${nombre}</div>
                                  <div class="notif-item__meta">
                                      <span class="notif-item__amount">S/ ${formatMonto(p.cuota)}</span>
                                      <span class="notif-item__dot"></span>
                                      <span class="notif-item__date">${p.fecha}</span>
                                  </div>
                              </div>
                          </div>
                      `;
                  }).join('');
              };

              const refreshIcons = () => {
                  if (typeof lucide !== 'undefined' && lucide.createIcons) {
                      lucide.createIcons();
                  }
              };

              fetch('/notificaciones_prestamos', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': token.getAttribute('content'),
                      'Accept': 'application/json',
                  },
                  body: JSON.stringify({}),
              })
              .then(r => r.json())
              .then(res => {
                  if (!res.success) return;
                  const d = res.data;
                  const badge = document.getElementById('badge-notificaciones');
                  const resumen = document.getElementById('notif-resumen');
                  const stats = document.getElementById('notif-stats');
                  const lista = document.getElementById('notif-lista');
                  const totalAlerta = d.cuota_hoy + d.cliente_moroso;

                  if (badge) {
                      if (totalAlerta > 0) {
                          badge.textContent = totalAlerta > 9 ? '9+' : totalAlerta;
                          badge.classList.remove('hidden');
                          badge.classList.add('notif-badge--pulse');
                      } else {
                          badge.classList.add('hidden');
                          badge.classList.remove('notif-badge--pulse');
                      }
                  }

                  if (resumen) {
                      if (totalAlerta > 0) {
                          resumen.textContent = `${totalAlerta} alerta${totalAlerta === 1 ? '' : 's'} requieren atención`;
                      } else {
                          resumen.textContent = `${d.total_activos} préstamo${d.total_activos === 1 ? '' : 's'} activo${d.total_activos === 1 ? '' : 's'}`;
                      }
                  }

                  if (stats) {
                      stats.innerHTML = renderStats(d);
                  }

                  if (lista) {
                      lista.innerHTML = renderLista(d.prestamos);
                      refreshIcons();
                  }
              })
              .catch(() => {});
          })();
      </script>

      @yield('js')
      <!-- END: JS Assets-->
  </body>

  </html>
