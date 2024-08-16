<!DOCTYPE html>
<html lang="pt-br">
<!-- begin::Head -->

<head>
    <!--begin::Base Path (base relative path for assets of this page) -->
    <base href="../" />

    <!--end::Base Path -->
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ultimate Hacking CTF Labs | Plataforma brasileira de CTF e e-Sports.</title>
    <meta name="title" content="Ultimate Hacking CTF Labs | Plataforma brasileira de CTF e e-Sports." />
    <meta name="description" content="UHC Labs é uma plataforma online que permite você aprender na prática Hacking e Segurança da Informação!
        Ajudamos a transformar Hacking em e-Sports." />

    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://uhclabs.com/" />
    <meta property="og:title" content="Ultimate Hacking CTF Labs | Plataforma brasileira de CTF e e-Sports." />
    <meta property="og:description" content="UHC Labs é uma plataforma online que permite você aprender na prática Hacking e Segurança da Informação!
        Ajudamos a transformar Hacking em e-Sports." />
    <meta property="og:image" content="/logo.png" />

    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="https://uhclabs.com/" />
    <meta property="twitter:title" content="Ultimate Hacking CTF Labs | Plataforma brasileira de CTF e e-Sports." />
    <meta property="twitter:description" content="UHC Labs é uma plataforma online que permite você aprender na prática Hacking e Segurança da Informação!
        Ajudamos a transformar Hacking em e-Sports." />
    <meta property="twitter:image" content="/logo.png" />

    <meta name="description" content="Updates and statistics" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!--begin::Fonts -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>
    <!-- Facebook Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '204148068052620');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=204148068052620&ev=PageView&noscript=1" /></noscript>
    <!-- End Facebook Pixel Code -->
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-PVWLL8G');
    </script>
    <!-- End Google Tag Manager -->
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-M8DL0W908H"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-M8DL0W908H');
    </script>
    <!--end::Fonts -->

    <!--begin::Page Vendors Styles(used by this page) -->
    <link href='{{ asset("assets/vendors/custom/fullcalendar/fullcalendar.bundle.css") }}' rel="stylesheet" type="text/css" />

    <!--end::Page Vendors Styles -->

    <!--begin:: Global Mandatory Vendors -->
    <link href='{{ asset("assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css") }}' rel="stylesheet" type="text/css" />

    <!--end:: Global Mandatory Vendors -->

    <!--begin:: Global Optional Vendors -->
    <link href='{{ asset("assets/vendors/general/tether/dist/css/tether.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/bootstrap-datetime-picker/css/bootstrap-datetimepicker.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/bootstrap-timepicker/css/bootstrap-timepicker.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/bootstrap-daterangepicker/daterangepicker.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/bootstrap-select/dist/css/bootstrap-select.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/select2/dist/css/select2.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/ion-rangeslider/css/ion.rangeSlider.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/nouislider/distribute/nouislider.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/owl.carousel/dist/assets/owl.carousel.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/owl.carousel/dist/assets/owl.theme.default.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/dropzone/dist/dropzone.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/summernote/dist/summernote.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/bootstrap-markdown/css/bootstrap-markdown.min.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/animate.css/animate.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/toastr/build/toastr.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/morris.js/morris.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/sweetalert2/dist/sweetalert2.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/general/socicon/css/socicon.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/custom/vendors/line-awesome/css/line-awesome.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/custom/vendors/flaticon/flaticon.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/vendors/custom/vendors/flaticon2/flaticon.css") }}' rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css" />

    <!--end:: Global Optional Vendors -->

    <!--begin::Global Theme Styles(used by all pages) -->
    <link href='{{ asset("assets/css/uhclabs/style.bundle.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/css/style.bundle.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/css/uhclabs/custom.css") }}' rel="stylesheet" type="text/css" />

    <!--end::Global Theme Styles -->

    <!--begin::Layout Skins(used by all pages) -->
    <link href='{{ asset("assets/css/uhclabs/skins/header/base/light.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/css/uhclabs/skins/header/menu/light.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/css/uhclabs/skins/brand/dark.css") }}' rel="stylesheet" type="text/css" />
    <link href='{{ asset("assets/css/uhclabs/skins/aside/dark.css") }}' rel="stylesheet" type="text/css" />

    <!--end::Layout Skins -->
    <link rel="shortcut icon" href='{{ asset("assets/media/logos/favicon.ico")}}' />
</head>

<!-- end::Head -->

