{{--<div class="list-group list-group-flush">--}}
{{-- https://freefrontend.com/css-cards/#google_vignette--}}
<link rel="stylesheet" href="{{asset('css/card.css')}}">
@php use App\Http\Controllers\GuestController;
    //$rewards = \App\Models\Reward::all();
$rewards =\App\Models\Reward::where('active', true)->get();
@endphp
@if(count($rewards) > 0)
    <h1 style="font-size: xx-large;">
        @if(count($rewards) > 0) {{__('Nos Récompenses')}}@endif
    </h1>
    <br>
            <?php
            $totalLength = count($rewards);
            $j = 0;
            ?>
        @while($j < $totalLength)
            @php $level = json_decode($rewards[$j]->level,true); @endphp
            <ul class="product-plans row">
                @php $i = 0; @endphp
                @while($i < 3 && $j + $i < $totalLength)
                    <li class="product-plan col-md-3">
                        <div class="title"><h5>{{$rewards[$j + $i]->name}}</h5></div>
                        <div class="price"><strong>Point: {{$level['point']}}</strong></div>
                        <ul class="features">
                            <li class="check">
                                {{__("Nature")}}:
                                @if($rewards[$j + $i]->nature == 'FINANCIAL')
                                    {{__("Financière")}}
                                @else
                                    @if($rewards[$j + $i]->nature == 'MATERIAL')
                                        {{__("Matériel")}}
                                    @else
                                        {{__("Service")}}
                                    @endif
                                @endif
                            </li>
                            <li class="{{$rewards[$j + $i]->active ? 'check' : 'cross'}}" style="border: 0 red solid; margin-bottom: -40px;">
                                @if($rewards[$j + $i]->active)
                                    {{__("Etat")}}: {{__("Activé")}}
                                    <img src="{{asset('images/icons8-checkmark-25.png')}}" alt=""
                                         style="position: relative; top: -40px; right: -140px;">
                                @else
                                    {{__("Etat")}}: {{__("Désactivé")}} &nbsp;&nbsp;&nbsp;
                                    <img src="{{asset('images/icons8-cancel-25.png')}}" alt=""
                                         style="position: relative; top: -40px; right: -140px;">
                                @endif
                            </li>

                            @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->is_admin)
                                <li class="{{$rewards[$j + $i]->active ? 'check' : 'cross'}}">
                                    @if($rewards[$j + $i]->active)
                                        <a class="" href="#"
                                           data-bs-toggle="modal"
                                           style="text-decoration: none;"
                                           data-bs-target="#confirm-deactivate-reward-modal">
                                            {{--<img src="{{asset('images/icons8-checkmark-25.png')}}" alt="OK">--}}
                                            {{ __('Désactiver') }}
                                        </a>

                                        <div class="modal fade" id="confirm-deactivate-reward-modal" data-bs-backdrop="static"
                                             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                            {{ __('Confirmez la désactivation de la récompense'). ' ' . $rewards[$j + $i]->name . '.'}}
                                                            {{--<strong
                                                                style="color: darkred;">{{$client->name}}</strong>--}}
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    {{-- <form method="POST" action="{{url('/client/' . $client->id . '/activate')}}"
                                                           onsubmit="return true;">--}}
                                                    <div class="modal-body">
                                                        <div class="list-group list-group-flush alert alert-info"
                                                             id="form-list-group">
                                                            <a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="name-displayer">
                                                                <h5>
                                                                    {{ __('Nom') }}: &nbsp; &nbsp; {{$rewards[$j + $i]->name}}
                                                                </h5>
                                                            </a>
                                                            <a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="telephone-displayer">
                                                                <h5>
                                                                    {{ __('Niveau')}}: &nbsp; &nbsp; {{$level['name']}}
                                                                </h5>
                                                            </a>
                                                            <a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="amount-displayer">
                                                                <h5 style="color: darkgreen;">Points: &nbsp; &nbsp; {{$level['point']}}</h5>
                                                            </a>
                                                            {{--<a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="receiptnumber-displayer">

                                                            </a>--}}
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                                data-bs-dismiss="modal">Annuler
                                                        </button>
                                                        <a href="{{url('/'.GuestController::getApplicationLocal().'/home/rewards/' . $rewards[$j + $i]->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=deactivate'}}"
                                                           title="{{ __('Désactiver') }}" class="btn btn-success">
                                                            {{ __('Confirmer') }}
                                                        </a>
                                                    </div>
                                                    {{--</form>--}}
                                                </div>
                                            </div>
                                        </div>

                                    @else
                                        <a class="" href="#"
                                           style="text-decoration: none;"
                                           data-bs-toggle="modal"
                                           data-bs-target="#confirm-activate-reward-modal2">
                                            {{--<img src="{{asset('images/icons8-cancel-25.png')}}" alt="KO">--}}
                                            {{ __('Activer') }}
                                        </a>
                                        <div class="modal fade" id="confirm-activate-reward-modal2" data-bs-backdrop="static"
                                             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                            {{ __('Confirmez l\'activation de la récompense'). ' ' . $rewards[$j + $i]->name . '.'}}
                                                            {{--<strong
                                                                style="color: darkred;">{{$client->name}}</strong>--}}
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    {{-- <form method="POST" action="{{url('/client/' . $client->id . '/activate')}}"
                                                           onsubmit="return true;">--}}
                                                    <div class="modal-body">
                                                        <div class="list-group list-group-flush alert alert-info"
                                                             id="form-list-group">
                                                            <a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="name-displayer">
                                                                <h5>
                                                                    {{ __('Nom') }}: &nbsp; &nbsp; {{$rewards[$j + $i]->name}}
                                                                </h5>
                                                            </a>
                                                            <a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="telephone-displayer">
                                                                <h5>
                                                                    {{ __('Niveau') }}: &nbsp; &nbsp; {{$level['name']}}
                                                                </h5>
                                                            </a>
                                                            <a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="amount-displayer">
                                                                <h5 style="color: darkgreen;">{{ __('Points') }}: &nbsp; &nbsp; {{$level['point']}}</h5>
                                                            </a>
                                                            {{--<a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="receiptnumber-displayer">

                                                            </a>--}}
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                                data-bs-dismiss="modal">Annuler
                                                        </button>
                                                        <a href="{{url('/'.GuestController::getApplicationLocal().'/home/rewards/' . $rewards[$j + $i]->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=activate'}}"
                                                           title="{{ __('Activer') }}" class="btn btn-success">
                                                            {{ __('Confirmer') }}
                                                        </a>
                                                    </div>
                                                    {{--</form>--}}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endif

                            {{--<li class="cross">Description Space</li>
                            <li class="cross">Sample Text Here</li>--}}
                        </ul>
                        <button class="btn"><strong>{{$level['name']}}</strong></button>
                    </li>
                            {{--<li class="product-plan">
                                <div class="title">Standard</div>
                                <div class="price">$5.99</div>
                                <ul class="features">
                                    <li class="check">Sample Text Here</li>
                                    <li class="check">Other Text Title</li>
                                    <li class="check">Text Space Goes Here</li>
                                    <li class="cross">Description Space</li>
                                    <li class="cross">Sample Text Here</li>
                                </ul>
                                <button class="btn">Select</button>
                            </li>
                            <li class="product-plan">
                                <div class="title">Premium</div>
                                <div class="price">$9.99</div>
                                <ul class="features">
                                    <li class="check">Sample Text Here</li>
                                    <li class="check">Other Text Title</li>
                                    <li class="check">Text Space Goes Here</li>
                                    <li class="check">Description Space</li>
                                    <li class="check">Sample Text Here</li>
                                </ul>
                                <button class="btn">Select</button>
                            </li>--}}
                        {{--</ul>--}}
                        {{--<div class="credits">
                            <a target="_blank" href="https://www.freepik.com/free-vector/web-pricing-comparison-boxes-table-template_18786190.htm">inspired by</a>
                        </div>--}}
                        {{--<div class="card" style="border-left: 2px black solid; background: white">
                            <div class="card-body" style="border: 0 black solid;">
                                <div class="card-title">
                                                        <span class="badge bg-dark position-absolute top|start-*"
                                                              style="position: relative; left: 0; font-size: 0.85em; margin-top: -18px;">
                                                            <strong>{{$j + 1 + $i}}</strong>
                                                        </span>
                                    &nbsp;&nbsp;&nbsp;&nbsp;--}}{{--{{ __("Type") }}: --}}{{--
                                    <div style="width: 100%; text-align: center; border-bottom: 2px black solid; margin-top: -30px; padding-bottom: 5px;">
                                        <strong>{{$levels[$j + $i]->name}}</strong>
                                    </div>
                                    <div style="margin-top: 10px;">{{ __("Points Requis") }}: <strong> {{$levels[$j + $i]->point}}</strong></div>
                                </div>
                            </div>
                        </div>--}}
                    {{--</div>--}}
                    @php $i = $i + 1; @endphp
                @endwhile
                    <?php $j = $j + $i; ?>
                <div class="row" style="border: 0 black solid; margin-top: 30px;"></div>
            </ul>
        @endwhile

@else
    <div></div>
@endif

{{--</div>--}}



