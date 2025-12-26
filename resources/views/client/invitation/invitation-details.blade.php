@php
    use App\Models\Client
    ;use App\Models\Reward
    ;use App\Models\Voucher
    ;use Illuminate\Support\Carbon
    ;use App\Http\Controllers\GuestController
    ;

  $invitationData = json_decode($invitation->sent_data);
  $client = Client::where('id', $invitationData->invited_by)->first();

@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5 style="display: inline;"><strong>{{ __("Détails de l'invité") . ': ' . $invitationData->name }}</strong></h5>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="list-group list-group-flush alert alert-{{($invitation->state === \App\Models\FriendInvitatin::ACCEPTED)?'success':'info'}}">
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{__("Nom")}}: &nbsp; &nbsp; {{$invitationData->name}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{__("Telephone")}}: &nbsp; &nbsp; {{$invitationData->telephone}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{__("Email")}}: &nbsp;
                                    @if($invitationData->email == null)
                                        {{'N/D'}}
                                    @else
                                        {{$invitationData->email }}
                                    @endif
                                </h5>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{ __("Date de Naissance") }}: {{$invitationData->birthdate}}
                                </h5>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{ __('Sexe') }}: &nbsp;&nbsp;
                                    @if($invitationData->gender == null)
                                        {{'N/D'}}
                                    @else
                                        {{$invitationData->gender === 'M' ? __("Masculin") : __("Féminin")}}
                                    @endif

                                </h5>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{ __("City") }}: &nbsp;&nbsp;
                                    @if($invitationData->city == null)
                                        {{'N/D'}}
                                    @else
                                        {{$invitationData->city}}
                                    @endif
                                </h5>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{ __("Lieu de résidence") }}: &nbsp;&nbsp;
                                    @if($invitationData->quarter == null)
                                        {{'N/D'}}
                                    @else
                                        {{$invitationData->quarter}}
                                    @endif
                                </h5>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    {{ __("Invité par") }}: &nbsp; &nbsp;{{ $client->name }}
                                </h5>
                            </a>
                        </div>

                        <div class="alert alert-{{($invitation->state === \App\Models\FriendInvitatin::ACCEPTED)?'success':'info'}}"
                             style="padding-left: 10px; padding-right: 10px;">
                            @if($invitation->state === \App\Models\FriendInvitatin::ACCEPTED)
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#confirm-client-invitation-modal">
                                    {{ __('Confirmer le client') }}
                                </button>

                                <div class="modal fade" id="confirm-client-invitation-modal" data-bs-backdrop="static"
                                     data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                    {{__("Vous souhaitez confirmer l'invitation de")}}
                                                    <strong style="color: darkred;">{{$invitationData->name}}</strong></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{url('/'.GuestController::getApplicationLocal().'/home/client-invitations/' . $invitation->id)}}"
                                                  onsubmit="return true;">
                                                <div class="modal-body">
                                                    <input type="hidden" name="error" id="error"
                                                           class="form-control @error('error') is-invalid @enderror">
                                                    @error('error')
                                                    <span class="invalid-feedback" role="alert"
                                                          style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                                                    @enderror
                                                    @csrf
                                                    <input type="hidden" name="clientid" value="{{$client->id}}">
                                                    <input type="hidden" name="userid" value="{{\Illuminate\Support\Facades\Auth::user()->id}}" id="userid">
                                                    <input type="hidden" name="invitationid" value="{{$invitation->id}}" id="invitationid">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-success">{{__("Confirmer")}}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#confirm-refuse-client-invitation-modal">
                                    {{ __('Refuser le client') }}
                                </button>

                                <div class="modal fade" id="confirm-refuse-client-invitation-modal" data-bs-backdrop="static"
                                     data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                    {{__("Vous souhaitez refuser l'invitation de: ")}}
                                                    <strong style="color: darkred;">{{$invitationData->name}}</strong></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{url('/'.GuestController::getApplicationLocal().'/home/client-invitations-refuse/' . $invitation->id)}}"
                                                  onsubmit="return true;">
                                                <div class="modal-body">
                                                    <input type="hidden" name="error" id="error"
                                                           class="form-control @error('error') is-invalid @enderror">
                                                    @error('error')
                                                    <span class="invalid-feedback" role="alert"
                                                          style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                                                    @enderror
                                                    @csrf
                                                    <input type="hidden" name="clientid" value="{{$client->id}}">
                                                    <input type="hidden" name="userid" value="{{\Illuminate\Support\Facades\Auth::user()->id}}" id="userid">
                                                    <input type="hidden" name="invitationid" value="{{$invitation->id}}" id="invitationid">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-success">{{__("Refuser")}}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @else

                                <div style="display: none;">{{__("Aucune action prévue")}}</div><br>

                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
