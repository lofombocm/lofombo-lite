@php use App\Models\Client;use Illuminate\Support\Carbon; @endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5 style="display: inline;"><strong>{{ 'Bons du systeme' }}</strong></h5>
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

                        @if(count($vouchers) > 0)
                            <table class="table table-striped table-responsive table-bordered">
                                <thead class="" style="color: darkred;">
                                <th scope="col">
                                    {{ 'No. Serie' }}
                                </th>
                                <th scope="col">
                                    {{ 'Client' }}
                                </th>
                                <th scope="col">
                                    {{ 'Niveau' }}
                                </th>
                                <th scope="col">
                                    {{ 'Points' }}
                                </th>
                                <th scope="col">
                                    {{ 'Expiration' }}
                                </th>
                                <th scope="col">
                                    {{ 'Statut' }}
                                </th>
                                <th scope="col">
                                    {{ 'Actions' }}
                                </th>
                                </thead>
                                <tbody>
                                @foreach($vouchers as $voucher)
                                        <?php
                                        $client = Client::where('id', $voucher->clientid)->first();
                                        //$type = $voucher->level === 'CLASSIC' ? 'alert-secondary' : ($voucher->level === 'PREMIUM' ? 'alert-success' : 'alert-warning');
                                        $type = 'alert-info';
                                        $validite = 'Valide';
                                        $expirationdate = Carbon::parse($voucher->expirationdate);
                                        $expired = false;
                                        $statut = '';
                                        if ($voucher->active){
                                            if ($voucher->is_used){
                                                $statut = 'UTILISE';
                                            }else{
                                                $statut = 'ACTIVE';
                                            }
                                        }else{
                                            if ($expirationdate->isBefore(Carbon::now())) {
                                                $statut = 'EXPIRE';
                                            }else{
                                                $statut = 'GENERE';
                                            }
                                        }

                                        if ($expirationdate->isBefore(Carbon::now())) {
                                            $validite = 'Invalide';
                                            $expired = true;
                                        }
                                        //$client = Client::where('id', $voucher->clientid)->first();
                                        //$reward = Reward::where('id', $voucher->reward)->first();
                                        ?>
                                    <tr >
                                        <th>
                                            <h5>{{$voucher->serialnumber}}</h5>
                                        </th>
                                        <td>
                                            <h5>{{$client->name}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{$voucher->level}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{$voucher->point}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{$expirationdate->format('d-m-Y H:i:s')}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{$statut}}</h5>
                                        </td>
                                        <td>
                                            <div  >
                                                <span style="float: right;">
                                                    @if($voucher->active === true)
                                                        @if($voucher->is_used)
                                                            <span class="position-relative top-0 start-100
                                                                                 translate-middle p-2 rounded-pill
                                                                                 bg-dark border border-light
                                                                                 rounded-circle badge">
                                                                    </span>
                                                        @else
                                                            <span class="position-relative top-0 start-100
                                                                                 translate-middle p-2 rounded-pill
                                                                                 bg-success border border-light
                                                                                 rounded-circle badge">

                                                                    </span>
                                                        @endif

                                                    @else

                                                        <span class="position-relative top-0 start-100
                                                                                 translate-middle p-2 rounded-pill
                                                                                 bg-danger border border-light
                                                                                 rounded-circle badge">

                                                        </span>
                                                    @endif
                                                </span>

                                                @if(!$voucher->active && !$voucher->is_used && !$expired)
                                                    <a class="btn btn-sm btn-link" href="#" data-bs-toggle="modal"
                                                       data-bs-target="#confirm-activate-voucher-modal" style="text-decoration: none;">
                                                        <b style="color: limegreen;">{{'Activer'}}</b>
                                                    </a>
                                                    <div class="modal fade" id="confirm-activate-voucher-modal"
                                                         data-bs-backdrop="static"
                                                         data-bs-keyboard="false" tabindex="-1"
                                                         aria-labelledby="staticBackdropLabel"
                                                         aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous
                                                                        souhaitez
                                                                        activer le bon <strong
                                                                            style="color: darkred;">{{$voucher->serialnumber}}</strong>
                                                                    </h1>
                                                                    <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                </div>
                                                                <form method="POST"
                                                                      action="{{url('/client/' . $client->id . '/vouchers/' . $voucher->id . '/activate')}}"
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

                                                                        <input type="hidden" name="clientid"
                                                                               value="{{$client->id}}">

                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger"
                                                                                data-bs-dismiss="modal">Annuler
                                                                        </button>
                                                                        <button type="submit" class="btn btn-success"> Activer
                                                                            le bon
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    @if(!$voucher->is_used && !$expired)

                                                        <a class="btn btn-sm btn-link" href="#" data-bs-toggle="modal"
                                                           data-bs-target="#confirm-deactivate-voucher-modal"
                                                           style="text-decoration: none;">
                                                            <b style="color: red;">{{'Desactiver'}}</b>
                                                        </a>
                                                        <div class="modal fade" id="confirm-deactivate-voucher-modal"
                                                             data-bs-backdrop="static"
                                                             data-bs-keyboard="false" tabindex="-1"
                                                             aria-labelledby="staticBackdropLabel"
                                                             aria-hidden="true">
                                                            <div
                                                                class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                                            Vous souhaitez
                                                                            desactiver le bon <strong
                                                                                style="color: darkred;">{{$voucher->serialnumber}}</strong>
                                                                        </h1>
                                                                        <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                    </div>
                                                                    <form method="POST"
                                                                          action="{{url('/client/' . $client->id . '/vouchers/' . $voucher->id . '/deactivate')}}"
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
                                                                            <input type="hidden" name="clientid"
                                                                                   value="{{$client->id}}">
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-danger"
                                                                                    data-bs-dismiss="modal">Annuler
                                                                            </button>
                                                                            <button type="submit" class="btn btn-success">
                                                                                Desactiver le bon
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <a class="btn btn-sm btn-link" href="#" data-bs-toggle="modal"
                                                           data-bs-target="#confirm-use-voucher-modal" style="text-decoration: none;">
                                                            <b style="color: blue;">{{'Utiliser'}}</b>
                                                        </a>
                                                        <div class="modal fade" id="confirm-use-voucher-modal"
                                                             data-bs-backdrop="static"
                                                             data-bs-keyboard="false" tabindex="-1"
                                                             aria-labelledby="staticBackdropLabel"
                                                             aria-hidden="true">
                                                            <div
                                                                class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title fs-5" id="staticBackdropLabel">
                                                                            Vous souhaitez
                                                                            confirme l'utilisation du bon <strong
                                                                                style="color: darkgreen;">{{$voucher->serialnumber}}</strong>
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                    </div>
                                                                    <form method="POST"
                                                                          action="{{url('/client/' . $client->id . '/vouchers/' . $voucher->id . '/use')}}"
                                                                          onsubmit="return validateCode();">
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

                                                                            <input type="hidden" name="clientid"
                                                                                   value="{{$client->id}}">
                                                                            <h5><strong style="color: darkred;">En confirmant
                                                                                    l'utilisation du bon, le systeme ne vous
                                                                                    permet plus de revenir en arriere.
                                                                                    Rassurez-vous
                                                                                    que le client utilise ce bon.</strong></h5>


                                                                            <div class="row mb-3">
                                                                                <label for="code" class="col-md-4 col-form-label text-md-end">{{ 'Code d\'utilisation' }}
                                                                                    <b class="" style="color: red;">*</b>
                                                                                </label>

                                                                                <div class="col-md-6">
                                                                                    <input id="code" type="text" class="form-control @error('code') is-invalid @enderror"
                                                                                           name="code" value="{{ old('code') }}" required autocomplete="code" autofocus>

                                                                                    @error('code')
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-danger"
                                                                                    data-bs-dismiss="modal">Annuler
                                                                            </button>
                                                                            <button type="submit" class="btn btn-success">
                                                                                Confirmez l'utilisation du bon
                                                                            </button>
                                                                        </div>
                                                                        <script type="text/javascript">
                                                                            function validateCode() {
                                                                                var codeElem = document.getElementById('code');
                                                                                var code = codeElem.value;
                                                                                var codeArray = code.split('-');
                                                                                var codestr = '';
                                                                                if (codeArray.length > 1) {
                                                                                    codestr = codeArray[0] + codeArray[1];
                                                                                }else{
                                                                                    codestr = codeArray[0];
                                                                                }
                                                                                var lengthiHeigt = codestr.length === 8;
                                                                                if (lengthiHeigt === false) {
                                                                                    alert('Le code a exactement 8 caracteres. L\'insertion ou l\'omission du caractere "-" n\'a pas d\'effet.');
                                                                                    return false;
                                                                                }
                                                                                return true;
                                                                            }
                                                                        </script>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{--<div>{{'Rien a faire'}}</div>--}}
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                {{--<tfoot>Pied de table</tfoot>--}}
                            </table>
                        @else
                            <h5>Aucun bon trouve dans le systeme</h5>
                        @endif

                    </div>

                    {{--<div class="card-footer">
                        {{' '}}
                    </div>--}}
                </div>
            </div>
        </div>
    </div>

@endsection
