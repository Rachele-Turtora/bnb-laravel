@extends('layouts.app')

@section('content')
<div class="home-show">
    <div class="dashboard">
        <div class="container">
            <div class="row justify-content-center" id="houdini">
                <div class="col mt-4">
                    <div class="form">
                        <div class="form-header">{{ __('Dashboard') }}</div>
    
                        <div class="form-body">
                            @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                            @endif
    
                            {{ __('You are logged in!') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card no-hover mt-5" >
                <div class="bg-btn pt-4 px-5">
                    <h3 class="mb-4 color-text2">Dati Utente<small>:</small></h3>
                </div>
                <div class="pb-4 px-5">
                    <div class="mt-5 user-data row">
                        <p class="col-sm-12 col-md-6"><span>Nome<small>:</small></span> {{$user->name}}</p>
                        <p class="col-sm-12 col-md-6"><span>Conome<small>:</small></span> {{$user->surname}}</p>
                        <p class="col-sm-12 col-md-6"><span>Data di Nascita<small>:</small></span> {{ \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y') }}</p>
                        <p class="col-sm-12 col-md-6"><span>Email<small>:</small></span> {{$user->email}}</p>
                    </div>
                    <hr>
                    <div class="my-4 row">
                        <h4 class="color-text">Le tue case:
                            @if ($homes->count())
                            <small class="color-text3">Totale {{$homes->count()}}</small></h4> 
                            @else
                            <small class="color-text3">0</small>
                            @endif
                        @forelse ( $homes as $home )
                        <p class="col-sm-12 col-xl-6">
                            <span><i class="fas fa-circle"></i><small></small></span> {{$home->title}} 
                            <span><i class="fas fa-location-arrow ms-4 me-2"></i></span>
                             {{$home->address}}
                        </p>                           
                        @empty
                        <p class="color-text3 mt-3">Non hai inserito nessuna casa!</p> 
                        <h4 class="color-text fs-4">Inserisci una casa:</h4>
                        <div class="manage mt-4">
                            <div class="create">
                                <a href="{{route('admin.homes.create') }}">{{ __('Crea Nuovo')}}</a>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
    
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="{{ asset('js/timeout.js') }}"></script>
@endsection