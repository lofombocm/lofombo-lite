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

                            <span  id="produits" style="display: none; height: 0; width: 0;">{{json_encode(\App\Models\Product::all())}}</span>
                            <form method="POST"
                                  action="{{ route('purchases.index.post') }}"
                                  enctype="multipart/form-data" onsubmit="return onSubmitPurchse();">
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
                                    <label for="client_id" class="col-md-3 col-form-label text-md-end">{{ 'Client' }}
                                        <b class="" style="color: red;">*</b></label>

                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <input id="clientid" type="hidden"  name="clientid" value="">
                                            <input list="clientids" id="client_id" name="client_id" class="form-control @error('client_id') is-invalid @enderror"
                                                   value="{{ old('client_id') }}" required autocomplete="client_id" autofocus
                                                   onchange="setClientId(this.value);"/>
                                            <datalist id="clientids" class="@error('clientids') is-invalid @enderror" >
                                                @foreach(\App\Models\Client::where('active', true)->get() as $client)
                                                    <option value="{{$client->name}} (Tel: {{$client->telephone}})" label="" data-value="{{$client->name}}">{{$client->telephone}}</option>
                                                @endforeach
                                            </datalist>
                                        </div>

                                        @error('client_id')
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
                                        <b class="" style="color: red;">*</b>
                                    </label>

                                    <div class="col-md-9">
                                        <input id="receiptnumber" type="text" class="form-control @error('receiptnumber') is-invalid @enderror" name="receiptnumber" value="{{ old('receiptnumber') }}" required autocomplete="receiptnumber">

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
                                            function setClientId(nameAndTel){
                                                //var regExp = /([A-Z]|[a-z]|[0-9])+(\s([A-Z]|[a-z]|[0-9])+)*(\s+\(Tel\:\s[0-9]{9,15}\))/g;
                                                var regExp2 = /[0-9]{9,15}/g;
                                                var clientid = document.getElementById("clientid");
                                                //var test = "Nguetsop Ngoufack Edwige Laure (Tel: 691179154)";
                                                var matches = nameAndTel.match(regExp2);
                                                if(matches === null){
                                                    console.log("No Clientid");
                                                }else{
                                                    console.log(matches);
                                                    console.log(matches[0]);
                                                    clientid.setAttribute('value', matches[0]);
                                                }

                                            }

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
                                                                '<input type="text" class="form-control" name="productname' + index + '" value="" placeholder="Nom du produit" onblur="filterProducts(this);">' +
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

                                            function filterProducts(inputNamei){
                                                var index = inputNamei.name.substring("productname".length);
                                                var inputPriceId = "unitprice" + index;
                                                var inputPrice = document.getElementById(inputPriceId);
                                                var productsJsonString = document.getElementById('produits').innerHTML;
                                                var produits =  JSON.parse(productsJsonString);
                                                var nomProduit = inputNamei.value;
                                                console.log(produits);
                                                console.log(inputPriceId);
                                                console.log(nomProduit);
                                                for(var i = 0; i < produits.length; i++){
                                                    if(nomProduit.toUpperCase() === produits[i].name){
                                                        console.log(produits[i].price);
                                                        inputPrice.setAttribute("value", produits[i].price);
                                                        break;
                                                    }
                                                }
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

                                        <a {{--type="submit"--}} class="btn btn-primary" href="#" onclick="loadModal();"
                                           data-bs-toggle="modal"
                                           data-bs-target="#confirm-register-purchase-modal">
                                            {{ 'Enregistrer' }}
                                        </a>

                                        {{--<button id="open-confirm-purchase-modal" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#confirm-register-purchase-modal"
                                                style="display: none;">
                                            {{ 'Enregistrer' }}
                                        </button>--}}

                                        <div class="modal fade" id="confirm-register-purchase-modal" data-bs-backdrop="static"
                                             data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                                            {{'Confirmez-vous l\'enregistrement de l\'achat?'}}
                                                            {{--<strong
                                                                style="color: darkred;">{{$client->name}}</strong>--}}
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                   {{-- <form method="POST" action="{{url('/client/' . $client->id . '/activate')}}"
                                                          onsubmit="return true;">--}}
                                                        <div class="modal-body">
                                                            <div class="list-group list-group-flush alert alert-info"
                                                                  id="form-list-group">
                                                                <a href="#" class="list-group-item list-group-item-action"
                                                                   style="margin-left: 15px; width: 98%;" id="name-displayer">
                                                                    {{--<h5>
                                                                        Telephone: &nbsp; &nbsp; {{$client->telephone}}
                                                                    </h5>--}}
                                                                </a>
                                                                <a href="#" class="list-group-item list-group-item-action"
                                                                   style="margin-left: 15px; width: 98%;" id="telephone-displayer">

                                                                </a>
                                                                <a href="#" class="list-group-item list-group-item-action"
                                                                   style="margin-left: 15px; width: 98%;" id="amount-displayer">

                                                                </a>
                                                                <a href="#" class="list-group-item list-group-item-action"
                                                                   style="margin-left: 15px; width: 98%;" id="receiptnumber-displayer">

                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger"
                                                                    data-bs-dismiss="modal">Annuler
                                                            </button>
                                                            <button type="submit" class="btn btn-success">
                                                                {{'Confirmer l\'achat'}}
                                                            </button>
                                                        </div>
                                                    {{--</form>--}}
                                                </div>
                                            </div>
                                        </div>
                                        <script type="text/javascript">
                                            function openConfirmModal(){
                                               var modalOpenner = document.getElementById('open-confirm-purchase-modal');
                                               var client = document.getElementById('clientid');
                                               var amount = document.getElementById('amount');
                                               var transactiontype = document.getElementById('transactiontype');
                                               var receiptnumber = document.getElementById('receiptnumber');
                                               if(
                                                   client.value.length > 0 &&
                                                   amount.value.length > 0 &&
                                                   transactiontype.value.length > 0 &&
                                                   receiptnumber.value.length > 0) {
                                                   modalOpenner.click();
                                               }else{
                                                   alert('Merci de completer le formulaire.');
                                               }
                                            }

                                            function loadModal(){
                                                //clientids
                                                var clientid = document.getElementById('clientid');
                                                var client_id = document.getElementById('client_id');
                                                var telephone = clientid.value;
                                                var nameTel = client_id.value;
                                                var datalist = document.getElementById('clientids');
                                                var datalistOptions = datalist.options;
                                                var selectedOption = null;

                                                for(var i = 0; i < datalistOptions.length; i++){
                                                    //console.log(datalistOptions[i]);
                                                    var regExp2 = /[0-9]{9,15}/g;
                                                    var matches = datalistOptions[i].value.match(regExp2);
                                                    //if(matches !== null){
                                                    console.log(datalistOptions[i].value + ' ?= ' + nameTel);
                                                    if(datalistOptions[i].value === nameTel){
                                                        selectedOption = datalistOptions[i];
                                                    }
                                                    //}
                                                }
                                                var name = '';
                                                if(selectedOption == null){
                                                    name = '';
                                                }else{
                                                    name = selectedOption.getAttribute('data-value');
                                                }

                                                var amount = document.getElementById('amount').value;
                                                var receiptnumber = document.getElementById('receiptnumber').value;
                                                document.getElementById('name-displayer').innerHTML =
                                                    '<h5>Client: ' + name + '</h5>';
                                                document.getElementById('telephone-displayer').innerHTML =
                                                    '<h5>Telephone: ' + telephone + '</h5>';
                                                document.getElementById('amount-displayer').innerHTML =
                                                    '<h5>Montant: ' + amount + '</h5>';
                                                document.getElementById('receiptnumber-displayer').innerHTML =
                                                    '<h5>No. Recu: ' + receiptnumber + '</h5>';

                                                /*console.log('datalistOptions.length: ' + datalistOptions.length);
                                                console.log('Name: ' + name + ', Telephone: ' + telephone + ', Amount: ' + amount
                                                 + ', Receiptnumber: ' + receiptnumber);*/
                                            }
                                        </script>
                                    </div>
                                </div>


                            </form>

                    </div>

                    {{--<div class="card-footer">
                        {{' '}}
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection


{{--
--}}
