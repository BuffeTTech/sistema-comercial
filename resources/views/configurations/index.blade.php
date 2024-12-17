@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
<style>
    .form-children {
        display: none;
    }
</style>

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Configuracoes'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Configurações</h6>
                    </div>
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-2">
                            <form action="{{ route('configurations.update', ['buffet'=>$buffet->slug]) }}" method="POST">
                                @csrf
                                <div>
                                    <h3>Configurações Tradicionais</h3>
                                    <div class="form-group">
                                        <label for="min_days_booking" class="form-control-label">Minimo de dias de antecedencia</label>
                                        <input class="form-control" type="number" placeholder="Minimo de dias de antecedencia" id="min_days_booking" name="min_days_booking" value="{{ old('min_days_booking') ?? $configuration->min_days_booking }}">
                                        <x-input-error :messages="$errors->get('min_days_booking')" class="mt-2" />
                                    </div>
                                    <div class="form-group">
                                        <label for="max_days_unavaiable_booking" class="form-control-label">Maximo de dias que a reserva indisponivel pode recomendar</label>
                                        <input class="form-control" type="number" placeholder="Maximo de dias que a reserva indisponivel pode recomendar" id="max_days_unavaiable_booking" name="max_days_unavaiable_booking" value="{{ old('max_days_unavaiable_booking') ?? $configuration->max_days_unavaiable_booking }}">
                                        <x-input-error :messages="$errors->get('max_days_unavaiable_booking')" class="mt-2" />
                                    </div>
                                </div>
                                <hr>
                                <div>
                                    <h3>Links</h3>
                                    <p>Insira a URL que redireciona até cada uma das redes</p>
                                    <div class="form-group">
                                        <label for="buffet_instagram" class="form-control-label">Instagram do Buffet</label>
                                        <input class="form-control" type="text" placeholder="Instagram do Buffet" id="buffet_instagram" name="buffet_instagram" value="{{ old('buffet_instagram') ?? $configuration->buffet_instagram }}">
                                        <x-input-error :messages="$errors->get('buffet_instagram')" class="mt-2" />
                                    </div>
                                    <div class="form-group">
                                        <label for="buffet_linkedin" class="form-control-label">Linkedin do Buffet</label>
                                        <input class="form-control" type="text" placeholder="Instagram do Buffet" id="buffet_linkedin" name="buffet_linkedin" value="{{ old('buffet_linkedin') ?? $configuration->buffet_linkedin }}">
                                        <x-input-error :messages="$errors->get('buffet_linkedin')" class="mt-2" />
                                    </div>
                                    <div class="form-group">
                                        <label for="buffet_facebook" class="form-control-label">Facebook do Buffet</label>
                                        <input class="form-control" type="text" placeholder="Facebook do Buffet" id="buffet_facebook" name="buffet_facebook" value="{{ old('buffet_facebook') ?? $configuration->buffet_facebook }}">
                                        <x-input-error :messages="$errors->get('buffet_facebook')" class="mt-2" />
                                    </div>
                                    <div class="form-group">
                                        <label for="buffet_whatsapp" class="form-control-label">WhatsApp do Buffet</label>
                                        <input class="form-control" type="text" placeholder="WhatsApp do Buffet" id="buffet_whatsapp" name="buffet_whatsapp" value="{{ old('buffet_whatsapp') ?? $configuration->buffet_whatsapp }}">
                                        <x-input-error :messages="$errors->get('buffet_whatsapp')" class="mt-2" />
                                        <x-input-helper :value="'Insira a API do WhatsApp sem mensagem padrão, como por exemplo https://wa.me/19987654321'" />
                                    </div>
                                </div>
                                <div>
                                    <h3>Das festas</h3>
                                    <div class="form-group">
                                        <label class="form-control-label">Decoração</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="external_decoration" name="external_decoration"
                                                @if (old('external_decoration') ?? $configuration->external_decoration) checked @endif>
                                            <label class="form-check-label" for="external_decoration">Permite decoração externa</label>
                                        </div>
                                        <x-input-error :messages="$errors->get('external_decoration')" class="mt-2" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">Horários</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="charge_by_schedule" name="charge_by_schedule" 
                                                @if (old('charge_by_schedule') ?? $configuration->charge_by_schedule) checked @endif>
                                            <label class="form-check-label" for="charge_by_schedule">Cobra pelos horários</label>
                                          </div>
                                        <x-input-error :messages="$errors->get('charge_by_schedule')" class="mt-2" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">Pagamento</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="allow_post_payment" name="allow_post_payment" 
                                                @if (old('allow_post_payment') ?? $configuration->allow_post_payment) checked @endif>
                                            <label class="form-check-label" for="allow_post_payment">Permite pagamento posterior</label>
                                          </div>
                                        <x-input-error :messages="$errors->get('allow_post_payment')" class="mt-2" />
                                    </div>
                                    <div class="row my-4">
                                        <div class="col-md-4">
                                            <label class="form-control-label">Convidados</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="children_affect_pricing" name="children_affect_pricing"
                                                    @if (old('children_affect_pricing') ?? $configuration->children_affect_pricing) checked @endif>
                                                <label class="form-check-label" for="children_affect_pricing">Crianças afetam os preços?</label>
                                            </div>
                                            <x-input-error :messages="$errors->get('children_affect_pricing')" class="mt-2" />
                                        </div>
                                        <div class="form-group col-md-4 form-children">
                                            <label for="children_price_adjustment" class="form-control-label">O quanto afeta?</label>
                                            <input class="form-control" type="number" placeholder="O quanto afeta?" id="children_price_adjustment" value="{{ old('children_price_adjustment') ?? $configuration->children_price_adjustment }}" name="children_price_adjustment" value="{{ old('children_price_adjustment') }}">
                                            <x-input-error :messages="$errors->get('children_price_adjustment')" class="mt-2" />
                                        </div>
                                        <div class="form-group col-md-4 form-children">
                                            <label for="child_age_limit" class="form-control-label">Até que idade considera criança?</label>
                                            <input type="range" min="0" step="1" class="form-range" id="disabledRange" id="child_age_limit" value="{{ old('child_age_limit') ?? $configuration->child_age_limit }}" name="child_age_limit" value="{{ old('child_age_limit') }}">
                                            <x-input-error :messages="$errors->get('child_age_limit')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-primary">Atualizar Configurações</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkbox = document.getElementById('children_affect_pricing');
                const formChildren = document.querySelectorAll('.form-children');
        
                const toggleChildrenFields = () => {
                    if (checkbox.checked) {
                        formChildren.forEach(formGroup => {
                            formGroup.style.display = 'block';
                        });
                    } else {
                        formChildren.forEach(formGroup => {
                            formGroup.style.display = 'none';
                            formGroup.querySelector('input').value = '';
                        });
                    }
                };
        
                toggleChildrenFields();
                checkbox.addEventListener('change', toggleChildrenFields);
            });
        </script>
        @include('layouts.footers.auth.footer')
    </div>
@endsection
