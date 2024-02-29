@extends('layouts.guest', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
              <header>
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                  <div class="container">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                      <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                      <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                          <a class="nav-link" href="{{route('buffetTest', ['buffet'=>$buffet->slug])}}">{{ $buffet->trading_name }}</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="{{route('login', ['buffet'=>$buffet->slug])}}">Entrar</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="{{route('register',['buffet'=>$buffet->slug])}}">Registrar</a>
                        </li>
                        {{-- <li class="nav-item">
                          <a class="nav-link" href="#">Contato</a>
                        </li> --}}
                      </ul>
                    </div>
                  </div>
                </nav>
              </header>
              
              <section class="jumbotron text-center">
                <div class="container">
                  <h1 class="display-4">Bem-vindo ao {{$buffet->trading_name}}!</h1>
                  <p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                  <div class= "flex">
                    <iframe src="{{ route('booking.calendar', ['buffet'=>$buffet->slug]) }}" frameborder="1" width="100%" height="800px"></iframe>
                      <a href="#" class="btn btn-primary">Saiba Mais</a>
                  </div>
                </div>
            </section>
            
            <section class="features">
                <div class="container">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="feature">
                        <i class="fas fa-rocket fa-3x mb-2"></i>
                        <h3>Velocidade</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="feature">
                        <i class="fas fa-cogs fa-3x mb-2"></i>
                        <h3>Flexibilidade</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="feature">
                        <i class="fas fa-heart fa-3x mb-2"></i>
                        <h3>Suporte</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection