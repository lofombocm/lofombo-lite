@php
    use App\Models\Config;
    use App\Models\Loyaltyaccount;
    use App\Models\Notification;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Client;
    use App\Http\Controllers\GuestController;

    //$notifications = Notification::where('sender_address', Auth::user()->email)->where('read', false)->get();
    //$unreadMsgNum = count($notifications);
@endphp
@extends('layouts.app-super_admin')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('layouts.super-admin-menu')
            <div class="col-md-9">
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row justify-content-center">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert" style="text-align: center;">
                                <h5>{{ session('status') }}</h5>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 style="display: inline; float: left;">{{ 'Licences' }} </h4>
                                    <h5 style="display: inline; float: right;">
                                        <a href="{{ route('home-super-admin.license.form.index')}}"
                                           style="text-decoration: none; font-size: large; color: green;"
                                           id="add_level_field"
                                           title="{{ __('Ajouter une licence')}}">
                                            <strong><span class="glyphicon glyphicon-plus">+</span></strong>
                                            <span style="font-size: initial;">{{ __('Ajouter') }}</span>
                                        </a>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(count($licenses) > 0)
                                        <table class="table table-striped table-responsive table-bordered">
                                            <thead class="" style="color: darkred;">
                                            <th scope="col">
                                                {{ '#' }}
                                            </th>
                                            <th scope="col">
                                                {{ 'Cle' }}
                                            </th>
                                            <th scope="col">
                                                {{ 'Assigne a' }}
                                            </th>
                                            <th scope="col">
                                                {{ 'Etat' }}
                                            </th>
                                            <th scope="col">
                                                {{ 'expiration' }}
                                            </th>
                                            {{--<th scope="col">
                                                {{ 'Metas donnees' }}
                                            </th>--}}
                                            <th scope="col">
                                                {{ 'Details' }}
                                            </th>
                                            </thead>
                                            <tbody>
                                            <?php
                                                $index = 1;
                                            ?>
                                            @foreach($licenses as $license)

                                                <tr>
                                                    <th>
                                                        <h5>{{$index}}</h5>
                                                    </th>
                                                    <th>
                                                        <h5>{{$license->license_key}}</h5>
                                                    </th>
                                                    <td>
                                                        <h5>{{$license->assigned_to}}</h5>
                                                    </td>
                                                    <td>
                                                        <h5>{{$license->active ? 'VALIDE' : 'NON VALIDE'}}</h5>
                                                    </td>
                                                    <td>
                                                        <h5>{{\Illuminate\Support\Carbon::parse($license->expires_at)->format('d-m-Y H:i:s')}}</h5>
                                                    </td>
                                                    {{--<td><h5>{{json_encode($license->metadata)}}</h5></td>--}}
                                                    <td>
                                                        <a href="{{url('/'.GuestController::getApplicationLocal().'/home-super-admin/licences/' . $license->id)}}"
                                                           class="list-group-item list-group-item-action">
                                                            <img src="{{asset('images/icons8-right-chevron-25.png')}}" alt=">"/>
                                                        </a>

                                                    </td>
                                                </tr>
                                                <?php
                                                    $index = $index + 1;
                                                ?>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
