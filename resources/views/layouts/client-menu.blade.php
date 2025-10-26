<div class="col-md-3">
    <div class="card">
        <div class="card-header">{{ 'Menu' }}</div>
        <div class="card-body" >
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action"  href="{{ route('vouchers.index')}}">
                    {{ 'Bons d\'Achats' }}
                </a>
            {{--</div>

            <div class="list-group list-group-flush">--}}
                <?php
                    $client = \Illuminate\Support\Facades\Auth::guard('client')->user();
                    $loyaltyAccount = \App\Models\Loyaltyaccount::where('holderid', $client->id)->first();
                    //echo $client->name;
                    $point = $loyaltyAccount->point_balance;
                    $isgold = $point >= intval(env('GOLD_THRESHOLD'));
                    $isPremium = $point >= intval(env('PREMIUM_THRESHOLD')) && $point < intval(env('GOLD_THRESHOLD'));
                    $isClassic = $point >= intval(env('CLASSIC_THRESHOLD')) && $point < intval(env('PREMIUM_THRESHOLD'));
                ?>
                @if($isgold)
                    <?php
                        $numGold = intdiv($point, intval(env('GOLD_THRESHOLD')));
                    ?>
                    <a class="list-group-item list-group-item-action"  href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type GOLD' }}
                        <span class="badge bg-primary position-absolute top|start-*" style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <a class="list-group-item list-group-item-action"  href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type PREMIUM' }}
                        <span class="badge bg-primary position-absolute top|start-*" style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <a class="list-group-item list-group-item-action"  href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type CLASSIC' }}
                        <span class="badge bg-primary position-absolute top|start-*" style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <!-- Modal -->
                    <div class="modal fade" id="confirm-generate-voucher"
                         data-bs-backdrop="static"
                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous pouvez Generer {{ $numGold }} bons de type GOLD</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{route('vouchers.post')}}" onsubmit="return true;">
                                    <div class="modal-body">

                                        <input type="hidden" name="error" id="error" class="form-control @error('error') is-invalid @enderror">
                                        @error('error')
                                            <span class="invalid-feedback" role="alert" style="position: relative; width: 100%; text-align: center;">
                                                <strong>{{ $message }}</strong>
                                            </span> <br/>
                                        @enderror
                                        @csrf

                                        <div class="row mb-3">
                                            <label for="level" class="col-md-4 col-form-label text-md-end">{{ 'Niveau du bon' }}</label>

                                            <div class="col-md-6">
                                                <select id="level" class="form-control form-select form-select-lg @error('level') is-invalid @enderror" name="level" >
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
                                        </div>

                                        <input type="hidden" name="clientid" value="{{$client->id}}">
                                        <input type="hidden" name="">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-success">Generer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @endif

                @if($isPremium)
                    <a class="list-group-item list-group-item-action"  href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type PREMIUM' }}
                        <span class="badge bg-primary position-absolute top|start-*" style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <a class="list-group-item list-group-item-action"  href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type CLASSIC' }}
                        <span class="badge bg-primary position-absolute top|start-*" style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <!-- Modal -->
                    <div class="modal fade" id="confirm-generate-voucher"
                         data-bs-backdrop="static"
                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous pouvez Generer 1 bons de type PREMIUM</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('vouchers.post') }}" onsubmit="return true;">
                                    <div class="modal-body">

                                        <input type="hidden" name="error" id="error" class="form-control @error('error') is-invalid @enderror">
                                        @error('error')
                                        <span class="invalid-feedback" role="alert" style="position: relative; width: 100%; text-align: center;">
                                                <strong>{{ $message }}</strong>
                                            </span> <br/>
                                        @enderror
                                        @csrf

                                        <div class="row mb-3">
                                            <label for="level" class="col-md-4 col-form-label text-md-end">{{ 'Niveau du bon' }}</label>

                                            <div class="col-md-6">
                                                <select id="level" class="form-control form-select form-select-lg @error('level') is-invalid @enderror" name="level" >
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
                                            <label for="montant" class="col-md-4 col-form-label text-md-end">{{ 'Montant'}}</label>

                                            <div class="col-md-6">
                                                <input id="montant" type="range" class="@error('montant') is-invalid @enderror" name="montant"
                                                       value="{{env('PREMIUM_THRESHOLD')}}" required autocomplete="montant" autofocus
                                                       min="{{env('PREMIUM_THRESHOLD')}}" max="{{$loyaltyAccount->amount_from_converted_point}}"
                                                       onchange="document.getElementById('montantp').innerHTML = this.value" ><small id="montantp">{{env('PREMIUM_THRESHOLD')}}</small>

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
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-success">Generer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if($isClassic)
                    <a class="list-group-item list-group-item-action"  href="#"
                       data-bs-toggle="modal" data-bs-target="#confirm-generate-voucher">
                        {{ 'Generer bon d\'achat. de type CLASSIC' }}
                        <span class="badge bg-primary position-absolute top|start-*" style="position: relative; right: 0;">{{$point}}</span>
                    </a>

                    <!-- Modal -->
                    <div class="modal fade" id="confirm-generate-voucher"
                         data-bs-backdrop="static"
                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Vous pouvez Generer 1 bons de type CLASSIC</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('vouchers.post') }}" onsubmit="return true;">
                                    <div class="modal-body">

                                        <input type="hidden" name="error" id="error" class="form-control @error('error') is-invalid @enderror">
                                        @error('error')
                                        <span class="invalid-feedback" role="alert" style="position: relative; width: 100%; text-align: center;">
                                                <strong>{{ $message }}</strong>
                                            </span> <br/>
                                        @enderror
                                        @csrf

                                        <div class="row mb-3">
                                            <label for="level" class="col-md-4 col-form-label text-md-end">{{ 'Niveau du bon' }}</label>

                                            <div class="col-md-6">
                                                <select id="level" class="form-control form-select form-select-lg @error('level') is-invalid @enderror" name="level">
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
                                            <label for="montant" class="col-md-4 col-form-label text-md-end">{{ 'Montant'}}</label>

                                            <div class="col-md-6">
                                                <input id="montant" type="range" class="@error('montant') is-invalid @enderror" name="montant"
                                                       value="{{env('CLASSIC_THRESHOLD')}}" required autocomplete="montant" autofocus
                                                       min="{{env('CLASSIC_THRESHOLD')}}" max="{{$loyaltyAccount->amount_from_converted_point}}"
                                                       onchange="document.getElementById('montantc').innerHTML = this.value" ><small id="montantc">{{env('CLASSIC_THRESHOLD')}}</small>

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
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-success">Generer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>
</div>
