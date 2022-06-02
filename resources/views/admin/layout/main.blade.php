<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>{{ config('app.name') }}</title>

    <META NAME="ROBOTS" CONTENT="NOINDEX, FOLLOW"/>
    <META NAME="ROBOTS" CONTENT="INDEX, NOFOLLOW"/>
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW"/>

    <!-- Favicon-->
    <link rel="icon" href="{{ url('images/dpm-favicon.png') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet"
          type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="{{ url('admin/plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{ url('admin/plugins/node-waves/waves.css') }}" rel="stylesheet"/>

    <!-- Animation Css -->
    <link href="{{ url('admin/plugins/dropzone/dropzone.css') }}" rel="stylesheet"/>
    <link href="{{ url('admin/plugins/animate-css/animate.css') }}" rel="stylesheet"/>

    <!-- Morris Chart Css-->
    <link href="{{ url('admin/plugins/morrisjs/morris.css') }}" rel="stylesheet"/>

    <link href="{{ url('admin/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet"/>

    <!-- noUISlider Css -->
    <link href="{{ url('admin/plugins/nouislider/nouislider.min.css') }}" rel="stylesheet" />

    <link href="{{ url('admin/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ url('admin/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}"
          rel="stylesheet">


    <!-- Light Gallery Plugin Css -->
    <link href="{{ url('admin/plugins/light-gallery/css/lightgallery.css') }}" rel="stylesheet">

    <!-- Custom Css -->
    <link href="{{ url('admin/css/style.css') }}" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="{{ url('admin/css/themes/all-themes.css') }}" rel="stylesheet"/>

    <link rel="stylesheet" href="{{ url('admin/css/jquery.fancybox.css') }}" type="text/css" media="screen"/>
    <style type="text/css">
        .bootstrap-select .bs-searchbox input.form-control {
            padding: 0 10px;
        }
        .geo-registry-view p{
            word-break: break-word;
        }
        .bootstrap-select .dropdown-menu {
            overflow: auto !important;
            max-height: 400px !important;
            z-index:9 !important;
        }
        .bootstrap-select.btn-group .dropdown-menu.inner{
            max-height: 349px !important;
        }
        .bootstrap-select.btn-group .dropdown-toggle .caret{
            right: -6px;
        }
        .modal-body h4{
            text-transform: capitalize;
        }
        .table-striped {
            text-transform: capitalize;
        }
        .created {
            color: green;
        }
        .updated {
            color: blue;
        }
        .deleted {
            color: red;
        }


        /* Important part */
        .modal-dialog{
            overflow-y: initial !important
        }
        .modal-body{
            height: 250px;
            overflow-y: auto;
        }
        .highcharts-data-table{
            margin: 10px;
        }

        .pull-right .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.25rem 1.5rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }
        .pull-right .dropdown-menu {
            transform: translateY(125%);
        }
     </style>

    {{--<style>--}}
    {{--.page-loader-wrapper {--}}
    {{--display: none--}}
    {{--}--}}
    {{--</style>--}}

    <script src="{{ url('admin/plugins/jquery/jquery.min.js') }}"></script>
    <script>
        const BASE_URL = '<?= url('/') ?>';
        const CSRF_TOKEN = '<?= csrf_token() ?>';
    </script>
    @stack('stylesheets')
</head>

<body class="theme-blue">
<!-- Page Loader -->
<div class="page-loader-wrapper">
    <div class="loader">
        <div class="preloader">
            <div class="spinner-layer pl-blue">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
        <p>Please wait...</p>
    </div>
</div>
<!-- #END# Page Loader -->
<!-- Overlay For Sidebars -->
<div class="overlay"></div>
<!-- #END# Overlay For Sidebars -->
<!-- Search Bar -->
<div class="search-bar">
    <div class="search-icon">
        <i class="material-icons">search</i>
    </div>
    <input type="text" placeholder="START TYPING...">
    <div class="close-search">
        <i class="material-icons">close</i>
    </div>
</div>
<!-- #END# Search Bar -->
<!-- Top Bar -->
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse"
               data-target="#navbar-collapse" aria-expanded="false"></a>
            <a href="javascript:void(0);" class="bars"></a>
            <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name') }}</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            @include('admin.layout.partial.top-nav')
        </div>
    </div>
