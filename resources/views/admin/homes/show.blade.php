@extends('layouts.app')

@section('content')

<div class="home-show p-5">
    <div class="container">
        <div class="button-manage">
            {{--? bottone indietro --}}
            <div class="back">
                <a href="{{route('admin.homes.index') }}">{{ __('Indietro')}}</a>
            </div>

            {{--? bottoni gestione --}}
            <div class="manage">
                <div class="create">
                    <a href="{{route('admin.homes.create') }}">{{ __('Crea Nuovo')}}</a>
                </div>
                <a href="{{route('admin.homes.edit', $home)}}" class="ml-45 mr-10">
                    <i class="fas fa-pen orange"></i>
                </a>
                <form action="{{route('admin.homes.destroy', $home)}}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="border-0 bg-transparent"><a href=""><i class="fas fa-trash"></i></a></button>
                </form>

            </div>
        </div>

        {{--? dettaglio informazioni --}}
        <div class="card p-5">
            <h2>{{$home->title}}</h2>
            <hr class="mb-5">

            <div class="crd">
                <div class="image">
                    {{--? immagine da url --}}
                    @if (Str::startsWith($home->image, 'http'))
                    <img src="{{ $home->image }}" alt="{{ $home->title }}" class="rounded">
                    @else
                    {{--? immagine da storage --}}
                    <img src="{{ asset('storage/' . $home->image) }}" alt="{{ $home->title }}" class="rounded">
                    @endif
                </div>
                <div class="text">
                    <ul>
                        <li class="mb-3">
                            <span>Indirizzo: </span>{{$home->address}}
                        </li>
                        <li class="mb-3">
                            <span>Numero Stanze: </span>{{$home->rooms}}
                        </li>
                        <li class="mb-3">
                            <span>Numero Letti: </span>{{$home->beds}}
                        </li>
                        <li class="mb-3">
                            <span>Numero Bagni: </span>{{$home->bathrooms}}
                        </li>
                        <li class="mb-3">
                            <span>Superficie: </span>{{$home->square_metres}} mq
                        </li>
                        <li class="mb-3">
                            <span>Servizi: </span>
                            @forelse ($home->services as $service)
                            {{ $service->name }} {{ !$loop->last ? ',' : '' }}
                            @empty
                            Nessuna servizio selezionato
                            @endforelse
                        </li>
                        <li class="mb-3">
                            @forelse ($home->ads as $ad )
                            <p><span>tipo di sponsorizzata </span>{{$ad->title}}</p>
                                @foreach ($sponsorships as $sponsorship)
                                    @if ($sponsorship['ad_id'] == $ad->id)
                                        <p><span>Tempo rimanente: </span>{{ $sponsorship['remaining_time'] }}</p>
                                    @endif
                                @endforeach
                            <p><span>data inizio </span>{{$ad->created_at->format('d/m/Y')}}</p>                               
                            @empty
                                <p>Non ci sono sponsorizzate!</p>
                            @endforelse
                        </li>
                    </ul>
                </div>
            </div>
            <p>
                <span>Descrizione: </span>{{$home->description}}
            </p>

            {{--? visualizzazioni & bottone per la sponsorizzazione --}}
            <div class="view-ads">
                <div class="view mt-4">
                    <p>
                        <span>visualizzazioni: </span>

                    </p>
                </div>
                <div class="ads button-manage text-end my-5">
                    <div class="back">
                        <a href="{{ route('payment.form', $home->slug) }}">
                            Compra Visibilità
                        </a>
                    </div>
                </div>

                {{--? messaggio di avvenuto pagamento --}}
                @if (session('success'))
                    <div class="alert alert-success my-3" id="messaggio">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <hr class="mt-4">
            {{--? messaggi da frontend: --}}
            <div class="row row-gap-3 my-4">
                <h3 class="com">Messaggi:</h3>
                @forelse ($home->messages->reverse() as $message)
                <div class="col-12">
                    <div class="card-body">
                        <h4 class="card-title">{{ $message ->name}}</h4>
                        <em>{{ $message ->email}}</em>
                        <p class="card-text">{{ $message ->content }}</p>
                    </div>
                </div>
                @empty
                <div class="">
                    <h3>Non ci sono messaggi</h3>
                </div>
                @endforelse
            </div>

        </div>
    </div>

    @endsection

   @section('scripts')
   <script src="{{ asset('js/timeout.js') }}"></script>    
   @endsection

    
   