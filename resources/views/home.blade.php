@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">

        @include('layouts.menu')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                        <div class="row justify-content-center">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert" style="text-align: center;">
                                    <h5>{{ session('status') }}</h5>
                                </div>
                            @endif
                            <div class="col-md-7">
                                <div class="card">
                                    <div class="card-header"><h4>{{ 'Clients' }}</h4></div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                        @foreach(App\Models\Client::all() as $c)
                                                <a href="{{url('/home/clients/' . $c->id)}}" class="list-group-item list-group-item-action" >
                                                    <h5 >
                                                        {{$c->name}} &nbsp; &nbsp; {{$c->email}}
                                                        <span class="badge bg-primary position-absolute top|start-*" style="position: relative; right: 0;">{{$c->telephone}}
                                                        @if($c->active)
                                                            <span class="position-absolute top-0 start-100
                                                                         translate-middle p-2 rounded-pill
                                                                         bg-success border border-light
                                                                         rounded-circle badge">
                                                                <span class="visually-hidden">
                                                                    Notifications of newly launched courses
                                                                </span>
                                                            </span>

                                                        @else

                                                            <span class="position-absolute top-0 start-100
                                                                         translate-middle p-2 rounded-pill
                                                                         bg-danger border border-light
                                                                         rounded-circle badge">
                                                                <span class="visually-hidden">
                                                                    Notifications of newly launched courses
                                                                </span>
                                                            </span>
                                                        @endif
                                                        </span>

                                                    </h5>
                                                </a>
                                        @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header"><h4>{{ 'Last Transaction' }}</h4></div>
                                </div>
                            </div>
                        </div>

                </div>

                <div class="card-footer">
                    {{'Footer'}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
