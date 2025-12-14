@php
    use App\Models\Reward;use App\Models\Voucher
  ; use Illuminate\Support\Carbon
  ; use App\Http\Controllers\GuestController;
@endphp
@extends('layouts.app-super_admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.super-admin-menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5 style="display: inline;"><strong>{{ 'Details de la licence: ' . $licence->license_key }}</strong></h5>
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

                        <div class="list-group list-group-flush alert alert-{{($licence->active)?'success':'danger'}}">
                            <?php
                                $assignedUsers = $licence->metadata['users'];
                                $allUsers = \App\Models\User::all();
                                $users = [];
                                foreach ($allUsers as $aUser){
                                    $isAssigned = false;
                                    foreach ($assignedUsers as $assignedUser){
                                        if ($aUser->id === $assignedUser['id']){
                                            $isAssigned = true;
                                            break;
                                        }
                                    }
                                    if (!$isAssigned){
                                        $users[] = $aUser;
                                    }
                                }
                            ?>

                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Cle: &nbsp; &nbsp; {{$licence->license_key}}
                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    <?php
                                    $numberOfDays = 0;
                                    $expiration = Carbon::parse($licence->expires_at);
                                    $now = Carbon::now();
                                    if ($expiration->isAfter($now)){
                                        $numberOfDays = $now->diffInDays($expiration);
                                    }
                                    ?>
                                    {{ 'Nb. Jour Restant: ' }} {{$numberOfDays > 0 ? round($numberOfDays) . ' jours' : ('Expire le: ' . $expiration->format('d-m-Y H:i:s'))}}

                                </h5>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action"
                               style="margin-left: 15px; width: 98%;">
                                <h5>
                                    Etat: &nbsp;
                                    <?php
                                    $isValid = $service->validateLicense($licence->license_key);

                                    ?>
                                    @if($isValid)
                                       {{'Actif'}}
                                    @else
                                        {{ 'Expire/Desactive'  }}
                                    @endif
                                </h5>
                            </a>
                            @if(count($assignedUsers) > 0)
                                <a href="#" class="list-group-item list-group-item-action"
                                   style="margin-left: 15px; width: 98%;">
                                    <h5>{{ __('Utilisateurs') }}</h5>
                                    <ol>
                                        @foreach($assignedUsers as $assignedUser)
                                            <li>{{ $assignedUser['name'] }} ({{ $assignedUser['email'] }})</li>
                                        @endforeach
                                    </ol>
                                    {{--<h5>
                                        Cle: &nbsp; &nbsp; {{$licence->license_key}}
                                    </h5>--}}
                                </a>
                            @endif
                        </div>

                        <div class="alert alert-{{($licence->active)?'success':'danger'}}"
                             style="padding-left: 10px; padding-right: 10px;">
                            @if($licence->active)
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirm-desactiver-license-modal">
                                    {{ 'Desactiver la licence' }}
                                </button>
                                <div class="modal fade" id="confirm-desactiver-license-modal" data-bs-backdrop="static"
                                     data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous souhaitez
                                                    desactiver la licence <strong
                                                        style="color: darkred;">{{$licence->license_key}}</strong></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <form method="POST"
                                                  action="{{url('/'.GuestController::getApplicationLocal().'/home-super-admin/licences/' . $licence->id . '/deactivate')}}"
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

                                                    <input type="hidden" name="licensid" value="{{$licence->id}}">

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-success">Desactiver la licence
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#add-user-to-license-modal">
                                    {{ __('Ajouter un utilisateur') }}
                                </button>
                                <div class="modal fade" id="add-user-to-license-modal" data-bs-backdrop="static"
                                     data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous souhaitez
                                                    Ajouter un utilisateur a la licence <strong
                                                        style="color: darkred;">{{$licence->license_key}}</strong></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <form method="POST"
                                                  action="{{url('/'.GuestController::getApplicationLocal().'/home-super-admin/licences/' . $licence->id . '/add-user')}}"
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

                                                    <div class="row mb-3" >

                                                        <label for="userid" class="col-md-5 col-form-label text-md-end">{{ 'Utilisateur' }}
                                                            <b class="" style="color: red;">*</b></label>

                                                        <div class="col-md-7">
                                                            <select id="userid"  class="form-control @error('userid') is-invalid @enderror"
                                                                    name="userid"  required autocomplete="userid" autofocus
                                                                    >
                                                                <option value="">Choisir ici</option>
                                                                @foreach($users as $user)
                                                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('userid')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <input type="hidden" name="licensid" value="{{$licence->id}}">

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-success">Assigner la licence</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else

                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#confirm-activer-license-modal">
                                    {{ 'Activer la licence' }}
                                </button>
                                <div class="modal fade" id="confirm-activer-license-modal" data-bs-backdrop="static"
                                     data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous souhaitez
                                                    Activer la licence <strong
                                                        style="color: darkred;">{{$licence->license_key}}</strong></h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <form method="POST"
                                                  action="{{url('/'.GuestController::getApplicationLocal().'/home-super-admin/licences/' . $licence->id . '/activate')}}"
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

                                                    <input type="hidden" name="licensid" value="{{$licence->id}}">

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-success">Activer la licence
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