<!-- begin::Body -->

<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PVWLL8G" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <!-- begin:: Page -->

    <!-- begin:: Header Mobile -->
    <div id="kt_header_mobile" class="kt-header-mobile kt-header-mobile--fixed">
        <div class="kt-header-mobile__logo">
            <a href="/">
                <b>UHC Labs</b>
            </a>
        </div>
        <div class="kt-header-mobile__toolbar">
            <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
            <button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button>
            <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
        </div>
    </div>

    <!-- end:: Header Mobile -->
    <div class="kt-grid kt-grid--hor kt-grid--root">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
            <!-- begin:: Aside -->
            <button class="kt-aside-close" id="kt_aside_close_btn"><i class="la la-close"></i></button>
            <div class="kt-aside kt-aside--fixed kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">
                <!-- begin:: Aside -->
                <div class="kt-aside__brand kt-grid__item" id="kt_aside_brand">
                    <div class="kt-aside__brand-logo">
                        <a href="/">
                            <b style="font-family: revert; font-size: 23px; color: white;">UHC Labs</b>
                        </a>
                    </div>
                    <div class="kt-aside__brand-tools">
                        <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                        <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
                                        <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
                                    </g>
                                </svg>
                            </span>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                        <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" />
                                        <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) " />
                                    </g>
                                </svg>
                            </span>
                        </button>

                        <!--
			<button class="kt-aside__brand-aside-toggler kt-aside__brand-aside-toggler--left" id="kt_aside_toggler"><span></span></button>
			-->
                    </div>
                </div>

                <!-- end:: Aside -->

                <!-- begin:: Aside Menu -->
                @include('layouts.sidebar')

                <!-- end:: Aside Menu -->
            </div>

            <!-- end:: Aside -->
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

                <!-- begin:: Header -->
                @include('layouts.header')
                <!-- end:: Header -->

                <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
                    <!-- Content -->

                    {{-- Comunicados cadastrados no DB --}}
                    @if (Auth::check() && Auth::user()->is_premium())
                    @forelse ($activeAnnouncements as $item)
                    <div class="container">
                        <div class="alert alert-custom alert-{{ $item->type }}" role="alert">
                            <div class="alert-icon">
                                <i class="flaticon-warning"></i>
                            </div>
                            <div class="alert-text">{{ $item->message }}</div>
                        </div>
                    </div>
                    @empty
                    {{-- Vazio --}}
                    @endforelse
                    @endif

                    @yield('content')

                    <!-- end:: Content -->
                </div>

                <!-- begin:: Footer -->
                <div class="kt-footer kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
                    <div class="kt-footer__copyright">2020&nbsp;&copy;&nbsp;<a href="http://uhclabs.com/" target="_blank" class="kt-link">UHC LABS S.A</a></div>
                </div>

                <!-- end:: Footer -->
            </div>
        </div>
    </div>

    <!-- end:: Page -->

    <!-- begin::Scrolltop
		<div id="kt_scrolltop" class="kt-scrolltop">
			<i class="fa fa-arrow-up"></i>
		</div>

		 end::Scrolltop -->

    <!--ENd:: Chat-->

    <!-- begin::Global Config(global config for global JS sciprts) -->
    <script>
        var KTAppOptions = {
            colors: {
                state: {
                    brand: "#5d78ff",
                    dark: "#282a3c",
                    light: "#ffffff",
                    primary: "#5867dd",
                    success: "#34bfa3",
                    info: "#36a3f7",
                    warning: "#ffb822",
                    danger: "#fd3995",
                },
                base: {
                    label: ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                    shape: ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"],
                },
            },
        };
    </script>

    <!-- end::Global Config -->
    @if(env('APP_ENV') !== 'local')
    <!-- Start of  Zendesk Widget script -->
    <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=ce3418a9-92d8-4517-a01c-e0ec82411a6e"></script>
    <!-- End of  Zendesk Widget script -->
    @endif
    <!--begin:: Global Mandatory Vendors -->
    <script src='{{ asset("assets/vendors/general/jquery/dist/jquery.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/popper.js/dist/umd/popper.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/bootstrap/dist/js/bootstrap.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/js-cookie/src/js.cookie.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/moment/min/moment.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/tooltip.js/dist/umd/tooltip.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/sticky-js/dist/sticky.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/wnumb/wNumb.js") }}' type="text/javascript"></script>

    <!--end:: Global Mandatory Vendors -->

    <!--begin:: Global Optional Vendors -->
    <script src='{{ asset("assets/vendors/general/jquery-form/dist/jquery.form.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/block-ui/jquery.blockUI.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/custom/js/vendors/bootstrap-datepicker.init.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/bootstrap-timepicker/js/bootstrap-timepicker.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/custom/js/vendors/bootstrap-timepicker.init.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/bootstrap-daterangepicker/daterangepicker.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/bootstrap-maxlength/src/bootstrap-maxlength.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/custom/vendors/bootstrap-multiselectsplitter/bootstrap-multiselectsplitter.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/bootstrap-select/dist/js/bootstrap-select.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/bootstrap-switch/dist/js/bootstrap-switch.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/custom/js/vendors/bootstrap-switch.init.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/select2/dist/js/select2.full.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/ion-rangeslider/js/ion.rangeSlider.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/typeahead.js/dist/typeahead.bundle.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/handlebars/dist/handlebars.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/inputmask/dist/jquery.inputmask.bundle.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/inputmask/dist/inputmask/inputmask.date.extensions.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/inputmask/dist/inputmask/inputmask.numeric.extensions.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/nouislider/distribute/nouislider.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/owl.carousel/dist/owl.carousel.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/autosize/dist/autosize.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/clipboard/dist/clipboard.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/dropzone/dist/dropzone.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/summernote/dist/summernote.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/markdown/lib/markdown.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/bootstrap-markdown/js/bootstrap-markdown.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/custom/js/vendors/bootstrap-markdown.init.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/bootstrap-notify/bootstrap-notify.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/custom/js/vendors/bootstrap-notify.init.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/jquery-validation/dist/jquery.validate.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/jquery-validation/dist/additional-methods.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/custom/js/vendors/jquery-validation.init.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/toastr/build/toastr.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/raphael/raphael.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/morris.js/morris.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/chart.js/dist/Chart.bundle.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/custom/vendors/bootstrap-session-timeout/dist/bootstrap-session-timeout.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/custom/vendors/jquery-idletimer/idle-timer.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/waypoints/lib/jquery.waypoints.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/counterup/jquery.counterup.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/es6-promise-polyfill/promise.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/sweetalert2/dist/sweetalert2.min.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/custom/js/vendors/sweetalert2.init.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/jquery.repeater/src/lib.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/jquery.repeater/src/jquery.input.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/jquery.repeater/src/repeater.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/general/dompurify/dist/purify.js") }}' type="text/javascript"></script>

    <!--end:: Global Optional Vendors -->

    <!--begin::Global Theme Bundle(used by all pages) -->
    <script src='{{ asset("assets/js/uhclabs/scripts.bundle.js") }}' type="text/javascript"></script>
    <script src='{{ asset("assets/js/uhclabs/custom.js") }}' type="text/javascript"></script>

    <!--end::Global Theme Bundle -->

    <!--begin::Page Vendors(used by this page) -->
    <script src='{{ asset("assets/vendors/custom/fullcalendar/fullcalendar.bundle.js") }}' type="text/javascript"></script>
    <script src="//maps.google.com/maps/api/js?key=AIzaSyBTGnKT7dt597vo9QgeQ7BFhvSRP4eiMSM" type="text/javascript"></script>
    <script src='{{ asset("assets/vendors/custom/gmaps/gmaps.js") }}' type="text/javascript"></script>

    <!--end::Page Vendors -->

    <!--begin::Page Scripts(used by this page) -->
    <script src='{{ asset("assets/js/uhclabs/pages/dashboard.js") }}' type="text/javascript"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js'></script>

    <script src='{{ asset("assets/js/uhclabs/pages/components/extended/toastr.js?v=7.1.8") }}'></script>

    <!--end::Page Scripts -->
    @yield('page_scripts')

    <script>
        $(document).on('load', function() {
            $("body").fadeIn("slow");
        });

        @if(Session::has('message'))
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "500",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        @if(Session::has('type'))
        @if(Session::get('type') == 'error')
        toastr.error("<b style='color: white'>{{Session::get('message')}}</b>", "<b style='color: white'>Oops</b>");

        @else
        toastr.success("<b style='color: white'>{{Session::get('message')}}</b>", "<b style='color: white'>Sucesso!</b>");
        @endif
        @endif
        @endif
    </script>
    <script src="https://dtd00fpwg1x0.statuspage.io/embed/script.js"></script>
</body>

<!-- end::Body -->

</html>