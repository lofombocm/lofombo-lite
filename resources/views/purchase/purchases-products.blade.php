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
                    <div class="card-header"><h5>{{ 'Produits du systeme'}}</h5></div>

                    <div class="card-body">
                        {{--<div class="modal-body" style="height: 80vh; overflow-y: auto;">--}}
                            @php
                                $products = \App\Models\Product::all();
                                $i = 1;
                            @endphp
                            @if(count($products) > 0)
                                <table class="table table-striped table-responsive table-bordered">
                                    <thead class="" style="color: darkred;">
                                    <th scope="col">
                                        {{ '#' }}
                                    </th>

                                    <th scope="col">
                                        {{ 'Nom du Produits' }}
                                    </th>

                                    <th scope="col">
                                        {{ 'Prix Unitaire' }}
                                    </th>
                                    <th scope="col">
                                        {{ 'Enreg. Le' }}
                                    </th>
                                    </thead>
                                    <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <th scope="row">
                                                {{$i}}
                                            </th>
                                            <td >
                                                {{$product->name}}
                                            </td>

                                            <td >
                                                {{$product->price}}
                                            </td>

                                            <td >
                                                {{Carbon::parse($product->creatd_at)->format('d-m-Y')}}
                                            </td>
                                        </tr>
                                        @php $i = $i + 1; @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        {{--</div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
