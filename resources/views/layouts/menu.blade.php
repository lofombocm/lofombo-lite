@php
    use App\Models\ConversionAmountPoint;
    use App\Models\Client;
    use \Illuminate\Support\Facades\Auth;
@endphp

<div class="col-md-3">
    <div class="card">
        <div class="card-header">{{ 'Menu' }}</div>
        <div class="card-body" >
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action btn btn-link"  href="{{ url('/home')}}">
                    {{ 'Liste des Clients' }}
                </a>
                @if(count(ConversionAmountPoint::all()) > 0)
                    @if(count(Client::all()) > 0)
                        <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('purchases.index')}}">
                            {{ 'Enregistrer un Achat' }}
                        </a>
                    @endif
                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('clients.index')}}">
                        {{ 'Enregistrer un Client' }}
                    </a>
                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('home.loyaltytransactions')}}">
                        {{ 'Liste des transactions' }}
                    </a>
                @endif

                @if(!(Auth::user() == null) && Auth::user()->is_admin)

                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('conversions-amount-points.index')}}">
                        {{ 'Enregistrer une conversion ' }} <strong>{{ 'Montant Point' }}</strong>
                    </a>

                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('conversions-point-rewards.index')}}">
                        {{ 'Enregistrer une conversion ' }} <strong>{{ 'Point Recompense' }}</strong>
                    </a>

                    {{--<a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('conversions.index')}}">
                        {{ 'Enregistrer une conversion de point' }}
                    </a>--}}

                    {{--<a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('conversions.list')}}">
                        {{ 'Definir La Conversion de point a Appliquer' }}
                    </a>--}}

                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('enregistrement')}}">
                        {{ 'Enregistrer un utilisateur' }}
                    </a>

                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('transactiontype')}}">
                        {{ 'Enregistrer un type de transaction' }}
                    </a>

                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('thresholds.index')}}">
                        {{ 'Enregistrer les seuils de points pour les bons' }}
                    </a>

                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('rewards.index')}}">
                        {{ 'Enregistrer une recompense' }}
                    </a>

                @endif

            </div>

        </div>
    </div>
</div>


