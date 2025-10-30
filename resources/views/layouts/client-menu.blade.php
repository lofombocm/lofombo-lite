@php use App\Http\Controllers\Reward\RewardController;use App\Models\Loyaltyaccount;use App\Models\Threshold;use App\Models\Transactiontype;use Illuminate\Support\Facades\Auth; @endphp
<div class="col-md-3">
    <div class="card">
        <div class="card-header">{{ 'Menu' }}</div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                {{--<a class="list-group-item list-group-item-action"  href="{{ route('vouchers.index')}}">
                    {{ 'Bons d\'Achats' }}
                </a>--}}
                {{--</div><div class="list-group list-group-flush">--}}
                <?php
                $client = Auth::guard('client')->user();
                $loyaltyAccount = Loyaltyaccount::where('holderid', $client->id)->first();
                $threshold = Threshold::where('is_applicable', true)->where('active', true)->first();
                //echo $client->name;
                $point = $loyaltyAccount->point_balance;

                $isgold = $point >= $threshold->gold_threshold;
                $isPremium = $point >= $threshold->premium_threshold && $point < $threshold->gold_threshold;
                $isClassic = $point >= $threshold->classic_threshold && $point < $threshold->premium_threshold;
                ?>

                @if($loyaltyAccount->point_balance >= $threshold->classic_threshold)
                    <a class="list-group-item list-group-item-action" href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer un bon' }}
                        <span class="badge bg-primary position-absolute top|start-*"
                              style="position: relative; right: 0;">{{$point}}</span>
                    </a>
                    {{--<button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#generate-voucher-modal">
                        {{ 'Generer un bon' }}
                    </button>--}}
                    <!-- Modal -->
                    <div class="modal fade" id="confirm-generate-voucher" data-bs-backdrop="static"
                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                            <div class="modal-content">
                                    <?php
                                    $bestRewardAndConversion = RewardController::getBestRewards($loyaltyAccount->point_balance);
                                    $bestReward = null;
                                    $conversionUsed = null;

                                    if ($bestRewardAndConversion === null) {
                                        $bestReward = null;
                                        $conversionUsed = null;
                                    } else {
                                        $bestReward = $bestRewardAndConversion['bestreward'];
                                        $conversionUsed = $bestRewardAndConversion['conversionused'];
                                    }

                                    ?>
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Point cummule:
                                        <strong
                                            style="color: darkred;">{{$loyaltyAccount->point_balance}}
                                            points</strong></h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="alert alert-light">
                                    <div class="alert alert-info">
                                        <ul>
                                            <li>
                                                <h6>De {{$threshold->classic_threshold}}
                                                    a {{$threshold->premium_threshold}} points, vous
                                                    gagnez un Bon de type <strong
                                                        style="color: #495057;">Classique</strong></h6>
                                            </li>
                                            <li>
                                                <h6>De {{$threshold->premium_threshold}}
                                                    a {{$threshold->gold_threshold}} points, vous gagnez
                                                    un Bon de type <strong style="color: #198754;">Premium</strong>
                                                </h6>
                                            </li>
                                            <li>
                                                <h6>Au dela de {{$threshold->gold_threshold}} points,
                                                    vous gagnez un Bon de type <strong
                                                        style="color: darkgoldenrod;">Gold</strong></h6>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                @if(!($bestReward === null))
                                        <?php
                                        $type = '';
                                        $points = $conversionUsed->min_point;
                                        if ($loyaltyAccount->point_balance >= $threshold->gold_threshold) {
                                            $type = 'GOLD';
                                        } else if ($loyaltyAccount->point_balance >= $threshold->premium_threshold) {
                                            $type = 'PREMIUM';
                                        } else {
                                            $type = 'CLASSIC';
                                        }

                                        ?>
                                    <div class="alert alert-light" style="margin-top: -20px;">
                                        <div class="alert alert-primary">
                                            <h5>
                                                vous pouvez generer un bon de type
                                                <strong>{{$type}}</strong>
                                                vous donnant droit a :
                                                <strong>{{$bestReward->name}}</strong>
                                                ayant une valeur de
                                                <strong>{{$bestReward->value}}</strong>
                                            </h5>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{route('vouchers.post')}}"
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

                                            <div class="row mb-3">
                                                @if(count(Transactiontype::where('code', 'TRANSACTIONTYPE_GEN_VOUCHER')->where('signe', -1)->where('active', true)->get()) === 1)
                                                        <?php
                                                        $transactiontype = Transactiontype::where('code', 'TRANSACTIONTYPE_GEN_VOUCHER')->where('signe', -1)->where('active', true)->first();
                                                        ?>
                                                    <input type="hidden" name="transactiontypeid"
                                                           value="{{$transactiontype->id}}">
                                                    {{--<label for="transactiontype" class="col-md-4 col-form-label text-md-end">{{ 'Type de transaction' }}</label>
                                                    <div class="col-md-6">
                                                        <select id="transactiontype" class="form-control form-select form-select-lg @error('transactiontype') is-invalid @enderror" name="transactiontype" >
                                                            <option value="">Choisissez ici</option>
                                                            <option value="GOLD">GOLD</option>
                                                            <option value="PREMIUM">PREMIUM</option>
                                                            <option value="CLASSIC">CLASSIC</option>
                                                        </select>

                                                        @error('level')
                                                        <span class="invalid-feedback" role="alert">
                                                             <strong>{{ $message }}</strong>
                                                         </span>
                                                        @enderror
                                                    </div>--}}
                                                @endif


                                                <input type="hidden" name="rewardid" id="rewardid"
                                                       value="{{$bestReward->id}}">
                                                <input type="hidden" name="clientid" id="clientid"
                                                       value="{{$client->id}}">
                                                <input type="hidden" name="conversionpointrewardid"
                                                       id="conversion_point_reward"
                                                       value="{{$conversionUsed->id}}">
                                                <input type="hidden" name="thresholdid"
                                                       value="{{$threshold->id}}">
                                                <input type="hidden" name="level" value="{{$type}}">
                                                {{--<input type="hidden" name="points" value="{{$points}}">--}}
                                                {{--<input type="hidden" name="clientid" id="clientid" value="{{$client->id}}">--}}


                                                {{-- --}}
                                            </div>

                                            {{--<div class="row mb-3">
                                                <label for="montant" class="col-md-4 col-form-label text-md-end">{{ 'Montant'}}</label>

                                                <div class="col-md-6">
                                                    <input id="montant" type="range" class="@error('montant') is-invalid @enderror" name="montant"
                                                           value="{{env('GOLD_THRESHOLD')}}" required autocomplete="montant" autofocus
                                                           min="{{env('GOLD_THRESHOLD')}}" max="{{$loyaltyAccount->amount_from_converted_point}}"
                                                           onchange="document.getElementById('montantg').innerHTML = this.value" ><small id="montantg">{{env('GOLD_THRESHOLD')}}</small>

                                                    @error('montant')
                                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                                    @enderror
                                                </div>
                                            </div>--}}


                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                    data-bs-dismiss="modal">Annuler
                                            </button>
                                            <button type="submit" class="btn btn-success">Generer
                                            </button>
                                        </div>
                                    </form>

                                @else
                                    <div>Aucune meilleur recompense trouvee</div>
                                @endif


                            </div>
                        </div>
                    </div>
                @endif

                {{--@if($isgold)
                        <?php
                        $numGold = intdiv($point, intval(env('GOLD_THRESHOLD')));
                        ?>
                    <a class="list-group-item list-group-item-action" href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type GOLD' }}
                        <span class="badge bg-primary position-absolute top|start-*"
                              style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <a class="list-group-item list-group-item-action" href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type PREMIUM' }}
                        <span class="badge bg-primary position-absolute top|start-*"
                              style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <a class="list-group-item list-group-item-action" href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type CLASSIC' }}
                        <span class="badge bg-primary position-absolute top|start-*"
                              style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <!-- Modal -->
                    <div class="modal fade" id="confirm-generate-voucher"
                         data-bs-backdrop="static"
                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous pouvez
                                        Generer {{ $numGold }} bons de type GOLD</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{route('vouchers.post')}}" onsubmit="return true;">
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

                                        <div class="row mb-3">
                                            <label for="level"
                                                   class="col-md-4 col-form-label text-md-end">{{ 'Niveau du bon' }}</label>

                                            <div class="col-md-6">
                                                <select id="level"
                                                        class="form-control form-select form-select-lg @error('level') is-invalid @enderror"
                                                        name="level">
                                                    <option value="">Choisissez ici</option>
                                                    <option value="GOLD">GOLD</option>
                                                    <option value="PREMIUM">PREMIUM</option>
                                                    <option value="CLASSIC">CLASSIC</option>
                                                </select>

                                                @error('level')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="montant"
                                                   class="col-md-4 col-form-label text-md-end">{{ 'Montant'}}</label>

                                            <div class="col-md-6">
                                                <input id="montant" type="range"
                                                       class="@error('montant') is-invalid @enderror" name="montant"
                                                       value="{{env('GOLD_THRESHOLD')}}" required autocomplete="montant"
                                                       autofocus
                                                       min="{{env('GOLD_THRESHOLD')}}"
                                                       max="{{$loyaltyAccount->amount_from_converted_point}}"
                                                       onchange="document.getElementById('montantg').innerHTML = this.value"><small
                                                    id="montantg">{{env('GOLD_THRESHOLD')}}</small>

                                                @error('montant')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <input type="hidden" name="clientid" value="{{$client->id}}">
                                        <input type="hidden" name="">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler
                                        </button>
                                        <button type="submit" class="btn btn-success">Generer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @endif

                @if($isPremium)
                    <a class="list-group-item list-group-item-action" href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type PREMIUM' }}
                        <span class="badge bg-primary position-absolute top|start-*"
                              style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <a class="list-group-item list-group-item-action" href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type CLASSIC' }}
                        <span class="badge bg-primary position-absolute top|start-*"
                              style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <!-- Modal -->
                    <div class="modal fade" id="confirm-generate-voucher"
                         data-bs-backdrop="static"
                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous pouvez Generer 1 bons de
                                        type PREMIUM</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('vouchers.post') }}" onsubmit="return true;">
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

                                        <div class="row mb-3">
                                            <label for="level"
                                                   class="col-md-4 col-form-label text-md-end">{{ 'Niveau du bon' }}</label>

                                            <div class="col-md-6">
                                                <select id="level"
                                                        class="form-control form-select form-select-lg @error('level') is-invalid @enderror"
                                                        name="level">
                                                    <option value="">Choisissez ici</option>
                                                    <option value="PREMIUM">PREMIUM</option>
                                                    <option value="CLASSIC">CLASSIC</option>
                                                </select>

                                                @error('level')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="montant"
                                                   class="col-md-4 col-form-label text-md-end">{{ 'Montant'}}</label>

                                            <div class="col-md-6">
                                                <input id="montant" type="range"
                                                       class="@error('montant') is-invalid @enderror" name="montant"
                                                       value="{{env('PREMIUM_THRESHOLD')}}" required
                                                       autocomplete="montant" autofocus
                                                       min="{{env('PREMIUM_THRESHOLD')}}"
                                                       max="{{$loyaltyAccount->amount_from_converted_point}}"
                                                       onchange="document.getElementById('montantp').innerHTML = this.value"><small
                                                    id="montantp">{{env('PREMIUM_THRESHOLD')}}</small>

                                                @error('montant')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <input type="hidden" name="clientid" value="{{$client->id}}">
                                        <input type="hidden" name="">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler
                                        </button>
                                        <button type="submit" class="btn btn-success">Generer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if($isClassic)
                    <a class="list-group-item list-group-item-action" href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type CLASSIC' }}
                        <span class="badge bg-primary position-absolute top|start-*"
                              style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <!-- Modal -->
                    <div class="modal fade" id="confirm-generate-voucher"
                         data-bs-backdrop="static"
                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous pouvez Generer 1 bons de
                                        type CLASSIC</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('vouchers.post') }}" onsubmit="return true;">
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

                                        <div class="row mb-3">
                                            <label for="level"
                                                   class="col-md-4 col-form-label text-md-end">{{ 'Niveau du bon' }}</label>

                                            <div class="col-md-6">
                                                <select id="level"
                                                        class="form-control form-select form-select-lg @error('level') is-invalid @enderror"
                                                        name="level">
                                                    <option value="CLASSIC">CLASSIC</option>
                                                </select>

                                                @error('level')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="row mb-3">
                                            <label for="montant"
                                                   class="col-md-4 col-form-label text-md-end">{{ 'Montant'}}</label>

                                            <div class="col-md-6">
                                                <input id="montant" type="range"
                                                       class="@error('montant') is-invalid @enderror" name="montant"
                                                       value="{{env('CLASSIC_THRESHOLD')}}" required
                                                       autocomplete="montant" autofocus
                                                       min="{{env('CLASSIC_THRESHOLD')}}"
                                                       max="{{$loyaltyAccount->amount_from_converted_point}}"
                                                       onchange="document.getElementById('montantc').innerHTML = this.value"><small
                                                    id="montantc">{{env('CLASSIC_THRESHOLD')}}</small>

                                                @error('montant')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>


                                        <input type="hidden" name="clientid" value="{{$client->id}}">
                                        <input type="hidden" name="">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler
                                        </button>
                                        <button type="submit" class="btn btn-success">Generer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif--}}

            </div>

        </div>
    </div>
</div>
