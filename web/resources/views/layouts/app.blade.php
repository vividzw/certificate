<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ trans('app.system') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
        .red {
            color : red;
        }
    </style>
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ trans('app.system') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @if (!Auth::guest())
                    <li><a href="{{ url('/') }}">{{ trans('app.home') }}</a></li>
                        @if (\App\User::checkRole('exam_admin'))
                        <li><a href="{{ url('/term') }}">{{ \App\TermModel::school_term()->name }}</a></li>
                        <li><a href="{{ url('/examsignup') }}/">{{ trans('comm.examsignup') }}</a></li>
                        <li><a href="{{ url('/depart') }}/">{{ trans('comm.depart') }}</a></li>
                        <li><a href="{{ url('/subject') }}/">{{ trans('comm.subject') }}</a></li>
                        <li><a href="{{ url('/classroom') }}/">{{ trans('comm.classroom') }}</a></li>
                        <li><a href="{{ url('/classteacher') }}/">{{ trans('comm.classteacher') }}</a></li>
                        <li><a href="{{ url('/student') }}/">{{ trans('comm.student') }}</a></li>
                        @endif
                        @if (\App\User::checkRole('classteacher', 'only_classteacher'))
                        <li><a href="{{ url('/examadd') }}/">{{ trans('comm.examadd') }}</a></li>
                        <li><a href="{{ url('/examadd/pay') }}/">{{ trans('comm.pay') }}</a></li>
                        <li><a href="{{ url('/classroomstudents') }}/">{{ trans('comm.student') }}</a></li>
                        @endif
                    @endif
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">{{ trans("app.login") }}</a></li>
                        @if (\App\TermModel::canRegister())
                        <li><a href="{{ url('/register') }}">{{ trans("app.register") }}</a></li>
                        @endif
                    @else
                        @if (\App\User::checkRole('exam_admin', $path))
                        <li><a href="/{{ $path }}export/tpl">{{ trans('app.excel_template') }}</a></li>
                        <li><a href="/{{ $path }}export">{{ trans('app.export') }}</a></li>
                        <li><a href="/{{ $path }}import">{{ trans('app.import') }}</a></li>
                        @endif
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>{{ trans("app.logout") }}</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}

    @yield('script')
</body>
</html>
