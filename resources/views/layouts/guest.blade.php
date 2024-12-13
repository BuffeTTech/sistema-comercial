<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/apple-icon.png">
    <link rel="icon" type="image/png" href="/img/favicon.png">

    <meta name="description" content="BuffetTech é o sistema ideal para a gestão de buffets de festas infantis. Organize reservas, cardápios personalizados, pagamentos e estoque de forma eficiente.">
    <meta name="keywords" content="buffettech, buffet infantil, sistema de gestão de buffets, software de gestão, reservas de festas, controle de estoque, planejamento de festas, organização de buffets">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ config('app.administrative_url') }}">
    
    <meta property="og:title" content="BuffetTech - A melhor forma de gerenciar seu buffet de festas infantis.">
    <meta property="og:description" content="BuffetTech é o sistema ideal para a gestão de buffets de festas infantis. Organize reservas, cardápios personalizados, pagamentos e estoque de forma eficiente.">
    <meta property="og:image" content="{{ asset('img/identidade-visual/buffettech_logo_vertical.png') }}">
    <meta property="og:url" content="{{ config('app.administrative_url') }}">
    <meta property="og:type" content="website">
    
    <meta name="twitter:title" content="BuffetTech - A melhor forma de gerenciar seu buffet de festas infantis.">
    <meta name="twitter:description" content="BuffetTech é o sistema ideal para a gestão de buffets de festas infantis. Organize reservas, cardápios personalizados, pagamentos e estoque de forma eficiente.">
    <meta name="twitter:image" content="{{ asset('img/identidade-visual/buffettech_logo_vertical.png') }}">
    <meta name="twitter:card" content="summary_large_image">

    <title>{{ config('app.name', 'BuffeTTech') }}</title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css') }}" rel="stylesheet" />
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="{{ $class ?? '' }}">

    @yield('content')

    <!--   Core JS Files   -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets/js/argon-dashboard.js') }}"></script>
    @stack('js')
</body>

</html>
