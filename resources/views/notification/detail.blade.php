
@extends('layouts.email-template')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">

                        <span class="badge bg-light position-absolute top|start-*"
                              style="position: relative; right: 0; margin-right: 8px; margin-top: -10px;">
                            @if(!$notification->read)
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
                        <h3>
                            {{ 'Disponibilite de recompenses au travers des bons' }}
                        </h3><br>

                        <h5>{{$data['msg'][0]}}</h5><br>
                        @php $i = 0; @endphp
                        <ul>
                            @foreach($data['msg'] as $msg)
                                @if($i > 0)
                                    <li>{{$msg}}</li>
                                @endif
                                @php $i = $i + 1; @endphp
                            @endforeach
                        </ul>

                        <p>
                            Pour le faire vous devez vous connecter en cliquant sur le lien:
                            <a href="{{$data['clientLoginUrl']}}" class="btn btn-link"
                            onclick="return false;">{{'Cliquez ici pour vous connecter.'}}</a>
                        </p>

                        <h5>Merci pour votre fidelite.</h5>
                        @if(!$notification->read)
                            <form method="POST" action="{{ route('notifications.index.read-or-unread', $notification)}}">
                                @csrf
                                <input type="hidden" name="action" value="read">
                                <button class="btn btn-link" type="submit" style="float: right;">Marquer comme lu</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('notifications.index.read-or-unread', $notification)}}">
                                @csrf
                                <input type="hidden" name="action" value="unread">
                                <button class="btn btn-link" type="submit" style="float: right;">Marquer comme non lu</button>
                            </form>
                        @endif
                        <button
                            class="btn btn-link"
                            type="submit"
                            style="float: left; "
                            onclick="history.back();"
                        >Retour</button>


                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
