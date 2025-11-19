@php
    use App\Models\Config;
    use Illuminate\Support\Carbon;
@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('layouts.menu')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5>{{ 'Configurer des parametres du systeme'}}</h5></div>

                    <div class="card-body">
                        <form method="POST" action="{{route('configs.post')}}"
                              enctype="multipart/form-data" onsubmit="return checkForm();">
                            {{--<div class="modal-body" style="height: 80vh; overflow-y: auto;">--}}

                            {{--<input type="hidden" name="error" id="error"
                                   class="form-control @error('error') is-invalid @enderror">
                            @error('error')
                            <span class="invalid-feedback" role="alert"
                                  style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                            @enderror--}}

                            @csrf

                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger" role="alert">
                                    {{ session('error') }}
                                </div>
                            @else
                               {{-- <div class="alert alert-success" role="alert">
                                    {{ 'No error' }}
                                </div>--}}
                            @endif

                            @php
                                $initial_loyalty_points = 0;
                                $amount_per_point = 5000;
                                $currency_name = 'FCFA';
                                $levels = json_decode('[]', true);
                                $index = 0;
                                $birthdate_bonus_rate = 1;
                                /*$classic_threshold = 50;
                                $premium_threshold = 80;
                                $gold_threshold = 120;*/
                                $voucher_duration_in_month = 3;
                                $password_recovery_request_duration = 60;
                                $enterprise_name = "LOFOMBO";
                                $enterprise_email = 'contact@gmail.com';
                                $enterprise_phone = '0123456789';
                                $enterprise_website = url('/');
                                $enterprise_address = '';
                                $enterprise_logo = asset('images/logo');
                                if (count(Config::where('is_applicable', true)->get()) === 1){
                                    $config = Config::where('is_applicable', true)->first();
                                    $levels = json_decode($config->levels, true);
                                    $birthdate_bonus_rate = $config->birthdate_bonus_rate;
                                    $initial_loyalty_points = $config->initial_loyalty_points;
                                    $amount_per_point = $config->amount_per_point;
                                    $currency_name = $config->currency_name;
                                    $classic_threshold = $config->classic_threshold;
                                    $premium_threshold = $config->premium_threshold;
                                    $gold_threshold = $config->gold_threshold;
                                    $voucher_duration_in_month = $config->voucher_duration_in_month;
                                    $password_recovery_request_duration = $config->password_recovery_request_duration;
                                    $enterprise_name = $config->enterprise_name;
                                    $enterprise_email = $config->enterprise_email;
                                    $enterprise_phone = $config->enterprise_phone;
                                    $enterprise_website = $config->enterprise_website;
                                    $enterprise_address = $config->enterprise_address;
                                    $enterprise_logo = $config->enterprise_logo;
                                }
                                //dd($password_recovery_request_duration);
                            @endphp

                            <div class="row mb-3">
                                <label for="initial_loyalty_points" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Nombre de Point initial Pour les clients'}}</label>
                                <div class="col-md-7">
                                    <input id="initial_loyalty_points" type="number"
                                           class="form-control @error('initial_loyalty_points') is-invalid @enderror"
                                           name="initial_loyalty_points"
                                           value="{{$initial_loyalty_points}}"
                                           required
                                           autocomplete="initial_loyalty_points"
                                           autofocus>
                                    @error('initial_loyalty_points')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="amount_per_point" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Montant donnant droit a 1 point'}}
                                </label>
                                <div class="col-md-7">
                                    <input id="amount_per_point" type="number"
                                           class="form-control @error('amount_per_point') is-invalid @enderror"
                                           name="amount_per_point"
                                           value="{{$amount_per_point}}"
                                           required
                                           autocomplete="amount_per_point"
                                           autofocus>
                                    @error('amount_per_point')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="currency_name" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Monnaie utilisee'}}</label>
                                <div class="col-md-7">
                                    <input id="currency_name" type="text"
                                           class="form-control @error('currency_name') is-invalid @enderror"
                                           name="currency_name"
                                           value="{{$currency_name}}"
                                           required
                                           autocomplete="currency_name"
                                           autofocus>
                                    @error('currency_name')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="birthdate_bonus_rate" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Coefficient pour anniversaire'}}
                                </label>
                                <div class="col-md-7">
                                    <input id="birthdate_bonus_rate" type="number" step="0.01"
                                           class="form-control @error('birthdate_bonus_rate') is-invalid @enderror"
                                           name="birthdate_bonus_rate"
                                           value="{{$birthdate_bonus_rate}}"
                                           required
                                           autocomplete="birthdate_bonus_rate"
                                           autofocus>
                                    @error('birthdate_bonus_rate')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="row mb-3">
                                <label for="levels" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Niveau des Bons' }}
                                    <br>
                                    <a href="#" onclick="addLevelFilds();"
                                       style="text-decoration: none; font-size: x-large; color: green;"
                                       id="add_level_field">
                                        <strong><span class="glyphicon glyphicon-plus">+</span></strong>
                                    </a>
                                </label>
                                <div class="col-md-7" id="levels">
                                    @foreach($levels as $level)
                                        <div class="row" id="{{$index}}">
                                            <div class="col-md-6">
                                                <label for="level_name{{$index}}">Nom</label>

                                                <input id="level_name{{$index}}" type="text"
                                                       class="form-control @error('level_name'.$index) is-invalid @enderror"
                                                       name="level_name{{$index}}"
                                                       value="{{$level['name']}}"
                                                       required
                                                       autocomplete="level_name{{$index}}"
                                                       autofocus>
                                                @error('level_name'.$index)
                                                <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label for="level_point{{$index}}">Point</label>

                                                <input id="level_point{{$index}}" type="number"
                                                       class="form-control @error('level_point'.$index) is-invalid @enderror"
                                                       name="level_point{{$index}}"
                                                       value="{{$level['point']}}"
                                                       required
                                                       autocomplete="level_point{{$index}}"
                                                       autofocus>
                                                @error('level_point'.$index)
                                                <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <div class="col-md-2"><br>
                                                    <button class="btn btn-link" type="button" name="{{$index}}"
                                                            onclick="removeLevelLine(this.name);"
                                                            style="text-decoration: none; font-size: x-large; color: red; margin-left: -10px;">
                                                        <strong><span class="glyphicon glyphicon-plus">-</span></strong>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                        @php $index = $index + 1; @endphp
                                    @endforeach
                                    {{--<input id="birthdate" type="date" class="form-control @error('birthdate') is-invalid @enderror" name="birthdate"  autocomplete="birthdate">--}}
                                    {{--@error('levels')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror--}}
                                </div>
                                <input type="hidden" value="{{$index}}" id="index" name="index">


                                <script type="text/javascript">

                                    function addLevelFilds() {
                                        var index = parseInt(document.getElementById('index').value);
                                        console.log("Num level: " + index);
                                        //var index = numItem
                                        var levels = document.getElementById('levels');
                                        var levelrow = document.createElement("div");
                                        //divtest.setAttribute("class", "row");
                                        //var rdiv = 'removeclass'+room;
                                        var rowid = "level" + index;


                                        levelrow.innerHTML =
                                            '<div class="row" id="' + rowid + '" style="margin-bottom: 7px;">' +
                                            '<div class="col-md-6">' +
                                            '<label for="level_name' + index + '" >Nom</label>' +
                                            '<input id="level_name' + index + '" type="text" ' +
                                            'class="form-control" ' +
                                            'name="level_name' + index + '" ' +
                                            'value="" required autocomplete="level_name' + index + '" autofocus>' +
                                            '</div>' +
                                            '<div class="col-md-4">' +
                                            '<label for="level_point' + index + '" >Point</label>' +
                                            '<input id="level_point' + index + '" type="number" ' +
                                            'class="form-control" ' +
                                            'name="level_point' + index + '" ' +
                                            'value="" required autocomplete="level_point' + index + '" autofocus>' +
                                            //'<button class="btn btn-link" type="button"  name="' + rowid + '" onclick="removeLevelLine(this.name);"> <span class="glyphicon glyphicon-plus" style="font-weight: bold; color: darkred;">-</span> </button>' +
                                            '</div>' +
                                            '<div class="col-md-2"> <br>' +
                                            '<button class="btn btn-link" type="button"  name="' + rowid + '" onclick="removeLevelLine(this.name);" style="text-decoration: none; font-size: x-large; color: red; margin-left: -10px;"> <strong><span class="glyphicon glyphicon-plus">-</span></strong></button>' +
                                            '</div>'
                                        '</div>';


                                        levels.appendChild(levelrow)

                                        var newIndex = index + 1;
                                        console.log("new index: " + newIndex);
                                        document.getElementById('index').setAttribute("value", "" + newIndex);
                                    }

                                    function removeLevelLine(level) {
                                        console.log(level);
                                        //product
                                        var indexStr = level.substring("level".length);
                                        console.log(indexStr);
                                        var index = parseInt(indexStr);
                                        //index = index - 1;
                                        var numItem = parseInt(document.getElementById('index').value);
                                        if (!(numItem - 1 === index)) {
                                            index = numItem - 1;
                                        }

                                        console.log(index);
                                        document.getElementById(level).remove();

                                        document.getElementById('index').setAttribute("value", "" + index);
                                        console.log("new num item: " + document.getElementById('index').value);

                                        var lvelElem = document.getElementById('levels');
                                        var rows = lvelElem.getElementsByClassName('row');
                                        console.log("rows: " + rows.length);
                                        for (var i = 0; i < rows.length; i++) {
                                            var inputs = rows[i].getElementsByTagName('input');
                                            inputs[0].setAttribute("name", 'level_name' + i);
                                            inputs[1].setAttribute("name", 'level_point' + i);
                                            /*inputs[2].setAttribute("name", 'quantity' + i);
                                            inputs[3].setAttribute("name", 'total' + i);*/
                                            /*var unitprice = parseFloat(inputs[1].value);
                                            var quantity = parseFloat(inputs[2].value);

                                            inputs[3].setAttribute("value", unitprice * quantity);*/

                                        }
                                    }

                                    /*function displayTotal() {
                                        var  productElem = document.getElementById('products');
                                        var rows = productElem.getElementsByClassName('row');
                                        console.log("rows: " + rows.length);
                                        for (var i = 0; i < rows.length; i++) {
                                            var inputs = rows[i].getElementsByTagName('input');

                                            var unitprice = parseFloat(inputs[1].value);
                                            var quantity = parseFloat(inputs[2].value);
                                            if(!Number.isNaN(unitprice) && !Number.isNaN(quantity)) {
                                                inputs[3].setAttribute("value", unitprice * quantity);
                                            }
                                        }
                                    }*/
                                    /*function remove_product_fields(rid) {
                                        $('.removeclass'+rid).remove();
                                    }*/
                                </script>
                            </div>
                            @if($index === 0)
                                <script type="text/javascript">
                                    addLevelFilds();
                                </script>
                            @endif



                            {{--<div class="row mb-3">
                                <label for="classic_threshold" class="col-md-6 col-form-label text-md-end">
                                    {{ 'Seuil de points pour obtenir un bon de type CLASSIC'}}</label>
                                <div class="col-md-6">
                                    <input id="classic_threshold" type="number"
                                           class="form-control @error('classic_threshold') is-invalid @enderror"
                                           name="classic_threshold"
                                           value="{{$classic_threshold}}"
                                           required
                                           autocomplete="classic_threshold"
                                           autofocus>
                                    @error('classic_threshold')
                                         <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="premium_threshold" class="col-md-6 col-form-label text-md-end">
                                    {{ 'Seuil de points pour obtenir un bon de type PREMIUM'}}</label>
                                <div class="col-md-6">
                                    <input id="premium_threshold" type="number"
                                           class="form-control @error('premium_threshold') is-invalid @enderror"
                                           name="premium_threshold"
                                           value="{{$premium_threshold}}"
                                           required
                                           autocomplete="premium_threshold"
                                           autofocus>
                                    @error('premium_threshold')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="gold_threshold" class="col-md-6 col-form-label text-md-end">
                                    {{ 'Seuil de points pour obtenir un bon de type GOLD'}}</label>
                                <div class="col-md-6">
                                    <input id="gold_threshold" type="number"
                                           class="form-control @error('gold_threshold') is-invalid @enderror"
                                           name="gold_threshold"
                                           value="{{$gold_threshold}}"
                                           required
                                           autocomplete="gold_threshold"
                                           autofocus>
                                    @error('gold_threshold')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>--}}

                            <div class="row mb-3">
                                <label for="voucher_duration_in_month" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Duree d\'un bon (Nombre de mois)'}}</label>
                                <div class="col-md-7">
                                    <input id="voucher_duration_in_month" type="number"
                                           class="form-control @error('voucher_duration_in_month') is-invalid @enderror"
                                           name="voucher_duration_in_month"
                                           value="{{$voucher_duration_in_month}}"
                                           required
                                           autocomplete="voucher_duration_in_month"
                                           autofocus>
                                    @error('voucher_duration_in_month')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password_recovery_request_duration"
                                       class="col-md-5 col-form-label text-md-end">
                                    {{ 'Duree de la demande de changement du mot de passe (Nombre d\'heures)'}}</label>
                                <div class="col-md-7">
                                    <input id="password_recovery_request_duration" type="number"
                                           class="form-control @error('password_recovery_request_duration') is-invalid @enderror"
                                           name="password_recovery_request_duration"
                                           value="{{intdiv($password_recovery_request_duration, 60)}}"
                                           required
                                           autocomplete="password_recovery_request_duration"
                                           autofocus>
                                    @error('password_recovery_request_duration')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="enterprise_name" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Nom de votre entreprise'}}</label>
                                <div class="col-md-7">
                                    <input id="enterprise_name" type="text"
                                           class="form-control @error('enterprise_name') is-invalid @enderror"
                                           name="enterprise_name"
                                           value="{{$enterprise_name}}"
                                           required
                                           autocomplete="enterprise_name"
                                           autofocus>
                                    @error('enterprise_name')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="enterprise_email" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Email de votre entreprise'}}</label>
                                <div class="col-md-7">
                                    <input id="enterprise_email" type="email"
                                           class="form-control @error('enterprise_email') is-invalid @enderror"
                                           name="enterprise_email"
                                           value="{{$enterprise_email}}"
                                           required
                                           autocomplete="enterprise_email"
                                           autofocus>
                                    @error('enterprise_email')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="enterprise_phone" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Telephone de votre entreprise'}}</label>
                                <div class="col-md-7">
                                    <input id="enterprise_phone" type="tel"
                                           class="form-control @error('enterprise_phone') is-invalid @enderror"
                                           name="enterprise_phone"
                                           value="{{$enterprise_phone}}"
                                           required
                                           autocomplete="enterprise_phone"
                                           autofocus>
                                    @error('enterprise_phone')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="enterprise_website" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Lien du site web de votre entreprise'}}</label>
                                <div class="col-md-7">
                                    <input id="enterprise_website" type="text"
                                           class="form-control @error('enterprise_website') is-invalid @enderror"
                                           name="enterprise_website"
                                           value="{{$enterprise_website}}"
                                           required
                                           autocomplete="enterprise_website"
                                           autofocus>
                                    @error('enterprise_website')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="enterprise_address" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Adresse de votre entreprise'}}</label>
                                <div class="col-md-7">
                                    <input id="enterprise_address" type="text"
                                           class="form-control @error('enterprise_address') is-invalid @enderror"
                                           name="enterprise_address"
                                           value="{{$enterprise_address}}"
                                           required
                                           autocomplete="enterprise_address"
                                           autofocus>
                                    @error('enterprise_address')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="enterprise_logo" class="col-md-5 col-form-label text-md-end">
                                    {{ 'Logo de votre entreprise'}}
                                    @if(count(Config::where('is_applicable', true)->get()) === 1)
                                        @php $config0 = Config::where('is_applicable', true)->get()[0]; @endphp
                                        @if(strlen($config0->enterprise_logo) > 0)
                                            <br><img src="{{asset('storage/' .$config0->enterprise_logo)}}"
                                                     style="margin-top: 0; margin-bottom: 0;" height="65" width="65"
                                                     alt="">
                                        @endif
                                    @endif
                                </label>
                                <div class="col-md-7">
                                    <input id="enterprise_logo" type="file"
                                           class="form-control @error('enterprise_logo') is-invalid @enderror"
                                           name="enterprise_logo" onchange="toggleLogoSizeIndicator();">
                                    <small style="float: right; color: orangered;" >
                                        <img src="{{asset('images/icons8-warning-25.png')}}" alt="w" width="20" height="20">
                                        {{'La taille de l\'image est limite a 10 MB.'}}
                                    </small>
                                    <small  id="ligo-size-indicator" style="display: none; color: red; float: right;">
                                        <img src="{{asset('images/icons8-warning-48.gif')}}" alt="Danger" width="20" height="20">
                                        {{'Le volume du fichier a depasse la limite requise (10MB).'}}
                                    </small>
                                    @error('enterprise_logo')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <strong>{{ 'Enregistrer' }}</strong>
                                    </button>
                                </div>
                            </div>

                            {{--</div>--}}
                            {{--<div class="modal-footer">
                                <button type="button" class="btn btn-danger"
                                        data-bs-dismiss="modal">Annuler
                                </button>
                                <button type="submit" class="btn btn-success">Enregistrer
                                </button>
                            </div>--}}
                        </form>

                        <script type="text/javascript">
                            function checkForm(){
                                var validation  = validateLogoSize();
                                if(validation === true){
                                   return true;
                                }
                                console.log(validation);
                                alert("Fichier pour logo tres volumineux. Taille limite: 10MB.");
                                return false;
                            }
                            function validateLogoSize(){
                                var enterprise_logo = document.getElementById('enterprise_logo');
                                console.log(enterprise_logo.files[0].size);
                                return !(enterprise_logo.files.length > 0 && enterprise_logo.files[0].size > 10000000);
                            }

                            function toggleLogoSizeIndicator(){

                                var enterprise_logo = document.getElementById('enterprise_logo');
                                if(enterprise_logo.files.length > 0){
                                    var indicator = document.getElementById('ligo-size-indicator');
                                    if(enterprise_logo.files[0].size > 10000000){
                                        indicator.style.display = 'block';
                                    }else{
                                        indicator.style.display = 'none';
                                    }
                                }
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
