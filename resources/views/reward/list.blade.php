{{--<div class="list-group list-group-flush">--}}
@if(count($rewards) > 0)
    <table class="table table-striped table-responsive table-bordered">
        <thead class="" style="color: darkred;">
            <th scope="col">
                {{ 'Nom' }}
            </th>

            <th scope="col">
               {{ 'Nature' }}
            </th>

            <th scope="col">
                {{ 'Niveau' }}
            </th>
            <th scope="col">
                {{ 'Point Requis' }}
            </th>
            @if(\Illuminate\Support\Facades\Auth::check())
                <th scope="col">
                    {{ 'Actions' }}
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
                            <h5 style="color: darkgreen;">{{$level['name']}}</h5>
                        </td>

                        <td >
                            <h5 style="color: darkgreen;">{{$level['point']}}</h5>
                        </td>
                        @if(\Illuminate\Support\Facades\Auth::check())
                            {{--@if($reward->active)--}}
                                <td >
                                    <a class="" href="#"
                                       data-bs-toggle="modal"
                                       style="text-decoration: none;"
                                       data-bs-target="#confirm-deactivate-reward-modal">
                                        <img src="{{asset('images/icons8-checkmark-25.png')}}" alt="OK"> Desactiver
                                    </a>
                                    <div class="modal fade" id="confirm-deactivate-reward-modal" data-bs-backdrop="static"
                                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                        {{'Confirmez-vous la desactivation de la recompense ' . $reward->name . '?'}}
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
                                                                Nom: &nbsp; &nbsp; {{$reward->name}}
                                                            </h5>
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action"
                                                           style="margin-left: 15px; width: 98%;" id="telephone-displayer">
                                                            <h5>
                                                                Niveau: &nbsp; &nbsp; {{$level['name']}}
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
                                                    <a href="{{url('/home/rewards/' . $reward->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=deactivate'}}"
                                                       title="Desactiver" class="btn btn-success">
                                                        {{'Confirmer'}}
                                                    </a>
                                                </div>
                                                {{--</form>--}}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            {{--@else
                                <td >
                                    <a class="" href="#"
                                       data-bs-toggle="modal"
                                       data-bs-target="#confirm-activate-reward-modal">
                                        <img src="{{asset('images/icons8-cancel-25.png')}}" alt="KO">
                                    </a>
                                    <div class="modal fade" id="confirm-activate-reward-modal" data-bs-backdrop="static"
                                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                        {{'Confirmez-vous l\'activation de la recompense ' . $reward->name . '?'}}
                                                        --}}{{--<strong
                                                            style="color: darkred;">{{$client->name}}</strong>--}}{{--
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                --}}{{-- <form method="POST" action="{{url('/client/' . $client->id . '/activate')}}"
                                                       onsubmit="return true;">--}}{{--
                                                <div class="modal-body">
                                                    <div class="list-group list-group-flush alert alert-info"
                                                         id="form-list-group">
                                                        <a href="#" class="list-group-item list-group-item-action"
                                                           style="margin-left: 15px; width: 98%;" id="name-displayer">
                                                            <h5>
                                                                Nom: &nbsp; &nbsp; {{$reward->name}}
                                                            </h5>
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action"
                                                           style="margin-left: 15px; width: 98%;" id="telephone-displayer">
                                                            <h5>
                                                                Niveau: &nbsp; &nbsp; {{$level['name']}}
                                                            </h5>
                                                        </a>
                                                        <a href="#" class="list-group-item list-group-item-action"
                                                           style="margin-left: 15px; width: 98%;" id="amount-displayer">
                                                            <h5 style="color: darkgreen;">Points: &nbsp; &nbsp; {{$level['point']}}</h5>
                                                        </a>
                                                        --}}{{--<a href="#" class="list-group-item list-group-item-action"
                                                           style="margin-left: 15px; width: 98%;" id="receiptnumber-displayer">

                                                        </a>--}}{{--
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Annuler
                                                    </button>
                                                    <a href="{{url('/home/rewards/' . $reward->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=activate'}}"
                                                       title="Activer" class="btn btn-success">
                                                        {{'Confirmer'}}
                                                    </a>
                                                </div>
                                                --}}{{--</form>--}}{{--
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endif--}}
                        @endif
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
                                <h5 style="color: darkgreen;">{{$level['name']}}</h5>
                            </td>

                            <td >
                                <h5 style="color: darkgreen;">{{$level['point']}}</h5>
                            </td>
                            @if(\Illuminate\Support\Facades\Auth::check())
                                {{--@if($reward->active)
                                    <td >

                                        <a class="btn btn-primary" href="#"
                                           data-bs-toggle="modal"
                                           data-bs-target="#confirm-deactivate-reward-modal2">
                                            <img src="{{asset('images/icons8-checkmark-25.png')}}" alt="OK">
                                        </a>
                                        <div class="modal fade" id="confirm-deactivate-reward-modal2" data-bs-backdrop="static"
                                             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                            {{'Confirmez-vous la desactivation de la recompense ' . $reward->name . '?'}}
                                                            --}}{{--<strong
                                                                style="color: darkred;">{{$client->name}}</strong>--}}{{--
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    --}}{{-- <form method="POST" action="{{url('/client/' . $client->id . '/activate')}}"
                                                           onsubmit="return true;">--}}{{--
                                                    <div class="modal-body">
                                                        <div class="list-group list-group-flush alert alert-info"
                                                             id="form-list-group">
                                                            <a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="name-displayer">
                                                                <h5>
                                                                    Nom: &nbsp; &nbsp; {{$reward->name}}
                                                                </h5>
                                                            </a>
                                                            <a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="telephone-displayer">
                                                                <h5>
                                                                    Niveau: &nbsp; &nbsp; {{$level['name']}}
                                                                </h5>
                                                            </a>
                                                            <a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="amount-displayer">
                                                                <h5 style="color: darkgreen;">Points: &nbsp; &nbsp; {{$level['point']}}</h5>
                                                            </a>
                                                            --}}{{--<a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="receiptnumber-displayer">

                                                            </a>--}}{{--
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                                data-bs-dismiss="modal">Annuler
                                                        </button>
                                                        <a href="{{url('/home/rewards/' . $reward->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=deactivate'}}"
                                                           title="Desactiver" class="btn btn-success">
                                                            {{'Confirmer'}}
                                                        </a>
                                                    </div>
                                                    --}}{{--</form>--}}{{--
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                @else--}}
                                    <td >

                                        <a class="" href="#"
                                           style="text-decoration: none;"
                                           data-bs-toggle="modal"
                                           data-bs-target="#confirm-activate-reward-modal2">
                                            <img src="{{asset('images/icons8-cancel-25.png')}}" alt="KO"> {{'Activer'}}
                                        </a>
                                        <div class="modal fade" id="confirm-activate-reward-modal2" data-bs-backdrop="static"
                                             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                            {{'Confirmez-vous l\'activation de la recompense ' . $reward->name . '?'}}
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
                                                                    Nom: &nbsp; &nbsp; {{$reward->name}}
                                                                </h5>
                                                            </a>
                                                            <a href="#" class="list-group-item list-group-item-action"
                                                               style="margin-left: 15px; width: 98%;" id="telephone-displayer">
                                                                <h5>
                                                                    Niveau: &nbsp; &nbsp; {{$level['name']}}
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
                                                        <a href="{{url('/home/rewards/' . $reward->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=activate'}}"
                                                           title="Activer" class="btn btn-success">
                                                            {{'Confirmer'}}
                                                        </a>
                                                    </div>
                                                    {{--</form>--}}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                {{--@endif--}}
                            @endif
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



