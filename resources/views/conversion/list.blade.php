@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ 'Choisir une conversion' }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('conversions.set-conversion.post') }}">
                            @csrf
                            <input type="hidden" name="conversionid" id="conversionid">

                            <div class="list-group" id="conversions">
                                <?php
                                    $i = 1;
                                    ?>
                                @foreach(\App\Models\Conversion::orderBy('updated_at', 'desc')->get() as $conversion)
                                    <a href="#" class="list-group-item list-group-item-action {{($conversion->is_applicable ? 'active' : '')}}"
                                        {{$conversion->is_applicable ? 'aria-current="true"' : ''}} onclick="selectItem(this.id);" id="{{$conversion->id}}">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                {{$i}} - &nbsp;
                                                <small>Un montant de {{$conversion->amount_to_point_amount . ' ' . env('CURRENCY_NAME')}} donne droit a {{$conversion->amount_to_point_point}} points</small>
                                            </h5>

                                        </div>
                                        <p class="mb-1">{{$conversion->point_to_amount_point}} points donne droit a {{$conversion->point_to_amount_amount . ' ' . env('CURRENCY_NAME')}}</p>
                                        <small>Coeficient de bonification pour anniversaire: {{$conversion->birthdate_rate}}.</small>
                                    </a>
                                        <?php
                                        $i = $i + 1;
                                        ?>
                                @endforeach

                                <br />
                                <script type="text/javascript">
                                    function selectItem(conversionid){
                                        var conversionidinput = document.getElementById('conversionid');
                                        conversionidinput.setAttribute('value', conversionid);
                                        var conversions = document.getElementById('conversions');
                                        var links = conversions.getElementsByTagName('a');
                                        for (var i = 0; i < links.length; i++) {
                                            links[i].classList.remove('active');
                                            links[i].removeAttribute('aria-current');
                                        }
                                        var selectedElement = document.getElementById(conversionid);
                                        selectedElement.classList.add('active');
                                        selectedElement.setAttribute('aria-current', 'true');
                                        selectedElement.setAttribute('href', '#' + conversionid);
                                        return false;
                                    }
                                </script>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-5">
                                    <button type="submit" class="btn btn-primary">
                                        {{ 'Enregistrer' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        {{'Footer'}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{--
--}}
