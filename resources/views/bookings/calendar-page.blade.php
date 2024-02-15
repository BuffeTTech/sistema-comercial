@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Horários'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Horário de festas</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="px-2">
                            <div class="card card-calendar">
                                <div class="card-body p-3">
                                  <div class="calendar" data-bs-toggle="calendar" id="calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script type="module">
        document.addEventListener('DOMContentLoaded', async function() {
            const SITEURL = "{{ url('/') }}";

            async function getEvents() {
                const csrf = document.querySelector('meta[name="csrf-token"]').content
                const data = await axios.get(SITEURL + '/api/{{$buffet->slug}}/booking/calendar', {
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    }
                });
                const events = data.data.map((dt) => {
                    // Obtenha a data e hora do evento
                    const dataEvento = new Date(dt.party_day + "T" + dt.schedule.start_time);
                    
                    // Formate a data e hora do evento no formato brasileiro (12 horas)
                    const horaInicio = dataEvento.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                    
                    // Calcule a hora de término adicionando a duração do evento
                    const horaTermino = new Date(dataEvento.getTime() + dt.schedule.duration * 60000)
                        .toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

                    return {
                        title: `${horaInicio} - ${horaTermino}`,
                        start: dataEvento,
                        end: new Date(dataEvento.getTime() + dt.schedule.duration * 60000), // Adiciona a duração ao tempo de início para obter o tempo final
                        className: 'bg-success'
                    };
                });
                return events;
            }

            const eventos = await getEvents();
            console.log(eventos)

            var calendar = new FullCalendar.Calendar(document.getElementById("calendar"), {
                initialView: "dayGridMonth",
                headerToolbar: {
                    start: 'title', // will normally be on the left. if RTL, will be on the right
                    center: '',
                    end: 'today prev,next' // will normally be on the right. if RTL, will be on the left
                },
                selectable: false,
                editable: false,
                initialDate: new Date(),
                events: eventos,
                contentHeight: 650,
                aspectRatio: 2,
                views: {
                    month: {
                    titleFormat: {
                        month: "long",
                        year: "numeric"
                    }
                    },
                    agendaWeek: {
                    titleFormat: {
                        month: "long",
                        year: "numeric",
                        day: "numeric"
                    }
                    },
                    agendaDay: {
                    titleFormat: {
                        month: "short",
                        year: "numeric",
                        day: "numeric"
                    }
                    }
                },
                });
            calendar.render();
            });
</script>