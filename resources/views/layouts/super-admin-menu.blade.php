@php
    use App\Models\Client;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Config;
@endphp

<div class="col-md-3" >
    <div class="card">
        <div class="card-header"><h5>{{ 'Menu' }}</h5></div>
        <div class="card-body" >
            <div class="list-group list-group-flush">
                @if(Auth::guard('super_admin')->check() && Auth::guard('super_admin')->user()->is_super_admin)
                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('home-super-admin')}}">
                        <h6><img src="{{asset('images/icons8-certificate-25.png')}}" alt=""> &nbsp;{{ __('Licences') }}</h6>
                    </a>
                @endif
                {{--@if(count(Config::where('is_applicable', true)->get()) > 0)
                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('home.clients-list')}}">
                        <h6><img src="{{asset('images/icons8-list-24.png')}}" alt=""> &nbsp;{{ 'Liste des Clients' }}</h6>
                    </a>
                @endif

                @if(count(Client::all()) > 0)
                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('purchases.index')}}">
                        <h6><img src="{{asset('images/icons8-purchase-order-25.png')}}" alt=""> &nbsp;{{ 'Enregistrer un Achat' }}</h6>
                    </a>

                        @if(count(\App\Models\Voucher::all()) > 0)
                            <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('clients.getVouchersAll.all')}}">
                                <h6><img src="{{asset('images/icons8-loyalty-card-25.png')}}" alt=""> &nbsp;{{ 'Tous les bons' }}</h6>
                            </a>
                        @endif

                @endif
                @if(count(Config::where('is_applicable', true)->get()) > 0)
                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('rewards.index.list') }}">
                        <h6><img src="{{asset('images/icons8-reward-25.png')}}" alt=""> &nbsp;{{ 'Recompenses' }}</h6>
                    </a>
                @endif

                @if(count(Config::where('is_applicable', true)->get()) > 0)

                @endif

                @if(Auth::guard('super_admin')->check() && Auth::guard('super_admin')->user()->is_super_admin)

                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{route('configs.index')}}"
                       --}}{{--data-bs-toggle="modal" data-bs-target="#system-config-modal"--}}{{-- id="lien-pour-configuration">
                        <h6><img src="{{asset('images/icons8-configuration-25.png')}}" alt=""> &nbsp;{{ 'Configurer les parametres' }}</h6>
                    </a>

                    @if(count(Config::where('is_applicable', true)->get()) === 0)
                        --}}{{--{{count(Config::all())}}--}}{{--
                        <script type="text/javascript">
                            var link = document.getElementById('lien-pour-configuration');
                            if (link) {
                                console.log(link.id);
                                var  evt = new MouseEvent('click', {
                                    bubbles: true,
                                    cancelable: true,
                                    view: window
                                });
                                var ret = link.dispatchEvent(evt);
                                link.click();
                                console.log(ret);
                            }
                        </script>
                    @endif

                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('utilisateurs.admin')}}">
                        <h6><img src="{{asset('images/icons8-user-25.png')}}" alt=""> &nbsp;{{ 'Utilisateurs' }}</h6>
                    </a>

                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('bi.menu')}}">
                        <h6><img src="{{asset('images/icons8-report-file-25.png')}}" alt=""> &nbsp;{{ 'Rapports' }}</h6>
                    </a>

                        <a class="list-group-item list-group-item-action btn btn-link"  href="{{ route('send-bulk-message.admin')}}">
                            <h6><img src="{{asset('images/icons8-sent-25.png')}}" alt=""> &nbsp;{{ 'Notifier' }}</h6>
                        </a>
                @endif
                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{route('users.purchases-products.index')}}"
                        id="lien-pour-produits-enregistres">
                        <h6><img src="{{asset('images/icons8-product-25.png')}}" alt=""> &nbsp;{{ 'Produits enregistres' }}</h6>
                    </a>

                    <a class="list-group-item list-group-item-action btn btn-link"  href="{{route('home.loyaltytransactions.all')}}"
                       id="lien-pour-transaction-enregistres">
                        <h6>
                            <img src="{{asset('images/icons8-transaction-25.png')}}" alt=""> &nbsp;{{ 'Transactions' }}
                            <span class="badge bg-primary position-absolute top|start-*"
                                  style="position: relative; right: 0; padding-top: 7px;">{{''}}</span>
                        </h6>
                    </a>--}}

            </div>
        </div>
    </div>
</div>


