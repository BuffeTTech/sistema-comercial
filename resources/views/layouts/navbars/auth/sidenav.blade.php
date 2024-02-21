<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 "
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('buffet.dashboard', ['buffet'=>$buffet->slug]) }}"
            target="_blank">
            <img src="{{ asset('img/logo-ct-dark.png') }}" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold">{{ $buffet->trading_name }}</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="w-auto collapse navbar-collapse pb-4" id="sidenav-collapse-main" style="height: auto">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'home' ? 'active' : '' }}" href="{{ route('buffet.dashboard', ['buffet'=>$buffet->slug]) }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'profile' ? 'active' : '' }}" href="{{ route('profile', ['buffet'=>$buffet->slug ]) }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Profile</span>
                </a>
            </li> --}}
            <li class="nav-item mt-3 d-flex align-items-center">
                <div class="ps-4">
                    <i class="fab fa-laravel" style="color: #f4645f;"></i>
                </div>
                <h6 class="ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-0">Páginas</h6>
            </li>
            <li class="nav-item">
                @can('view next bookings')
                    <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'booking.') && !in_array(Route::currentRouteName(), ['booking.create', 'booking.my_bookings']) ? 'active' : '' }}" href="{{ route('booking.index', ['buffet'=>$buffet->slug]) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-note-03 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Reservas</span>
                    </a>
                @endcan
            </li>
            <li class="nav-item">
                @can('list user booking')
                    <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'booking.my_bookings') ? 'active' : '' }}" href="{{ route('booking.my_bookings', ['buffet'=>$buffet->slug]) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-note-03 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Minhas Reservas</span>
                    </a>
                @endcan
            </li>
            <li class="nav-item">
                @can('create booking')
                    <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'booking.create') ? 'active' : '' }}" href="{{ route('booking.create', ['buffet'=>$buffet->slug]) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-note-03 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Criar Reserva</span>
                    </a>
                @endcan
            </li>
            <li class="nav-item">
                @can('list recommendation')
                    <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'recommendation.') ? 'active' : '' }}" href="{{ route('recommendation.index', ['buffet'=>$buffet->slug]) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-world-2 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Recomendações</span>
                    </a>
                @endcan
            </li>
            <li class="nav-item">
                @can('list recommendation')
                    <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'calendar') ? 'active' : '' }}" href="{{ route('calendar', ['buffet'=>$buffet->slug]) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-calendar-grid-58 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Calendário</span>
                    </a>
                @endcan
            </li>
            <li class="nav-item">
                @can('list food')
                    <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'food.') ? 'active' : '' }}" href="{{ route('food.index', ['buffet'=>$buffet->slug]) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-cart text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Comidas</span>
                    </a>
                @endcan
            </li>
            <li class="nav-item">
                @can('list decoration')
                    <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'decoration.') ? 'active' : '' }}" href="{{ route('decoration.index', ['buffet'=>$buffet->slug]) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-app text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Decorações</span>
                    </a>
                @endcan
            </li>
            <li class="nav-item">
                @can('list schedule')
                    <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'schedule.') ? 'active' : '' }}" href="{{ route('schedule.index', ['buffet'=>$buffet->slug]) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-calendar-grid-58 text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Horários</span>
                    </a>
                @endcan
            </li>
            <li class="nav-item">
                @can('list all survey question')
                    <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'survey.') ? 'active' : '' }}" href="{{ route('survey.index', ['buffet'=>$buffet->slug]) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-tag text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Pesquisa de Satisfação</span>
                    </a>
                @endcan
            </li>
            <li class="nav-item">
                @can('list employee')
                    <a class="nav-link {{ Str::startsWith(Route::currentRouteName(), 'employee.') ? 'active' : '' }}" href="{{ route('employee.index', ['buffet'=>$buffet->slug]) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-settings text-primary text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Funcionários</span>
                    </a>
                @endcan
            </li>
        </ul>
    </div>
</aside>
