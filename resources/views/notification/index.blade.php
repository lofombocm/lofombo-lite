@php
    use App\Models\Config;
    use App\Models\Notification;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Auth;
@endphp
@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('layouts.menu')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5>{{ 'Notifications'}}</h5></div>

                    <div class="card-body">
                        {{--<div class="modal-body" style="height: 80vh; overflow-y: auto;">--}}

                        <input type="hidden" name="error" id="error"
                               class="form-control @error('error') is-invalid @enderror">
                        @error('error')
                        <span class="invalid-feedback" role="alert"
                              style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                        @enderror

                        @csrf

                        {{--<div class="card">--}}
                            {{--<div class="card-body">--}}
                                @if(count($notifications) > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($notifications as $notification)
                                            <adiv class="list-group-item list-group-item-action">
                                                <h5>
                                                    Objet: <strong>{{$notification->subject}}</strong>
                                                    &nbsp; &nbsp;
                                                    <span
                                                        class="badge bg-primary position-absolute top|start-*"
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
                                    </div>
                                @else
                                    <div>Pas de notifications</div>
                                @endif
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--</div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
