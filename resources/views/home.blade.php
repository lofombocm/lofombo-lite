@php
    use App\Models\Config;
    use App\Models\Loyaltyaccount;
    use App\Models\Notification;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Client;
    use App\Http\Controllers\GuestController;

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
                                    <h4 style="display: inline; float: left;">{{ __('Clients') }} </h4>
                                    <h5 style="display: inline; float: right;">
                                        @if(count(Config::where('is_applicable', true)->get()) > 0)
                                            <a href="{{ route('clients.index')}}"
                                               style="text-decoration: none; font-size: x-large; color: green;"
                                               id="add_level_field"
                                               title="{{ __('Ajouter un client') }}">
                                                <strong><span class="glyphicon glyphicon-plus">+</span></strong>
                                                <span style="font-size: initial;">{{ __('Ajouter') }}</span>
                                            </a>
                                        @endif
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(count(Client::all())) @endif
                                    <table class="table table-striped table-responsive table-bordered">
                                        <thead class="" style="color: darkred;">
                                        <th scope="col">
                                            {{ __('Nom') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('Numéro Tel') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('Email') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('Points de fidélité') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('Actions') }}
                                        </th>
                                        </thead>
                                        <tbody>
                                        @foreach(Client::all() as $c)
                                                <?php
                                                $loyaltyaccount = Loyaltyaccount::where('holderid', $c->id)->first();
                                                ?>
                                            <tr>
                                                <th>
                                                    <h5>{{$c->name}}</h5>
                                                </th>
                                                <td>
                                                    <h5>{{$c->telephone}}</h5>
                                                </td>
                                                <td>
                                                    <h5>{{$c->email ? $c->email : 'N/D'}}</h5>
                                                </td>
                                                <td>
                                                    <h5>{{intval(strval(decrypt(($loyaltyaccount->point_balance)))}}</h5>
                                                </td>
                                                <td>
                                                    <a href="{{url('/'.GuestController::getApplicationLocal().'/home/clients/' . $c->id)}}"
                                                       class="list-group-item list-group-item-action">
                                                        <img src="{{asset('images/icons8-right-chevron-25.png')}}" alt=">"/>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
