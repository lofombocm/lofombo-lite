@php
    use App\Models\Config;
    use App\Models\Notification;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Auth;
@endphp
@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @include('layouts.menu')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5>{{ 'Atteindre nos clients en un clique'}}</h5></div>

                    <div class="card-body">
                        {{--<div class="modal-body" style="height: 80vh; overflow-y: auto;">--}}
                        <form action="{{route('send-bulk-message.admin.post')}}" method="POST" onsubmit="return true;"
                            enctype="multipart/form-data">
                            <input type="hidden" name="error" id="error"
                                   class="form-control @error('error') is-invalid @enderror">
                            @error('error')
                            <span class="invalid-feedback" role="alert"
                                  style="position: relative; width: 100%; text-align: center;">
                                                                    <strong>{{ $message }}</strong>
                                                                </span> <br/>
                            @enderror

                            @csrf

                            @if (session('error'))
                                <div class="alert alert-danger" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="chanel" class="form-label">{{'Choisir le canal'}}</label>
                                <div class="row" id="chanel">
                                   <div class="col col-md-6">
                                       <div class="form-check form-switch">
                                           <input type="hidden" name="smschanel" id="smschanel" value="off">
                                           <input style="height: 17px; width: 17px;" class="" type="checkbox" id="SMS" value="off" name="sms_chanel" onclick="toggleSmsValue(this);">
                                           <label class="form-check-label" for="SMS">Canal SMS</label>
                                       </div>
                                   </div>
                                    <div class="col col-md-6">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="emailchanel" id="emailchanel" value="off">
                                            <input style="height: 17px; width: 17px;"  class="" type="checkbox" id="email" value="off" name="email_chanel" onclick="toggleEmailValue(this);">
                                            <label class="form-check-label" for="email">Canal E-Mail</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Objet</label>
                                <input class="form-control" id="subject"  placeholder="Objet de la campagne" name="subject">
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" rows="10" placeholder="Redigez votre message ici!" name="message"></textarea>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-5">
                                    <button type="submit" class="btn btn-info">
                                        <b>{{ 'Envoyer' }}</b> <img src="{{asset('images/icons8-sent-25.png')}}" alt="{{'envoyer'}}">
                                    </button>
                                </div>
                            </div>

                            <script type="text/javascript">
                                function toggleSmsValue(checkbox){

                                    if(checkbox.checked){
                                        //checkbox.checked = !checkbox.checked;
                                        document.getElementById('smschanel').setAttribute('value','on');
                                    }else{
                                        //checkbox.checked = !checkbox.checked;
                                        document.getElementById('smschanel').setAttribute('value','off');
                                    }
                                }

                                function toggleEmailValue(checkbox){
                                    if(checkbox.checked){
                                        //checkbox.checked = !checkbox.checked;
                                        document.getElementById('emailchanel').setAttribute('value','on');
                                    }else{
                                        //checkbox.checked = !checkbox.checked;
                                        document.getElementById('emailchanel').setAttribute('value','off');
                                    }
                                }
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
