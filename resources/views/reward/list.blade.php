{{--<div class="list-group list-group-flush">--}}
@php use App\Http\Controllers\GuestController;@endphp
@if(count($rewards) > 0)
    <table class="table table-striped table-responsive table-bordered">
        <thead class="" style="color: darkred;">
            <th scope="col">
                {{ __('Nom') }}
            </th>

            <th scope="col">
               {{ __('Nature') }}
            </th>

            <th scope="col">
                {{ __('Niveau') }}
            </th>
            <th scope="col">
                {{ __('Point Requis') }}
            </th>
            @if(\Illuminate\Support\Facades\Auth::check())
                <th scope="col">
                    {{ __('Actions') }}
                </th>
            @endif
        </thead>
        <tbody>
            @foreach($rewards as $reward)
                @php $level = json_decode($reward->level,true); @endphp
                {{--<a class="list-group-item list-group-item-action btn btn-link"  href="#{{$reward->id}}" id="{{$reward->id}}">--}}

                @if($reward->active)
                    <tr>
                        <th scope="row">
                            <h5 >{{$reward->name}}</h5>
                        </th>

                        <td >
                            <h5 >{{$reward->nature}}</h5>
                        </td>

                        <td >
                            <h5 style="">{{$level['name']}}</h5>
                        </td>

                        <td >
                            <h5 style="">{{$level['point']}}</h5>
                        </td>

                            {{--@if($reward->active)--}}
                                <td >
                                    @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->is_admin)
                                    <a class="" href="#"
                                       data-bs-toggle="modal"
                                       style="text-decoration: none;"
                                       data-bs-target="#confirm-deactivate-reward-modal">
                                        <img src="{{asset('images/icons8-checkmark-25.png')}}" alt="OK"> {{ __('Désactiver') }}
                                    </a>
                                    <div class="modal fade" id="confirm-deactivate-reward-modal" data-bs-backdrop="static"
                                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                        {{ __('Confirmez la désactivation de la récompense'). ' ' . $reward->name . '?'}}
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
                                                                {{ __('Nom') }}: &nbsp; &nbsp; {{$reward->name}}
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
                                                    <a href="{{url('/'.GuestController::getApplicationLocal().'/home/rewards/' . $reward->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=deactivate'}}"
                                                       title="{{ __('Désactiver') }}" class="btn btn-success">
                                                        {{ __('Confirmer') }}
                                                    </a>
                                                </div>
                                                {{--</form>--}}
                                            </div>
                                        </div>
                                    </div>

                                        <button class="btn btn-link" style="text-decoration: none;"
                                                data-bs-toggle="modal" data-bs-target="#confirm-delete-reward">
                                            <img src="{{asset('images/icons8-remove-24.png')}}" alt="OK">
                                        </button>

                                        <div class="modal fade" id="confirm-delete-reward" data-bs-backdrop="static"
                                             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                            {{__("Confirmez la suppression de la récompense")}} </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST" action="{{route('rewards.delete', $reward->id)}}" id="delete_reward">
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
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger"
                                                                    data-bs-dismiss="modal">{{__("Annuler")}}
                                                            </button>
                                                            <button type="submit" class="btn btn-success">
                                                                {{__("Supprimer")}}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @else
                                        <span><img src="{{asset('images/icons8-checkmark-25.png')}}" alt="OK"> Active</span>
                                    @endif
                                </td>
                    </tr>
                @else
                        @if(\Illuminate\Support\Facades\Auth::check())
                            <tr>
                                <th scope="row">
                                    <h5 >{{$reward->name}}</h5>
                                </th>

                                <td >
                                    <h5 >{{$reward->nature}}</h5>
                                </td>

                                <td >
                                    <h5 style="">{{$level['name']}}</h5>
                                </td>

                                <td >
                                    <h5 style="">{{$level['point']}}</h5>
                                </td>
                                <td >
                                    @if(\Illuminate\Support\Facades\Auth::user()->is_admin)
                                        <a class="" href="#"
                                           style="text-decoration: none;"
                                           data-bs-toggle="modal"
                                           data-bs-target="#confirm-activate-reward-modal2">
                                            <img src="{{asset('images/icons8-cancel-25.png')}}" alt="KO"> {{ __('Activer') }}
                                        </a>
                                        <div class="modal fade" id="confirm-activate-reward-modal2" data-bs-backdrop="static"
                                             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                            {{ __('Confirmez l\'activation de la récompense'). ' ' . $reward->name . '?'}}
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
                                                                    {{ __('Nom') }}: &nbsp; &nbsp; {{$reward->name}}
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
                                                        <a href="{{url('/'.GuestController::getApplicationLocal().'/home/rewards/' . $reward->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=activate'}}"
                                                           title="{{ __('Activer') }}" class="btn btn-success">
                                                            {{ __('Confirmer') }}
                                                        </a>
                                                    </div>
                                                    {{--</form>--}}
                                                </div>
                                            </div>
                                        </div>

                                        <button class="btn btn-link" style="text-decoration: none;"
                                                data-bs-toggle="modal" data-bs-target="#confirm-delete-reward">
                                            <img src="{{asset('images/icons8-remove-24.png')}}" alt="OK">
                                        </button>

                                        <div class="modal fade" id="confirm-delete-reward" data-bs-backdrop="static"
                                             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                            {{__("Confirmez la suppression de la récompense")}}</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST" action="{{route('rewards.delete', $reward->id)}}" id="delete_reward">
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
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger"
                                                                    data-bs-dismiss="modal">{{__("Annuler")}}
                                                            </button>
                                                            <button type="submit" class="btn btn-success">
                                                                {{__("Supprimer")}}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @else
                                        <span><img src="{{asset('images/icons8-cancel-25.png')}}" alt="KO"> {{ __('Désactiver') }}</span>

                                        {{--@endif--}}
                                    @endif
                                </td>

                            </tr>
                        @endif


                @endif

            @endforeach
        </tbody>
    </table>
@else
    <div></div>
@endif

{{--</div>--}}



