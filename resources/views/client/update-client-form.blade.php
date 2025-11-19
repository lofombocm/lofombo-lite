@php
    use App\Models\Config;
    use Illuminate\Support\Carbon;
@endphp
@extends('layouts.app-client')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.client-menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ 'Parametrage' }}</div>
                    <div class="card-body">
                        <?php
                        //$conversion = ConversionAmountPoint::where('active', true)->where('is_applicable', true)->first();
                        //$threshold = Threshold::where('active', true)->where('is_applicable', true)->first();
                        $configuration = Config::where('is_applicable', true)->first();
                        $levels = json_decode($configuration->levels);
                        ?>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row justify-content-center">

                            <form method="POST" action="{{route('clients.post.update.client', $client->id)}}"
                                  onsubmit="return true;">
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
                                    <input type="hidden" name="clientid" value="{{$client->id}}">

                                    <div class="row mb-3">
                                        <label for="name"
                                               class="col-md-4 col-form-label text-md-end">{{ 'Nom du Client' }}
                                            <b class="" style="color: red;">*</b>
                                        </label>

                                        <div class="col-md-6">
                                            <input id="name" type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   name="name" value="{{ $client->name }}" required
                                                   autocomplete="name" autofocus>

                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="telephone"
                                               class="col-md-4 col-form-label text-md-end">
                                            {{ 'Telephone' }}
                                            <b class="" style="color: red;">*</b>
                                        </label>

                                        <div class="col-md-6">
                                            <input id="telephone" type="tel"
                                                   class="form-control @error('telephone') is-invalid @enderror"
                                                   name="telephone" value="{{ $client->telephone }}"
                                                   required autocomplete="telephone">
                                            @error('telephone')
                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="email"
                                               class="col-md-4 col-form-label text-md-end">{{ 'E-Mail'}}</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   name="email" value="{{ $client->email }}"
                                                   autocomplete="email">

                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="birthdate"
                                               class="col-md-4 col-form-label text-md-end"><br>{{ 'Date de Naissance' }}
                                        </label>
                                            <?php
                                            $date = Carbon::now();
                                            $thisyear = $date->year;
                                            //selected
                                            $year = "00";
                                            $day = "00";
                                            $month = "00";
                                            if ($client->birthdate !== null && strlen($client->birthdate) > 0) {
                                                $ymd = explode('-', $client->birthdate);

                                                $year = $ymd[0];
                                                $month = $ymd[1];
                                                $day = $ymd[2];
                                            }
                                            ?>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="day">Jour</label>
                                                    <select class="form-select" id="day" name="day">
                                                        <option value=""> --Choisir ici--</option>
                                                        <option
                                                            value="01" {{($day !== '00' ?  ($day === '01' ? 'selected' : ''): '')}}>
                                                            01
                                                        </option>
                                                        <option
                                                            value="02" {{($day !== '00' ?  ($day === '02' ? 'selected' : ''): '')}}>
                                                            02
                                                        </option>
                                                        <option
                                                            value="03" {{($day !== '00' ?  ($day === '03' ? 'selected' : ''): '')}}>
                                                            03
                                                        </option>
                                                        <option
                                                            value="04" {{($day !== '00' ?  ($day === '04' ? 'selected' : ''): '')}}>
                                                            04
                                                        </option>
                                                        <option
                                                            value="05" {{($day !== '00' ?  ($day === '05' ? 'selected' : ''): '')}}>
                                                            05
                                                        </option>
                                                        <option
                                                            value="06" {{($day !== '00' ?  ($day === '06' ? 'selected' : ''): '')}}>
                                                            06
                                                        </option>
                                                        <option
                                                            value="07" {{($day !== '00' ?  ($day === '07' ? 'selected' : ''): '')}}>
                                                            07
                                                        </option>
                                                        <option
                                                            value="08" {{($day !== '00' ?  ($day === '08' ? 'selected' : ''): '')}}>
                                                            08
                                                        </option>
                                                        <option
                                                            value="09" {{($day !== '00' ?  ($day === '09' ? 'selected' : ''): '')}}>
                                                            09
                                                        </option>
                                                        <option
                                                            value="10" {{($day !== '00' ?  ($day === '10' ? 'selected' : ''): '')}}>
                                                            10
                                                        </option>
                                                        <option
                                                            value="11" {{($day !== '00' ?  ($day === '11' ? 'selected' : ''): '')}}>
                                                            11
                                                        </option>
                                                        <option
                                                            value="12" {{($day !== '00' ?  ($day === '12' ? 'selected' : ''): '')}}>
                                                            12
                                                        </option>
                                                        <option
                                                            value="13" {{($day !== '00' ?  ($day === '13' ? 'selected' : ''): '')}}>
                                                            13
                                                        </option>
                                                        <option
                                                            value="14" {{($day !== '00' ?  ($day === '14' ? 'selected' : ''): '')}}>
                                                            14
                                                        </option>
                                                        <option
                                                            value="15" {{($day !== '00' ?  ($day === '15' ? 'selected' : ''): '')}}>
                                                            15
                                                        </option>
                                                        <option
                                                            value="16" {{($day !== '00' ?  ($day === '16' ? 'selected' : ''): '')}}>
                                                            16
                                                        </option>
                                                        <option
                                                            value="17" {{($day !== '00' ?  ($day === '17' ? 'selected' : ''): '')}}>
                                                            17
                                                        </option>
                                                        <option
                                                            value="18" {{($day !== '00' ?  ($day === '18' ? 'selected' : ''): '')}}>
                                                            18
                                                        </option>
                                                        <option
                                                            value="19" {{($day !== '00' ?  ($day === '19' ? 'selected' : ''): '')}}>
                                                            19
                                                        </option>
                                                        <option
                                                            value="20" {{($day !== '00' ?  ($day === '20' ? 'selected' : ''): '')}}>
                                                            20
                                                        </option>
                                                        <option
                                                            value="21" {{($day !== '00' ?  ($day === '21' ? 'selected' : ''): '')}}>
                                                            21
                                                        </option>
                                                        <option
                                                            value="22" {{($day !== '00' ?  ($day === '22' ? 'selected' : ''): '')}}>
                                                            22
                                                        </option>
                                                        <option
                                                            value="23" {{($day !== '00' ?  ($day === '23' ? 'selected' : ''): '')}}>
                                                            23
                                                        </option>
                                                        <option
                                                            value="24" {{($day !== '00' ?  ($day === '24' ? 'selected' : ''): '')}}>
                                                            24
                                                        </option>
                                                        <option
                                                            value="25" {{($day !== '00' ?  ($day === '25' ? 'selected' : ''): '')}}>
                                                            25
                                                        </option>
                                                        <option
                                                            value="26" {{($day !== '00' ?  ($day === '26' ? 'selected' : ''): '')}}>
                                                            26
                                                        </option>
                                                        <option
                                                            value="27" {{($day !== '00' ?  ($day === '27' ? 'selected' : ''): '')}}>
                                                            27
                                                        </option>
                                                        <option
                                                            value="28" {{($day !== '00' ?  ($day === '28' ? 'selected' : ''): '')}}>
                                                            28
                                                        </option>
                                                        <option
                                                            value="29" {{($day !== '00' ?  ($day === '29' ? 'selected' : ''): '')}}>
                                                            29
                                                        </option>
                                                        <option
                                                            value="30" {{($day !== '00' ?  ($day === '30' ? 'selected' : ''): '')}}>
                                                            30
                                                        </option>
                                                        <option
                                                            value="31" {{($day !== '00' ?  ($day === '31' ? 'selected' : ''): '')}}>
                                                            31
                                                        </option>
                                                    </select>

                                                </div>
                                                <div class="col-md-4">
                                                    <label for="month">Mois</label>
                                                    <select class="form-select" id="month" name="month">
                                                        <option value="">--Choisir ici --</option>
                                                        <option
                                                            value="01" {{($month !== '00' ?  ($month === '01' ? 'selected' : ''): '')}}>
                                                            Janvier
                                                        </option>
                                                        <option
                                                            value="02" {{($month !== '00' ?  ($month === '02' ? 'selected' : ''): '')}}>
                                                            Fevrier
                                                        </option>
                                                        <option
                                                            value="03" {{($month !== '00' ?  ($month === '03' ? 'selected' : ''): '')}}>
                                                            Mars
                                                        </option>
                                                        <option
                                                            value="04" {{($month !== '00' ?  ($month === '04' ? 'selected' : ''): '')}}>
                                                            Avril
                                                        </option>
                                                        <option
                                                            value="05" {{($month !== '00' ?  ($month === '05' ? 'selected' : ''): '')}}>
                                                            Mai
                                                        </option>
                                                        <option
                                                            value="06" {{($month !== '00' ?  ($month === '06' ? 'selected' : ''): '')}}>
                                                            Juin
                                                        </option>
                                                        <option
                                                            value="07" {{($month !== '00' ?  ($month === '07' ? 'selected' : ''): '')}}>
                                                            Juillet
                                                        </option>
                                                        <option
                                                            value="08" {{($month !== '00' ?  ($month === '08' ? 'selected' : ''): '')}}>
                                                            Aout
                                                        </option>
                                                        <option
                                                            value="09" {{($month !== '00' ?  ($month === '09' ? 'selected' : ''): '')}}>
                                                            Septembre
                                                        </option>
                                                        <option
                                                            value="10" {{($month !== '00' ?  ($month === '10' ? 'selected' : ''): '')}}>
                                                            Octobre
                                                        </option>
                                                        <option
                                                            value="11" {{($month !== '00' ?  ($month === '11' ? 'selected' : ''): '')}}>
                                                            Novembre
                                                        </option>
                                                        <option
                                                            value="12" {{($month !== '00' ?  ($month === '12' ? 'selected' : ''): '')}}>
                                                            Decembre
                                                        </option>
                                                    </select>

                                                </div>

                                                <div class="col-md-4">
                                                    <label for="year">Annee</label>
                                                    <select class="form-select" id="year" name="year">
                                                        <option value="">--Choisir ici --</option>
                                                        @for($i = $thisyear; $i >= 1900; $i--)
                                                            <option
                                                                value="{{$i}}" {{($year !== '00' ?  ($year === (''. $i) ? 'selected' : ''): '')}}>{{$i}}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>


                                            {{--<input id="birthdate" type="date" class="form-control @error('birthdate') is-invalid @enderror" name="birthdate"  autocomplete="birthdate">--}}

                                            @error('birthdate')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="gender"
                                               class="col-md-4 col-form-label text-md-end">{{ 'Civilite' }}</label>

                                        <div class="col-md-6">
                                            <select id="gender"
                                                    class="form-control form-select form-select-lg @error('gender') is-invalid @enderror"
                                                    name="gender">
                                                <option value="">Choisissez ici</option>
                                                <option
                                                    value="MONSIEUR" {{$client->gender === 'MONSIEUR' ? 'selected' : ''}}>
                                                    Monsieur
                                                </option>
                                                <option
                                                    value="MADAME" {{$client->gender === 'MADAME' ? 'selected' : ''}}>
                                                    Madame
                                                </option>
                                                <option
                                                    value="MADEMOISELLE" {{$client->gender === 'MADEMOISELLE' ? 'selected' : ''}}>
                                                    Mademoiselle
                                                </option>
                                            </select>

                                            @error('gender')
                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="city"
                                               class="col-md-4 col-form-label text-md-end">{{ 'Ville' }}</label>

                                        <div class="col-md-6">
                                            <input id="city" type="text"
                                                   class="form-control @error('city') is-invalid @enderror"
                                                   name="city" autocomplete="city"
                                                   value="{{ $client->city }}">

                                            @error('city')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="quarter"
                                               class="col-md-4 col-form-label text-md-end">{{ 'Quartier' }}</label>

                                        <div class="col-md-6">
                                            <input id="quarter" type="text"
                                                   class="form-control @error('quarter') is-invalid @enderror"
                                                   name="quarter" autocomplete="quarter"
                                                   value="{{ $client->quarter }}">

                                            @error('quarter')
                                            <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-0">
                                        <div class="col-md-6 offset-md-5">
                                            <button type="submit" class="btn btn-primary">
                                                {{ 'Enregistrer' }}
                                            </button>
                                        </div>
                                    </div>


                                </div>
                                {{--<div class="modal-footer">
                                    <button type="button" class="btn btn-danger"
                                            data-bs-dismiss="modal">Annuler
                                    </button>
                                    <button type="submit" class="btn btn-success">Ajourner le client
                                    </button>
                                </div>--}}
                            </form>

                        </div>

                    </div>
                    <div class="card-footer">
                        {{' '}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

