@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ 'Enregistrer un Achat' }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                            <form method="POST" action="{{ route('purchases.index.post') }}">
                                @csrf
                                <div><h5>Les champs marques par <b class="" style="color: red;">*</b> sont obligatoires</h5></div>
                                <br>

                                <input type="hidden" name="error" id="error" class="form-control @error('error') is-invalid @enderror">
                                @error('error')
                                <span class="invalid-feedback" role="alert" style="position: relative; width: 100%; text-align: center;">
                                        <strong>{{ $message }}</strong>
                                    </span> <br/>
                                @enderror

                                <div class="row mb-3">
                                    <label for="clientid" class="col-md-3 col-form-label text-md-end">{{ 'Client' }}
                                        <b class="" style="color: red;">*</b></label>

                                    <div class="col-md-9">

                                        <div class="input-group">
                                            <input list="clientids" id="clientid" name="clientid" class="form-control @error('clientid') is-invalid @enderror"
                                                   value="{{ old('clientid') }}" required autocomplete="clientid" autofocus />
                                            <datalist id="clientids" class="@error('clientid') is-invalid @enderror" name="clientid" >
                                                @foreach(\App\Models\Client::all() as $client)
                                                    <option value="{{$client->telephone}}" label="{{$client->name}}" data-value="{{$client->name}}">{{$client->name}}</option>
                                                @endforeach
                                            </datalist>
                                        </div>

                                        @error('clientid')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="amount" class="col-md-3 col-form-label text-md-end">{{ 'Montant De l\'achat' }}
                                        <b class="" style="color: red;">*</b>
                                    </label>

                                    <div class="col-md-9">
                                        <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required autocomplete="amount" autofocus>

                                        @error('amount')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="receiptnumber" class="col-md-3 col-form-label text-md-end">
                                        {{ 'Numero du recu' }}
                                    </label>

                                    <div class="col-md-9">
                                        <input id="receiptnumber" type="text" class="form-control @error('receiptnumber') is-invalid @enderror" name="receiptnumber" value="{{ old('receiptnumber') }}" autocomplete="receiptnumber">

                                        @error('receiptnumber')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="transactiontypeid" class="col-md-3 col-form-label text-md-end">{{ 'Type de transaction' }}
                                        <b class="" style="color: red;">*</b></label>

                                    <div class="col-md-9">
                                            {{--<input list="clientids" id="clientid" name="clientid" class="form-control @error('clientid') is-invalid @enderror"
                                                   value="{{ old('clientid') }}" required autocomplete="clientid" autofocus />--}}
                                        <select id="transactiontypeid" class="form-control @error('transactiontypeid') is-invalid @enderror" name="transactiontypeid"  required>
                                            <option value="">-- Choisissez ici --</option>
                                            @foreach(\App\Models\Transactiontype::all() as $transactiontype)
                                                <option value="{{$transactiontype->id}}" >{{$transactiontype->name}}</option>
                                            @endforeach
                                        </select>

                                        @error('transactiontypeid')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <input type="hidden" id="numitem" name="numitem" value="0">
                                    <label for="products" class="col-md-3 col-form-label text-md-end">{{ 'Produits'}}</label>
                                    <div class="col-md-9" id="products">

                                    </div>
                                    <div class="input-group-btn" style="text-align: right;">
                                        <button class="btn btn-link" type="button"  onclick="addProduct_fields();"> <span class="glyphicon glyphicon-plus" style="font-weight: bold;">Ajouter</span> </button>
                                    </div>

                                        <script type="text/javascript">
                                            addProduct_fields();
                                            function addProduct_fields() {
                                                var numItem = parseInt(document.getElementById('numitem').value);
                                                console.log("Num Item: " + numItem);
                                                var index = numItem
                                                var products = document.getElementById('products');
                                                var divtest = document.createElement("div");
                                                //divtest.setAttribute("class", "form-group removeclass"+room);
                                                //var rdiv = 'removeclass'+room;
                                                var rowid = "product" + index;

                                                divtest.innerHTML =
                                                    '<div class="row" id="product' + index + '" style="margin-bottom: 7px;">' +
                                                        '<div class="col-sm-3 nopadding">' +
                                                            '<div class="form-group">' +
                                                                '<input type="text" class="form-control" name="productname' + index + '" value="" placeholder="Nom du produit">' +
                                                            '</div>' +
                                                        '</div>' +
                                                        '<div class="col-sm-3 nopadding">' +
                                                            '<div class="form-group">' +
                                                                '<input type="number" class="form-control" name="unitprice' + index + '" value="" placeholder="Prix Unitaire">'+
                                                            '</div>' +
                                                        '</div>' +
                                                        '<div class="col-sm-3 nopadding">' +
                                                            '<div class="form-group">' +
                                                                '<input type="number" class="form-control" name="quantity' + index + '" value="" placeholder="Quantite" onblur="displayTotal();">' +
                                                           '</div>' +
                                                        '</div>' +
                                                        '<div class="col-sm-3 nopadding">' +
                                                            '<div class="form-group">' +
                                                                '<div class="input-group">' +
                                                                    '<input type="number" class="form-control"  name="total' + index + '" value="" placeholder="0" disabled >' +
                                                                    '<div class="input-group-btn">' +
                                                                        '<button class="btn btn-link" type="button"  name="' + rowid + '" onclick="removeProductLine(this.name);"> <span class="glyphicon glyphicon-plus" style="font-weight: bold; color: darkred;">-</span> </button>' +
                                                                    '</div>' +
                                                                '</div>' +
                                                            '</div>' +
                                                        '</div>' +
                                                    '</div>';
                                                    //'<div class="col-sm-3 nopadding"><div class="form-group"> <input type="text" class="form-control" id="productname" name="productname" value="" placeholder="Non du produit"></div></div><div class="col-sm-3 nopadding"><div class="form-group"> <input type="number" class="form-control" id="unitprice" name="unitprice" value="" placeholder="Prix unitaire"></div></div><div class="col-sm-3 nopadding"><div class="form-group"> <input type="number" class="form-control" id="quantity" name="quantity" value="" placeholder="Quantite"></div></div><div class="col-sm-3 nopadding"><div class="form-group"><div class="input-group"> <div class="input-group-btn"> <button class="btn btn-danger" type="button" onclick="remove_product_fields('+ room +');"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span> </button></div></div></div></div><div class="clear"></div>';

                                                products.appendChild(divtest)

                                                var newNumItem = numItem + 1;
                                                console.log("new Num Item: " + newNumItem);
                                                document.getElementById('numitem').setAttribute("value", "" + newNumItem);

                                            }

                                            function removeProductLine(product) {
                                                console.log(product);
                                                //product
                                                var indexStr = product.substring("product".length);
                                                console.log(indexStr);
                                                var index = parseInt(indexStr);

                                                //index = index - 1;
                                                var numItem = parseInt(document.getElementById('numitem').value);
                                                if(!(numItem - 1 === index)) {
                                                    index = numItem - 1;
                                                }

                                                console.log(index);
                                                document.getElementById(product).remove();

                                                document.getElementById('numitem').setAttribute("value", "" + index);
                                                console.log("new num item: " + document.getElementById('numitem').value);

                                               var  productElem = document.getElementById('products');
                                               var rows = productElem.getElementsByClassName('row');
                                                console.log("rows: " + rows.length);
                                                for (var i = 0; i < rows.length; i++) {
                                                    var inputs = rows[i].getElementsByTagName('input');
                                                    inputs[0].setAttribute("name", 'productname' + i);
                                                    inputs[1].setAttribute("name", 'unitprice' + i)
                                                    inputs[2].setAttribute("name", 'quantity' + i);
                                                    inputs[3].setAttribute("name", 'total' + i);
                                                    /*var unitprice = parseFloat(inputs[1].value);
                                                    var quantity = parseFloat(inputs[2].value);

                                                    inputs[3].setAttribute("value", unitprice * quantity);*/

                                                }
                                            }

                                            function displayTotal() {
                                                var  productElem = document.getElementById('products');
                                                var rows = productElem.getElementsByClassName('row');
                                                console.log("rows: " + rows.length);
                                                for (var i = 0; i < rows.length; i++) {
                                                    var inputs = rows[i].getElementsByTagName('input');

                                                    var unitprice = parseFloat(inputs[1].value);
                                                    var quantity = parseFloat(inputs[2].value);
                                                    if(!Number.isNaN(unitprice) && !Number.isNaN(quantity)) {
                                                        inputs[3].setAttribute("value", unitprice * quantity);
                                                    }
                                                }
                                            }
                                            /*function remove_product_fields(rid) {
                                                $('.removeclass'+rid).remove();
                                            }*/
                                        </script>

                                </div>

                                <div class="row mb-0">
                                    <div class="col-md-6 offset-md-3">
                                        <button type="submit" class="btn btn-primary">
                                            {{ 'Enregistrer' }}
                                        </button>
                                    </div>
                                </div>
                            </form>

                    </div>

                    <div class="card-footer">
                        {{'Footer'}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{--
--}}
