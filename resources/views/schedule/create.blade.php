@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Perguntas', 'subtitle'=>'Criar Pergunta'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Pesquisa de satisfação</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-4">
                            <form method="POST" action="{{ route('schedule.store', ['buffet'=>$buffet->slug]) }}" id="form">
                                @csrf
                                <div class="form-group">
                                    <label for="day_week" class="form-control-label">Dia da semana</label>
                                    <select name="day_week" id="day_week" class="form-control" required>
                                        @foreach( App\Enums\DayWeek::array() as $key=>$day_week )
                                            <option value="{{$day_week}}" {{ old('day_week') == $day_week ? 'selected' : ''}}>{{$key}}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('day_week')" class="mt-2" />
                                </div>
                                <div class="form-group">
                                    <label for="start_time" class="form-control-label">Horário de Inicio</label>
                                    <input class="form-control" type="time" id="start_time" name="start_time" required>
                                    <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                                </div>
                                <div class="form-group">
                                    <label for="duration" class="form-control-label">Duração</label>
                                    <input class="form-control" type="number" step="1" id="duration" name="duration" required>
                                    <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                                </div>

                                <button class="btn btn-primary" type="submit">Cadastrar Horário</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div> 
    <script>
        const form = document.querySelector("#form")
    
        form.addEventListener('submit', async function(e) {
            e.preventDefault()
            const userConfirmed = await confirm(`Deseja criar este horário?`)
    
            if (userConfirmed) {
                this.submit();
            } else {
                error("Ocorreu um erro!")
            }
        })
    </script> 
@endsection
