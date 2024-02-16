@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dashboard'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Meus Buffets</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-4">
                        @foreach($buffets as $user_buffet)
                            <div class="bg-gray-500 p-4 my-4">
                                <h2>{{ $user_buffet->trading_name }}</h2>
                                <a href="{{ route('buffet.dashboard', ['buffet'=>$user_buffet->slug]) }}">Ver buffet</a>
                                |
                                <a href="{{ route('buffet.edit', ['buffet'=>$user_buffet->slug]) }}">Editar buffet</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
