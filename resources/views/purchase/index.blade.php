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

                            <form method="POST" action="{{ route('purchases.index.post') }}" enctype="multipart/form-data" onsubmit="return onSubmitPurchse();">
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

                                {{--<div class="row mb-3" >
                                    <label  class="col-md-3 col-form-label text-md-end">
                                        <a href="#" onclick="toggleProductContent();" style="text-decoration: none; font-size: initial; color: green;" id="add_level_field">
                                            <span class="glyphicon glyphicon-plus"><strong>+</strong></span> <span style="font-size: initial;"> produits de l'achat</span>
                                        </a>
                                    </label>

                                    <div class="col-md-9">
                                        <input id="receiptnumber" type="text" class="form-control @error('receiptnumber') is-invalid @enderror" name="receiptnumber" value="{{ old('receiptnumber') }}" autocomplete="receiptnumber">

                                    </div>
                                </div>--}}

                                <input  id="transactiontype" name="transactiontype" value="ENREGISTREMENT ACHAT" type="hidden"/>
                                <input id="montant" type="hidden" name="montant" id="montant">

                                <div class="row mb-3" id="product-content" {{--style="display: none;"--}}>
                                    <input type="hidden" id="numitem" name="numitem" value="0">
                                    <label for="products" class="col-md-3 col-form-label text-md-end">
                                        {{ 'Produits'}}
                                        <br>
                                        <a href="#" onclick="addProduct_fields();" style="text-decoration: none; font-size: initial; color: green;" id="add_level_field">
                                            <strong><span class="glyphicon glyphicon-plus">+</span></strong>
                                        </a>
                                    </label>
                                    <div class="col-md-9" >
                                        <div id="products">

                                        </div>
                                        <br>
                                        <strong>
                                            <span id="general_total" style="display: inline; float: right; margin-right: 30px; font-size: initial">
                                                Total: 0
                                            </span>
                                        </strong>
                                    </div>

                                </div>
                                    {{--<div class="input-group-btn" style="text-align: right;">
                                        <button class="btn btn-link" type="button"  > <span class="glyphicon glyphicon-plus" style="font-size: large;">Ajouter</span> </button>
                                    </div>--}}

                                        <script type="text/javascript">
                                            //addProduct_fields();
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
                                                                '<input id="unitprice' +  index + '" type="number" class="form-control" name="unitprice' + index + '" value="" placeholder="Prix Unitaire" onblur="displayTotal();">'+
                                                            '</div>' +
                                                        '</div>' +
                                                        '<div class="col-sm-3 nopadding">' +
                                                            '<div class="form-group">' +
                                                                '<input id="' +  index + '" type="number" class="form-control" name="quantity' + index + '" value="" placeholder="Quantite" onblur="displayTotal();">' +
                                                           '</div>' +
                                                        '</div>' +
                                                        '<div class="col-sm-3 nopadding">' +
                                                            '<div class="form-group">' +
                                                                '<div class="input-group" >' +
                                                                    '<input type="number" class="form-control col-sm-9"  name="total' + index + '" value="" placeholder="0" disabled > &nbsp;' +
                                                                    '<div class="input-group-btn col-sm-3">' +
                                                                        '<a href="#"  title="' + rowid + '" onclick="removeProductLine(this.title);" style="text-decoration: none; font-size: x-large; color: red;"> <span class="glyphicon glyphicon-plus">-</span> </a>' +
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
                                                var general_total = document.getElementById('general_total');
                                                var generaltotal = 0;
                                                for (var i = 0; i < rows.length; i++) {
                                                    var inputs = rows[i].getElementsByTagName('input');
                                                    inputs[0].setAttribute("name", 'productname' + i);
                                                    inputs[1].setAttribute("name", 'unitprice' + i);
                                                    inputs[2].setAttribute("name", 'quantity' + i);
                                                    inputs[3].setAttribute("name", 'total' + i);
                                                    var unitprice = parseFloat(inputs[1].value);
                                                    var quantity = parseFloat(inputs[2].value);
                                                    if(!Number.isNaN(unitprice) && !Number.isNaN(quantity)) {
                                                        inputs[3].setAttribute("value", unitprice * quantity);
                                                        generaltotal += unitprice * quantity;
                                                    }
                                                }
                                                general_total.innerHTML = "<i id='total_general' title='" +  generaltotal + "'>Total: " + generaltotal + "</i>";
                                                var montant = document.getElementById('montant');
                                                montant.setAttribute("value", "" + generaltotal);
                                            }

                                            function displayTotal() {
                                                var  productElem = document.getElementById('products');
                                                //if(productElem !== null){
                                                var rows = productElem.getElementsByClassName('row');
                                                console.log("rows: " + rows.length);
                                                var general_total = document.getElementById('general_total');
                                                var generaltotal = 0;
                                                for (var i = 0; i < rows.length; i++) {
                                                    var inputs = rows[i].getElementsByTagName('input');

                                                    var unitprice = parseFloat(inputs[1].value);
                                                    var quantity = parseFloat(inputs[2].value);
                                                    if(!Number.isNaN(unitprice) && !Number.isNaN(quantity)) {
                                                        inputs[3].setAttribute("value", unitprice * quantity);
                                                        generaltotal += unitprice * quantity;
                                                    }
                                                }
                                                general_total.innerHTML = "<i id='total_general' title='" +  generaltotal + "'>Total: " + generaltotal + "</i>";
                                                var montant = document.getElementById('montant');
                                                montant.setAttribute("value", "" + generaltotal);

                                            }

                                            function onSubmitPurchse(){
                                                var amount = parseInt(document.getElementById('amount').value);
                                                var montant = parseInt(document.getElementById('montant').value);
                                                var numItem = parseInt(document.getElementById('numitem').value);
                                                if(amount !== montant && numItem > 0){
                                                    alert("Le montant des achats est different de la somme des montant des differents produits");
                                                    return false;
                                                }
                                                return true;
                                            }

                                            function toggleProductContent() {
                                                var productContainer = document.getElementById('product-content');
                                                if (productContainer.checkVisibility()) {
                                                    productContainer.style.display = 'none';
                                                }else{
                                                    productContainer.style.display = 'block';
                                                }
                                            }
                                            /*function remove_product_fields(rid) {
                                                $('.removeclass'+rid).remove();
                                            }*/
                                        </script>



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
                        {{' '}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{--
--}}
