@php
    use App\Models\Loyaltytransaction;
    use App\Models\Product;
    use App\Models\Client;
    use App\Models\Reward;
    use App\Models\Voucher;
    use App\Models\Purchase;
@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    {{--<div class="card-header">

                    </div>--}}
                    <div class="card-body" >
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
                        <br>
                        <div>
                            <div style="text-align: center; width: 100%;">
                                <h2>Valeurs de strategie</h2>
                                <br><br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-4">
                                <button class="btn btn-danger btn-lg"
                                        style="width: 100%;">
                                   <span style="color: white; font-size: xx-large;">
                                       {{'Total des Achats'}}
                                       <br>
                                       <strong>{{Purchase::sum('amount')}} {{isset($config) ? $config->currency_name : 'FCFA'}} ({{Purchase::count()}})</strong>
                                   </span>
                                </button>
                            </div>
                            <div class="col col-md-4">
                                <button class="btn btn-primary btn-lg"
                                        style="width: 100%;">
                                    <span style="color: white; font-size: xx-large;">
                                        {{'Total des Recompenses'}}
                                        <br>
                                        <strong>{{Reward::sum('value')}} {{isset($config) ? $config->currency_name : 'FCFA'}} ({{Reward::count()}})</strong>
                                   </span>
                                </button>
                            </div>

                            <div class="col col-md-4">
                                <button class="btn btn-success btn-lg"
                                        style="width: 100%;">
                                <span style="color: white; font-size: xx-large;">
                                   {{'Total des Bons'}}
                                    <br>&nbsp;
                                    <strong><br>{{Voucher::count()}}</strong>
                               </span>
                                </button>
                            </div>

                        </div>
                        <br><br>

                        <div class="row">
                            <div class="col col-md-4">
                                <button class="btn btn-info btn-lg"
                                        style="width: 100%;">
                                   <span style="color: white; font-size: x-large;">
                                       {{'Total des Clients'}}
                                       <br><br>
                                       <strong>{{Client::count()}}</strong>
                                   </span>
                                </button>
                            </div>
                            <div class="col col-md-4">
                                <button class="btn btn-warning btn-lg"
                                        style="width: 100%;">
                                    <span style="color: black; font-size: x-large;">
                                        {{'Total des Produits'}}
                                        <br><br>
                                        <strong>{{Product::count()}}</strong>
                                   </span>
                                </button>
                            </div>

                            <div class="col col-md-4">
                                <button class="btn btn-secondary btn-lg"
                                        style="width: 100%;">
                                <span style="color: white; font-size: x-large;">
                                   {{'Total des Transactions'}}
                                    <br><strong>{{Loyaltytransaction::sum('amount')}} {{isset($config) ? $config->currency_name : 'FCFA'}}
                                        ({{Loyaltytransaction::count()}})</strong>
                               </span>
                                </button>
                            </div>

                        </div>
                        <div class="row" style="border-bottom: 1px black solid; margin-top: 20px;">

                        </div>
                        <br><br>
                        <div class="row">

                            <br>

                            <table class="table table-borderless ">
                                <thead>
                                    <tr>
                                        <th colspan="5" style="text-align: center;">
                                            <h2>Quelques rapports</h2>
                                            <br><br>
                                        </th>
                                    </tr>
                                </thead>
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Rapport</th>
                                    <th scope="col">Filtre</th>
                                    <th scope="col">Periode</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row"><br><br>1</th>
                                    <td class="form-group">
                                        <br><br>
                                        <label for="_tx" class="text-md-start"><strong>{{ 'Transactions' }}</strong></label>
                                        <select
                                            id="_tx"
                                            class="form-control"
                                            name="_tx" required
                                            onselect="setTransactionType();"
                                        >
                                            <option value="ALL">Tous les transactions</option>
                                            <option value="PURCHASE_REGISTRATION">Pour les Achats</option>
                                            <option value="VOUCHER_GENERATION">Pour Generation des Bons</option>
                                            <option value="ACCOUNT_INITIALIZATION">Initialistion de compte</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        <br><br>
                                        <label for="_period" class="text-md-start"><strong>{{ 'Periode' }}</strong></label>
                                        <select
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
                                        </select>

                                    </td>
                                    <td>
                                        <br><br><br>
                                        <form action="{{route('reports.txs')}}" method="GET"
                                        onsubmit="return submitTx();">
                                            <input type="hidden" name="tx" value="ALL" id="tx">
                                            <input type="hidden" name="period" value="ALL" id="period">
                                            <button role="button" class="btn btn-primary btn-sm" type="submit"
                                            style="color: white;">
                                                <strong>{{'Generer'}}</strong>
                                            </button>
                                        </form>
                                    </td>
                                    <script type="text/javascript">
                                        function setTransactionType(){
                                            document.getElementById('tx').setAttribute('value', document.getElementById('_tx').value);
                                        }

                                        function setPeriod(){
                                            document.getElementById('period').setAttribute('value', document.getElementById('_period').value);
                                        }

                                        function submitTx(){
                                            document.getElementById('tx').setAttribute('value', document.getElementById('_tx').value);
                                            document.getElementById('period').setAttribute('value', document.getElementById('_period').value);
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
                                        <label for="_state" class="text-md-start"><strong>{{ 'Bons' }}</strong></label>
                                        <select
                                            id="_state"
                                            class="form-control"
                                            name="_state" required
                                            onselect="setVoucherState();"
                                        >
                                            <option value="ALL">Tous les etats</option>
                                            <option value="GENERATED">Etat Genere</option>
                                            <option value="ACTIVATED">Etat Active</option>
                                            <option value="USED">Etat Utilise</option>
                                        </select>
                                    </td>
                                    <td>
                                        <br><br>
                                        <label for="_level" class="text-md-start"><strong>{{ 'Niveaux' }}</strong></label>
                                        <select
                                            id="_level"
                                            class="form-control"
                                            name="_level" required
                                            onselect="setVoucherLevel();"
                                        >
                                            <option value="ALL">{{'Toutes les niveaux'}}</option>
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
                                    <td >
                                        <br><br>
                                        <label for="_period1" class="text-md-start"><strong>{{ 'Periode' }}</strong></label>
                                        <select
                                            id="_period1"
                                            class="form-control"
                                            name="_period1" required
                                            onselect="setPeriod1();"
                                        >
                                            <option value="ALL">{{'Toutes les Periodes'}}</option>
                                            <option value="MONTHLY">{{'Mois dernier'}}</option>
                                            <option value="QUATERLY">{{'Les 3 derniers mois'}}</option>
                                            <option value="BIYEARLY">{{'Les six derniers mois'}}</option>
                                            <option value="YEARLY">{{'L\'an dernier'}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <br><br><br>
                                        <form action="{{route('reports.vouchers')}}" method="GET"
                                              onsubmit="return submitVoucher();">
                                            <input type="hidden" name="state" value="ALL" id="state">
                                            <input type="hidden" name="level" value="ALL" id="level">
                                            <input type="hidden" name="configid" value="{{$config->id}}" id="configid">
                                            <input type="hidden" name="period" value="ALL" id="period1">
                                            <button role="button" class="btn btn-primary btn-sm" type="submit"
                                                    style="color: white;">
                                                <strong>{{'Generer'}}</strong>
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
                                        function setPeriod1(){
                                            document.getElementById('period1').setAttribute('value', document.getElementById('_period1').value);
                                        }

                                        function submitVoucher(){
                                            document.getElementById('state').setAttribute('value', document.getElementById('_state').value);
                                            document.getElementById('level').setAttribute('value', document.getElementById('_level').value);
                                            document.getElementById('period1').setAttribute('value', document.getElementById('_period1').value);

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
                                            <option value="ALL">Tous les etats</option>
                                            <option value="ACTIVATED">Etat Active</option>
                                            <option value="DEACTIVATED">Etat Desactive</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        <br><br>
                                        <label for="_period2" class="text-md-start"><strong>{{ 'Periode' }}</strong></label>
                                        <select
                                            id="_period2"
                                            class="form-control"
                                            name="_period2" required
                                            onselect="setPeriod2();"
                                        >
                                            <option value="ALL">{{'Toutes les Periodes'}}</option>
                                            <option value="MONTHLY">{{'Mois dernier'}}</option>
                                            <option value="QUATERLY">{{'Les 3 derniers mois'}}</option>
                                            <option value="BIYEARLY">{{'Les six derniers mois'}}</option>
                                            <option value="YEARLY">{{'L\'an dernier'}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <br><br><br>
                                        <form action="{{route('reports.clients')}}" method="GET"
                                              onsubmit="return submitClient();">
                                            <input type="hidden" name="etat" value="ALL" id="etat">
                                            <input type="hidden" name="configid" value="{{$config->id}}" id="configid">
                                            <input type="hidden" name="period" value="ALL" id="period2">
                                            <button role="button" class="btn btn-primary btn-sm" type="submit"
                                                    style="color: white;">
                                                <strong>{{'Generer'}}</strong>
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
                                        function setPeriod2(){
                                            document.getElementById('period2').setAttribute('value', document.getElementById('_period2').value);
                                        }

                                        function submitClient(){
                                            document.getElementById('etat').setAttribute('value', document.getElementById('_etat').value);
                                            //document.getElementById('level').setAttribute('value', document.getElementById('_level').value);
                                            document.getElementById('period2').setAttribute('value', document.getElementById('_period2').value);
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

