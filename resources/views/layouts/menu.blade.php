<div class="col-md-3">
    <div class="card">
        <div class="card-header">{{ 'Menu' }}</div>
        <div class="card-body" >
            <div class="list-group list-group-flush">
                @if(count(\App\Models\Conversion::all()) > 0)
                    @if(count(\App\Models\Client::all()) > 0)
                        <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('purchases.index')}}">
                            {{ 'Enregistrer un Achat' }}
                        </a>
                    @endif
                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('clients.index')}}">
                        {{ 'Enregistrer un Client' }}
                    </a>
                @endif

                @if(!(\Illuminate\Support\Facades\Auth::user() == null) && \Illuminate\Support\Facades\Auth::user()->is_admin)
                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('conversions.index')}}">
                        {{ 'Enregistrer une conversion de point' }}
                    </a>

                        <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('conversions.list')}}">
                            {{ ' Definir La Conversion de point a Appliquer' }}
                        </a>

                @endif

            </div>

        </div>
    </div>
</div>


