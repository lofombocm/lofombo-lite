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
                <div class="card-body">

                    <div class="row justify-content-center">
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('status'))
                            <div class="alert alert-success" role="alert" style="text-align: center;">
                                <h5>{{ session('status') }}</h5>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 style="display: inline; float: left;">{{ __('Invitations') }} </h4>
                                </div>
                                <div class="card-body">
                                    @if(count($friendInvitationAccepteds)) @endif
                                    <table class="table table-striped table-responsive table-bordered">
                                        <thead class="" style="color: darkred;">
                                        <th scope="col">
                                            {{ __('Nom') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('Téléphone') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('Email') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('Invité par') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('Invité le') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('Accepté le') }}
                                        </th>
                                        <th scope="col">
                                            {{ 'Actions' }}
                                        </th>
                                        </thead>
                                        <tbody>
                                        @foreach($friendInvitationAccepteds as $invitation)
                                                <?php
                                                $invitationData = json_decode($invitation->sent_data);
                                                $client = Client::where('id', $invitationData->invited_by)->first();
                                                ?>
                                            <tr>
                                                <th>
                                                    <h5>{{$invitationData->name}}</h5>
                                                </th>
                                                <td>
                                                    <h5>{{$invitationData->telephone}}</h5>
                                                </td>
                                                <td>
                                                    <h5>{{$invitationData->email ? $invitationData->email : 'N/D'}}</h5>
                                                </td>
                                                <td>
                                                    <h5>{{$client->name}}</h5>
                                                </td>
                                                <td>
                                                    <h5>{{\Illuminate\Support\Carbon::parse($invitation->created_at)->format('d-m-Y H:i:s')}}</h5>
                                                </td>
                                                <td>
                                                    <h5>{{\Illuminate\Support\Carbon::parse($invitation->updated_at)->format('d-m-Y H:i:s')}}</h5>
                                                </td>
                                                <td>
                                                    <a href="{{url('/'.GuestController::getApplicationLocal().'/home/client-invitations/' . $invitation->id)}}"
                                                       class="list-group-item list-group-item-action">
                                                        <img src="{{asset('images/icons8-right-chevron-25.png')}}" alt=">"/>
                                                    </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>


                                    {{--<div class="list-group list-group-flush">
                                        @foreach(Client::all() as $c)
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
                                    </div>--}}
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
        </div>
    </div>
@endsection
