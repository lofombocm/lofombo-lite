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
                    <div class="card-header">
                        <h5 style="display: inline;">{{ __("Article de la plateforme") }}</h5>
                        <h5 style="display: inline; float: right;">
                            @if(count(Config::where('is_applicable', true)->get()) > 0)
                                <a href="{{ route('home.products.index')}}"
                                   style="text-decoration: none; font-size: x-large; color: green;"
                                   title="{{__('Ajouter un client')}}">
                                    <strong><span class="glyphicon glyphicon-plus">+</span></strong>
                                    <span style="font-size: initial;">{{ __('Ajouter') }}</span>
                                </a>
                            @endif
                        </h5>
                    </div>

                    <div class="card-body">
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
                                        {{ __("Nom Article") }}
                                    </th>

                                    <th scope="col">
                                        {{ __("Prix Unitaire (TTC)") }}
                                    </th>
                                    <th scope="col">
                                        {{ __("Enregistré le") }}
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
