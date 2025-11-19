@extends('layouts.email-template')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>{{ $data['subject'] }} <br></h3>
                    </div>

                    <div class="card-body">
                        <p>
                            {{$data['message']}}

                            <a href="{{url('')}}" class="btn btn-primary" >{{'Cliquez ici pour acceder a notre site web.'}}</a>
                        </p>

                        <h5>Merci pour votre fidelite.</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


