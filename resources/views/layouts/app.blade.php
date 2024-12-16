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
    <script src="https://kit.fontawesome.com/65968b1114.js" crossorigin="anonymous"></script>
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css') }}" rel="stylesheet" />
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="{{ $class ?? '' }}">

    @guest
        @yield('content')
    @endguest

    @auth
        @if (in_array(request()->route()->getName(), ['sign-in-static', 'sign-up-static', 'login', 'register', 'recover-password', 'rtl', 'virtual-reality']))
            @yield('content')
        @else
            @if (!in_array(request()->route()->getName(), ['profile', 'profile-static']))
                <div class="min-height-300 bg-primary position-absolute w-100"></div>
            @elseif (in_array(request()->route()->getName(), ['profile-static', 'profile']))
                <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/profile-layout-header.jpg'); background-position-y: 50%;">
                    <span class="mask bg-primary opacity-6"></span>
                </div>
            @endif
            @isset($buffet)
                @include('layouts.navbars.auth.sidenav', ['buffet'=>$buffet])
            @endisset
            <main class="main-content border-radius-lg">
                @yield('content')
            </main>
            @isset($buffet)
                @can('answer survey question')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            async function execute_questions() {
                                const csrf = document.querySelector('meta[name="csrf-token"]').content
                                const data = await axios.get('{{ route("api.bookings.get_questions_by_user_id", ["buffet"=>$buffet->slug, "user_id"=>auth()->user()->hashed_id]) }}', {
                                    headers: {
                                        'X-CSRF-TOKEN': csrf
                                    }
                                })
                        
                                if(data.data.hasOwnProperty('message')) return;
                    
                                const questions = data.data.questions.map((question, index)=>{
                                    console.log(question)
                                    if(question.question_type == "M") {
                                        return `
                                            <div>
                                                <p><strong>${question.question}</strong></p>
                                                <div>
                                                    <input required name="rows[q-${question.id}]" type="radio" id="q-${question.id}-1" value="0-25%">
                                                    <label for="q-${question.id}-1">0-25%</label>
                                                </div>
                                                <div>
                                                    <input name="rows[q-${question.id}]" type="radio" id="q-${question.id}-2" value="0-25%">
                                                    <label for="q-${question.id}-2">26-50%</label>
                                                </div>
                                                <div>
                                                    <input name="rows[q-${question.id}]" type="radio" id="q-${question.id}-3" value="26-50%">
                                                    <label for="q-${question.id}-3">51-75%</label>
                                                </div>
                                                <div>
                                                    <input name="rows[q-${question.id}]" type="radio" id="q-${question.id}-4" value="76-100%">
                                                    <label for="q-${question.id}-4">76-100%</label>
                                                </div>
                                            </div>
                                        `
                                    } else {
                                        return `
                                            <div>
                                                <label for="q-${question.id}"><strong>${question.question}</strong></label>
                                                <br>
                                                <textarea required id="q-${question.id}" name="rows[q-${question.id}]"></textarea>
                                            </div>
                                        `
                                    }
                                })
                                const booking = data.data.data.booking
                                
                                const data_modal = {
                                        title: "Pesquisa de satisfação",
                                        content: `
                                            <form action="{{ route('survey.answer_question', ['buffet'=>$buffet->slug]) }}" method="POST">
                                                <p class="font-size-20px"><strong>Aniversariante ${booking.name_birthdayperson}</strong></p>
                                                <br>
                                                @csrf
                                                ${questions.join('<br>')}
                                                <input type="hidden" value="${booking.id}" name="booking_id">
                                                <br>
                                                <button type="submit" class="bg-amber-300 hover:bg-amber-500 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Enviar pesquisa</button>
                                            </form>
                                            `
                                    }
                                html(data_modal)
                            }
                            execute_questions()
                        
                
                        })
                    </script>
                @endcan
            @endisset
        @endif
    @endauth
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
