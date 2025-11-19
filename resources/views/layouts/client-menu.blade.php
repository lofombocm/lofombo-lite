@php
    use App\Models\Config
  ; use App\Models\Loyaltyaccount
  ; use App\Models\Reward
  ; use Illuminate\Support\Facades\Auth
  ;
@endphp
<div class="col-md-3">
    <div class="card">
        <div class="card-header"><h5>{{ 'Menu' }}</h5></div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                @if(Auth::guard('client')->check() && Auth::guard('client')->user()->active)
                    @php
                        $configuration = Config::where('is_applicable', true)->first();
                        $levels = json_decode($configuration->levels);
                        $client = Auth::guard('client')->user();
                        $loyaltyAccount = Loyaltyaccount::where('holderid', $client->id)->first();
                        $maxLevel = $levels[0];
                        $minLevel = $levels[0];
                        foreach ($levels as $level){
                            if($level->point > $maxLevel->point && $loyaltyAccount->point_balance >= $level->point){
                                $maxLevel = $level;
                            }
                            if($level->point < $minLevel->point){
                                $minLevel = $level;
                            }
                        }

                        $possibleLevels = [];
                        foreach ($levels as $level){
                            if ($level->point <= $maxLevel->point && $level->point >= $minLevel->point){
                                array_push($possibleLevels, $level);
                            }
                        }

                    @endphp

                    @if(Auth::guard('client')->check())
                        <a class="list-group-item list-group-item-action" href="{{ route('home.client') }}">
                            <h6><img src="{{asset('images/icons8-dashboard-25.png')}}" alt=""> &nbsp;{{ 'Tableau de bord' }}</h6>
                        </a>

                        <a class="list-group-item list-group-item-action btn btn-link"  href="{{route('home.loyaltytransactions.client.search.all', Auth::guard('client')->user()->id)}}"
                           id="lien-pour-transaction-enregistres">
                            <h6><img src="{{asset('images/icons8-transaction-25.png')}}" alt=""> &nbsp;{{ 'Transactions' }}</h6>
                        </a>
                        <a class="list-group-item list-group-item-action" href="{{ route('rewards.list.view') }}">
                            <h6><img src="{{asset('images/icons8-reward-25.png')}}" alt=""> &nbsp;{{ 'Recompenses' }}</h6>
                        </a>
                    @endif

                    @if($loyaltyAccount->point_balance >= $minLevel->point)
                        <a class="list-group-item list-group-item-action" href="#"
                           data-bs-toggle="modal" data-bs-target="#generate-voucher-modal">
                            <h6><img src="{{asset('images/icons8-loyalty-card-25.png')}}" alt=""> &nbsp;{{ 'Generer un bon' }}
                            <span class="badge bg-primary position-absolute top|start-*"
                                  style="position: relative; right: 0; padding-top: 7px;">{{$loyaltyAccount->point_balance}}</span></h6>
                        </a>

                        <!-- Modal -->
                        <div class="modal fade" id="generate-voucher-modal" data-bs-backdrop="static"
                             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                             aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                <div class="modal-content">
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
                                            <ol>
                                                @foreach($possibleLevels as $level)
                                                    <li>
                                                        <h6>
                                                            Lorsque vous accumuler <strong> {{$level->point}}
                                                                points</strong>
                                                            vous beneficiez d'un bon de type
                                                            <strong
                                                                style="color: #495057;">{{$level->name}}</strong>
                                                        </h6>
                                                        @php
                                                            $rewards = Reward::all();
                                                            $selectedRewards = [];
                                                            foreach ($rewards as $reward){
                                                                $niveau = json_decode($reward->level);
                                                                if ($niveau->name === $level->name && $niveau->point === $level->point){
                                                                    array_push($selectedRewards, $reward);
                                                                }
                                                            }
                                                        @endphp
                                                        @if(count($selectedRewards) > 0)
                                                            <h6>
                                                                Vous pouvez beneficier de :
                                                            </h6>
                                                            <ul>
                                                                @foreach($selectedRewards as $theReward)
                                                                    <li>
                                                                        {{$theReward->name}}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    </div>

                                    {{--@if(!($bestReward === null))--}}

                                    {{--<div class="alert alert-light" style="margin-top: -20px;">
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
                                    </div>--}}
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

                                            @if(count($possibleLevels) >= 1)
                                                <div class="row mb-3">
                                                    {{--<input type="hidden" name="transactiontypeid"
                                                           value="{{$transactiontype->id}}">--}}
                                                    <label for="level"
                                                           class="col-md-4 col-form-label text-md-end">{{ 'Niveau du bon' }}</label>
                                                    <div class="col-md-6">
                                                        <select id="level"
                                                                class="form-control form-select form-select-lg @error('level') is-invalid @enderror"
                                                                name="level">
                                                            <option value="">Choisissez ici</option>
                                                            @foreach($possibleLevels as $level)
                                                                <option
                                                                    value="{{json_encode($level)}}">{{$level->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('level')
                                                            <span class="invalid-feedback" role="alert">
                                                                 <strong>{{ $message }}</strong>
                                                             </span>
                                                        @enderror
                                                    </div>

                                                    <input type="hidden" name="clientid" id="clientid"
                                                           value="{{$client->id}}">
                                                    <input id="transactiontype" name="transactiontype"
                                                           value="GENERATION DE BON" type="hidden"/>
                                                    {{--<input type="hidden" name="rewardid" id="rewardid"
                                                           value="{{$bestReward->id}}">
                                                    <input type="hidden" name="conversionpointrewardid"
                                                           id="conversion_point_reward"
                                                           value="{{$conversionUsed->id}}">
                                                    <input type="hidden" name="thresholdid"
                                                           value="{{$threshold->id}}">
                                                    <input type="hidden" name="level" value="{{$type}}">--}}
                                                </div>
                                            @else
                                                <input type="hidden" name="level" id="level"
                                                       value="{{json_encode($possibleLevels[0])}}">
                                                <input type="hidden" name="clientid" id="clientid"
                                                       value="{{$client->id}}">
                                                <input id="transactiontype" name="transactiontype"
                                                       value="GENERATION DE BON" type="hidden"/>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                    data-bs-dismiss="modal">Annuler
                                            </button>
                                            <button type="submit" class="btn btn-success">Generer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div>
                        <div class="alert alert-danger" role="alert">{{'Utilsateur Desactive'}}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
