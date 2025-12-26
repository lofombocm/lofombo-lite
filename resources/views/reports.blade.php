@php
    use App\Models\Loyaltytransaction;
    use App\Models\Product;
    use App\Models\Client;
    use App\Models\Reward;
    use App\Models\Voucher;
    use App\Models\Purchase;
    use App\Models\Loyaltyaccount;
@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-body" >
                        {{--@if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                        <div>
                            <div style="text-align: center; width: 100%;">
                                <h2>{{__("Quelques données")}}</h2>
                                <br>
                            </div>
                        </div>--}}
                        <div class="row">

                            <br>

                            <table class="table table-borderless ">
                                <thead>
                                <tr>
                                    <th colspan="5" style="text-align: center;">
                                        <h2 style="border: 0 red solid; padding: 1px; color: #164fa9; font-size: 2em; font-weight: bold;">
                                            {{__('Quelques Rapports')}}</h2>
                                        <br>
                                    </th>
                                </thead>
                                {{--<thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Rapport</th>
                                    <th scope="col">Filtre</th>
                                    <th scope="col">Periode</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>--}}
                                <tbody>
                                <tr>
                                    <th scope="row"><br><br>1</th>
                                    <td class="form-group">
                                        <br>
                                        <label for="_tx" class="text-md-start"><strong>{{ __('Transactions') }}</strong></label>
                                        <select
                                            id="_tx"
                                            class="form-control"
                                            name="_tx" required
                                            onselect="setTransactionType();"
                                        >
                                            <option value="ALL">{{__('Toutes les transactions')}}</option>
                                            <option value="PURCHASE_REGISTRATION">{{__('Achats enregistrés')}}</option>
                                            <option value="VOUCHER_GENERATION">{{__('Bons de Fidélité Générés')}}</option>
                                            <option value="ACCOUNT_INITIALIZATION">{{__('Initialisation de compte')}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <br>
                                        <label for="_from" class="text-md-start"><strong>{{ __("Période") }} &nbsp; &nbsp; {{ __("De") }}: </strong> </label>
                                        <input type="date" name="_from" id="_from" class="form-control" onselect="setFrom();">
                                        {{--<select
                                            id="_period"
                                            class="form-control"
                                            name="_period" required
                                            onselect="setPeriod();"
                                        >
                                            <option value="ALL">{{'Toutes les Periodes'}}</option>
                                            <option value="MONTHLY">{{'Mois dernier'}}</option>
                                            <option value="QUATERLY">{{'Les 3 derniers mois'}}</option>
                                            <option value="BIYEARLY">{{'Les six derniers mois'}}</option>
                                            <option value="YEARLY">{{'L\'an dernier'}}</option>
                                        </select>--}}


                                    </td>
                                    <td>
                                        <br>
                                        <label for="_to" class="text-md-start"><strong>&nbsp; &nbsp;&nbsp; &nbsp;{{ __("A") }}</strong></label>
                                        <input type="date" name="_to" id="_to" class="form-control" onselect="setTo();">

                                    </td>
                                    <td>
                                        <br><br>
                                        <form action="{{route('reports.txs')}}" method="GET"
                                              onsubmit="return submitTx();">
                                            <input type="hidden" name="tx" value="ALL" id="tx">
                                            <input type="hidden" name="from" value="" id="from">
                                            <input type="hidden" name="to" value="" id="to">
                                            <button role="button" class="btn btn-primary btn-sm" type="submit"
                                                    style="color: white;">
                                                <strong>{{__('Générer')}}</strong>
                                            </button>
                                        </form>
                                    </td>
                                    <script type="text/javascript">
                                        function setTransactionType(){
                                            document.getElementById('tx').setAttribute('value', document.getElementById('_tx').value);
                                        }

                                        function setFrom(){
                                            document.getElementById('from').setAttribute('value', document.getElementById('_from').value);
                                        }

                                        function setTo(){
                                            document.getElementById('to').setAttribute('value', document.getElementById('_to').value);
                                        }

                                        function submitTx(){
                                            document.getElementById('tx').setAttribute('value', document.getElementById('_tx').value);
                                            document.getElementById('from').setAttribute('value', document.getElementById('_from').value);
                                            document.getElementById('to').setAttribute('value', document.getElementById('_to').value);
                                            //alert('type: ' + document.getElementById('tx').value);
                                            //alert('period: ' + document.getElementById('period').value);
                                            return true;
                                        }
                                    </script>
                                </tr>

                                <tr>
                                    <th scope="row"><br><br>2</th>
                                    <td class="form-group">
                                        <br><br>
                                        <label for="_state" class="text-md-start"><strong>{{ __('Bons') }}</strong></label>
                                        <select
                                            id="_state"
                                            class="form-control"
                                            name="_state" required
                                            onselect="setVoucherState();"
                                        >
                                            <option value="ALL">{{ __("Tous les états") }}</option>
                                            <option value="GENERATED">{{ __("Etat Généré") }}</option>
                                            <option value="ACTIVATED">{{ __("Etat Activé") }}</option>
                                            <option value="USED">{{ __("Etat Utilisé") }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <br><br>
                                        <label for="_level" class="text-md-start"><strong>{{ __("Type de Bon") }}</strong></label>
                                        <select
                                            id="_level"
                                            class="form-control"
                                            name="_level" required
                                            onselect="setVoucherLevel();"
                                        >
                                            <option value="ALL">{{ __('Tous Types')}}</option>
                                            <?php
                                            $config = \App\Models\Config::where('is_applicable', true)->first();
                                            $levels = json_decode($config->levels);
                                            ?>
                                            @foreach($levels as $level)
                                                <option value="{{$level->id}}">{{$level->name}}</option>
                                            @endforeach

                                            {{--<option value="MONTHLY">{{'Mois dernier'}}</option>
                                            <option value="QUATERLY">{{'Les 3 derniers mois'}}</option>
                                            <option value="BIYEARLY">{{'Les six derniers mois'}}</option>
                                            <option value="YEARLY">{{'L\'an dernier'}}</option>--}}
                                        </select>

                                    </td>
                                    <td>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <br>
                                                <label for="_from1" class="text-md-start"><strong>{{ __('Période') }} &nbsp; &nbsp; {{__("De")}}: </strong> </label>
                                                <input type="date" name="_from1" id="_from1" class="form-control" onselect="setFrom1();">
                                            </div>
                                            <div class="col-md-6">
                                                <br>
                                                <label for="_to1" class="text-md-start"><strong>&nbsp; &nbsp;&nbsp; &nbsp;{{__("A")}}</strong></label>
                                                <input type="date" name="_to1" id="_to1" class="form-control" onselect="setTo1();">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <br><br><br>
                                        <form action="{{route('reports.vouchers')}}" method="GET"
                                              onsubmit="return submitVoucher();">
                                            <input type="hidden" name="state" value="ALL" id="state">
                                            <input type="hidden" name="level" value="ALL" id="level">
                                            <input type="hidden" name="configid" value="{{$config->id}}" id="configid">
                                            <input type="hidden" name="from" value="" id="from1">
                                            <input type="hidden" name="to" value="" id="to1">
                                            <button role="button" class="btn btn-primary btn-sm" type="submit" style="color: white;">
                                                <strong>{{__('Générer')}}</strong>
                                            </button>
                                        </form>
                                    </td>

                                    <script type="text/javascript">
                                        function setVoucherState(){
                                            document.getElementById('state').setAttribute('value', document.getElementById('_state').value);
                                        }

                                        function setVoucherLevel(){
                                            document.getElementById('level').setAttribute('value', document.getElementById('_level').value);
                                        }
                                        function setFrom1(){
                                            document.getElementById('from1').setAttribute('value', document.getElementById('_from1').value);
                                        }

                                        function setTo1(){
                                            document.getElementById('to1').setAttribute('value', document.getElementById('_to1').value);
                                        }

                                        function submitVoucher(){
                                            document.getElementById('state').setAttribute('value', document.getElementById('_state').value);
                                            document.getElementById('level').setAttribute('value', document.getElementById('_level').value);
                                            document.getElementById('from1').setAttribute('value', document.getElementById('_from1').value);
                                            document.getElementById('to1').setAttribute('value', document.getElementById('_to1').value);

                                            //alert('type: ' + document.getElementById('tx').value);
                                            //alert('period: ' + document.getElementById('period').value);
                                            return true;
                                        }
                                    </script>
                                </tr>

                                <tr>
                                    <th scope="row"><br><br>3</th>
                                    <td class="form-group">
                                        <br><br>
                                        <label for="_etat" class="text-md-start"><strong>{{ 'Clients' }}</strong></label>
                                        <select
                                            id="_etat"
                                            class="form-control"
                                            name="_etat" required
                                            onselect="setClientState();"
                                        >
                                            <option value="ALL">{{__("Tous les états")}}</option>
                                            <option value="ACTIVATED">{{__("Etat Activé")}}</option>
                                            <option value="DEACTIVATED">{{ __("Etat Desactivé") }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <br><br>
                                        <label for="_from2" class="text-md-start"><strong>{{ __('Période') }} &nbsp; &nbsp; {{__("De")}}: </strong> </label>
                                        <input type="date" name="_from2" id="_from2" class="form-control" onselect="setFrom2();">
                                    </td>

                                    <td>
                                        <br><br>
                                        <label for="_to2" class="text-md-start"><strong>&nbsp; &nbsp;&nbsp; &nbsp;{{__("A")}}</strong></label>
                                        <input type="date" name="_to2" id="_to2" class="form-control" onselect="setTo2();">
                                    </td>
                                    <td>
                                        <br><br><br>
                                        <form action="{{route('reports.clients')}}" method="GET"
                                              onsubmit="return submitClient();">
                                            <input type="hidden" name="etat" value="ALL" id="etat">
                                            <input type="hidden" name="configid" value="{{$config->id}}" id="configid">
                                            <input type="hidden" name="from" value="" id="from2">
                                            <input type="hidden" name="to" value="" id="to2">
                                            <button role="button" class="btn btn-primary btn-sm" type="submit"
                                                    style="color: white;">
                                                <strong>{{__('Générer')}}</strong>
                                            </button>
                                        </form>
                                    </td>
                                    <script type="text/javascript">
                                        function setClientState(){
                                            document.getElementById('etat').setAttribute('value', document.getElementById('_etat').value);
                                        }

                                        /*function setVoucherLevel(){
                                            document.getElementById('level').setAttribute('value', document.getElementById('_level').value);
                                        }*/
                                        function setTo2(){
                                            document.getElementById('to2').setAttribute('value', document.getElementById('_to2').value);
                                        }

                                        function setFrom2(){
                                            document.getElementById('from2').setAttribute('value', document.getElementById('_from2').value);
                                        }

                                        function submitClient(){
                                            document.getElementById('etat').setAttribute('value', document.getElementById('_etat').value);
                                            //document.getElementById('level').setAttribute('value', document.getElementById('_level').value);
                                            document.getElementById('from2').setAttribute('value', document.getElementById('_from2').value);
                                            document.getElementById('to2').setAttribute('value', document.getElementById('_to2').value);
                                            return true;
                                        }
                                    </script>
                                </tr>
                                </tbody>
                            </table>
                            <br><br><br><br>
                        </div>

                        <br><br><br><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

