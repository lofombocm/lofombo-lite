{{--<div class="list-group list-group-flush">--}}
@if(count($rewards) > 0)
    <table class="table table-striped">
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
                            @if($reward->active)
                                <td >
                                    <a href="{{url('/home/rewards/' . $reward->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=deactivate'}}" title="Desactiver">
                                        <span class="glyphicon glyphicon-pencil" style="text-orientation: sideways;">&#x270f;</span>
                                    </a>
                                </td>
                            @else
                                <td >
                                    <a href="{{url('/home/rewards/' . $reward->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=activate'}}" title="Activer">
                                        <span class="glyphicon glyphicon-pencil" style="text-orientation: sideways;">&#x270f;</span>

                                    </a>
                                </td>
                            @endif
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
                                @if($reward->active)
                                    <td >
                                        <a href="{{url('/home/rewards/' . $reward->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=deactivate'}}" title="Desactiver">
                                            <span class="glyphicon glyphicon-pencil" style="text-orientation: sideways;">&#x270f;</span>
                                        </a>
                                    </td>
                                @else
                                    <td >
                                        <a href="{{url('/home/rewards/' . $reward->id . '/activate-deactivate').'?user=' . \Illuminate\Support\Facades\Auth::user()->id.'&action=activate'}}" title="Activer">
                                            <span class="glyphicon glyphicon-pencil" style="text-orientation: sideways;">&#x270f;</span>

                                        </a>
                                    </td>
                                @endif
                            @endif
                        </tr>
                    @endif
                @endif

            @endforeach
        </tbody>
    </table>
@endif

{{--</div>--}}