</nav>
<!-- #Top Bar -->
<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <!-- User Info -->
    @include('admin.layout.partial.user')
    <!-- #User Info -->
        <!-- Menu -->
    @include('admin.layout.partial.sidebar-nav')
    <!-- #Menu -->
        <!-- Footer -->
        <div class="legal">
            <div class="copyright">
                Sigma Ventures Ltd &copy; {{ date('Y') }} <br> Digital Property Management System.
            </div>
        </div>
        <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->
    <!-- Right Sidebar -->
    <aside id="rightsidebar" class="right-sidebar">
        @include('admin.layout.partial.right-bar')
    </aside>
    <!-- #END# Right Sidebar -->
</section>

<section class="content">
    <div class="container-fluid">
        @include('admin.layout.partial.flash-message')

        @yield('content')
    </div>
</section>


<!-- Jquery Core Js -->
<script>
    var autoPopulateAjaxUrl = '{{route('admin.ward.options')}}';

</script>

<script src="{{ asset('admin/js/AutoPopulate.js') }}"></script>
<script src="{{ url('admin/plugins/momentjs/moment.js') }}"></script>

<!-- Bootstrap Core Js -->
<script src="{{ url('admin/plugins/bootstrap/js/bootstrap.js') }}"></script>

<!-- Select Plugin Js -->
<script src="{{ url('admin/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>

<!-- noUISlider Plugin Js -->
<script src="{{ url('admin/plugins/nouislider/nouislider.js') }}"></script>

<!-- Slimscroll Plugin Js -->
<script src="{{ url('admin/plugins/jquery-slimscroll/jquery.slimscroll.js') }}"></script>

<!-- Waves Effect Plugin Js -->
<script src="{{ url('admin/plugins/node-waves/waves.js') }}"></script>

<script src="{{ url('admin/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>

<script src="{{ url('admin/plugins/multi-select/js/jquery.multi-select.js') }}"></script>


<script src="{{ url('admin/plugins/dropzone/dropzone.js') }}"></script>
<!-- Jquery CountTo Plugin Js -->
<script src="{{ url('admin/plugins/jquery-countto/jquery.countTo.js') }}"></script>

<!-- Morris Plugin Js -->
<script src="{{ url('admin/plugins/raphael/raphael.min.js') }}"></script>
<script src="{{ url('admin/plugins/morrisjs/morris.js') }}"></script>


<script src="{{ url('admin/plugins/sweetalert/sweetalert.min.js') }}"></script>

<script src="{{ url('admin/plugins/jquery-sparkline/jquery.sparkline.js') }}"></script>
<!-- Sparkline Chart Plugin Js -->

<script src="{{ url('admin/plugins/jquery-validation/jquery.validate.js') }}"></script>
<script src="{{ url('admin/plugins/jquery-steps/jquery.steps.js') }}"></script>
<script src="{{ url('admin/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>

<!-- Light Gallery Plugin Js -->
<script src="{{ url('admin/plugins/light-gallery/js/lightgallery-all.js') }}"></script>
<script src="{{ url('admin/plugins/jquery-inputmask/jquery.inputmask.bundle.js') }}"></script>

<!-- Custom Js -->
<script src="{{ url('admin/js/pages/medias/image-gallery.js') }}"></script>

<!-- Custom Js -->
<script src="{{ url('admin/js/admin.js') }}"></script>
<script src="{{ url('admin/js/pages/forms/form-wizard.js') }}"></script>
<script src="{{ url('admin/js/pages/forms/basic-form-elements.js') }}"></script>
{{--<script src="{{ url('admin/js/pages/forms/advanced-form-elements.js') }}"></script>--}}


<!-- Demo Js -->
<script src="{{ url('admin/js/demo.js') }}"></script>


<script type="text/javascript" src="{{ url('admin/js/jquery.fancybox.pack.js') }}"></script>

<script type="text/javascript" src="{{ url('admin/js/jquery.fancybox-media.js') }}"></script>

<script type="text/javascript" src="{{ url('admin/js/custom.js') }}"></script>

@yield('scripts')

@stack('scripts')

@stack('grid_js')

<div id="response-block"></div>



</body>

</html>
