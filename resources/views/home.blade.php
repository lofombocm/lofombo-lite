@php
    use App\Models\Config;
    use App\Models\Notification;
    use Illuminate\Support\Carbon;use Illuminate\Support\Facades\Auth;

    $notifications = Notification::where('sender_address', Auth::user()->email)->where('read', false)->get();
    $unreadMsgNum = count($notifications);
@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                {{--<div class="card">--}}
                    {{--<div class="card-header">{{ __('Dashboard') }}</div>--}}
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
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 style="display: inline; float: left;">{{ 'Clients' }}</h4>
                                        <h5 style="display: inline; float: right;">
                                            @if(count(Config::where('is_applicable', true)->get()) > 0)
                                                <a href="{{ route('clients.index')}}"
                                                   style="text-decoration: none; font-size: x-large; color: green;"
                                                   id="add_level_field"
                                                   title="Ajouter un client">
                                                    <strong><span class="glyphicon glyphicon-plus">+</span></strong>
                                                    <span style="font-size: initial;">{{ 'Ajouter' }}</span>
                                                </a>
                                            @endif
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            @foreach(App\Models\Client::all() as $c)
                                                <br>
                                                <a href="{{url('/home/clients/' . $c->id)}}"
                                                   class="list-group-item list-group-item-action">
                                                    <h5>
                                                        {{$c->name}} &nbsp; &nbsp; {{$c->email}}
                                                        <span class="badge bg-primary position-absolute top|start-*"
                                                              style="position: relative; right: 0;">{{$c->telephone}}
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
                                                        <br>
                                                        <span class="badge bg-light position-absolute top|start-*"
                                                              style="position: relative; right: 0; margin-top: 5px;">
                                                            <?php
                                                                $loyaltyaccount = \App\Models\Loyaltyaccount::where('holderid', $c->id)->first();
                                                                ?>
                                                            <strong
                                                                style="color: #6f42c1;">Solde point de fidelite: {{$loyaltyaccount->point_balance}}</strong>
                                                        </span>

                                                    </h5>
                                                    <br>
                                                </a>

                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--<div class="col-md-5">
                                <div class="card">
                                    <div class="card-header"><h4>{{ 'Last Transaction' }}</h4></div>
                                </div>
                            </div>--}}
                        </div>

                    {{--</div>--}}

                    {{--<div class="card-footer">
                        {{' '}}
                    </div>--}}
                </div>
            </div>

            <div class="modal fade modal-lg" id="notifications-modal" data-bs-backdrop="static"
                 data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                     style="overflow-y: initial; width: 75%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel"
                                style="border: 0 red solid; width: 100%;">
                                <strong
                                    style="color: darkred;">Notifications</strong>
                                <span style="
                                            position:relative;
                                            right: 0;
                                            float: right;
                                            color: darkred;
                                            font-size: x-large;"><strong>{{$unreadMsgNum}}</strong></span>
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        {{--<form method="POST" action="{{route('configuration.post')}}"
                              enctype="multipart/form-data" onsubmit="return true;" >--}}
                        <div class="modal-body" style="height: 80vh; overflow-y: auto;">

                            <input type="hidden" name="error" id="error"
                                   class="form-control @error('error') is-invalid @enderror">
                            @error('error')
                            <span class="invalid-feedback" role="alert"
                                  style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                            @enderror

                            @csrf

                            <div class="card">
                                <div class="card-body">
                                    {{--<div class="list-group list-group-flush">--}}
                                        @foreach($notifications as $notification)
                                            <adiv class="list-group-item list-group-item-action">
                                                <h5>
                                                    Objet: <strong>{{$notification->subject}}</strong> &nbsp; &nbsp;
                                                    <span class="badge bg-primary position-absolute top|start-*"
                                                          style="position: relative; right: 0; font-size: small;">
                                                        @php
                                                            $sent_at = Carbon::parse($notification->sent_at);
                                                        @endphp
                                                        Le: {{$sent_at->day . '-' . $sent_at->month . '-' . $sent_at->year . ' a ' . $sent_at->hour . ':' . $sent_at->minute . ':' . $sent_at->second}}
                                                        </span>
                                                </h5>
                                                <h5 style="font-size: small;">
                                                    De: {{$notification->sender}}
                                                    <a href="{{route('notifications.index', $notification->id)}}"
                                                        style="position: relative; right: 0; float:right; text-decoration: none; margin-top: 5px;">
                                                        {{'Details'}}
                                                    </a>
                                                </h5>
                                               {{--<br><br>--}}
                                            </adiv>

                                        @endforeach
                                    {{--</div>--}}
                                </div>
                            </div>

                        </div>
                        {{--<div class="modal-footer">
                            <button type="button" class="btn btn-danger"
                                    data-bs-dismiss="modal">Annuler
                            </button>
                            <button type="submit" class="btn btn-success">Enregistrer
                            </button>
                        </div>--}}
                        {{--</form>--}}


                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
