@php
    use App\Models\ConversionAmountPoint;
    use App\Models\Reward;
    use App\Models\Threshold;
    use App\Models\Transactiontype;
    use App\Models\Voucher;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Loyaltytransaction;

@endphp
@extends('layouts.app-client')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.client-menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">

                        @include('reward.list')

                    </div>
                    <div class="card-footer">
                        {{' '}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
