                    <h1>Meus Buffets</h1>
                    <br>
                    @foreach($buffets as $buffet)
                        <div class="bg-gray-500">
                            <h2>{{ $buffet->trading_name }}</h2>
                            <a href="{{ route('buffet.dashboard', ['buffet'=>$buffet->slug]) }}">Ver buffet</a>
                            |
                            <a href="{{ route('buffet.edit', ['buffet'=>$buffet->slug]) }}">Editar buffet</a>
                        </div>
                    @endforeach