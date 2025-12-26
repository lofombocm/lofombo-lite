@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('layouts.menu')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header"><h5>{{ __('Enregistrer un Achat') }}</h5></div>
                    <div class="card-body">

                            <span  id="produits" style="display: none; height: 0; width: 0;">{{json_encode(\App\Models\Product::all())}}</span>
                            <form method="POST"
                                  action="{{ route('purchases.index.post') }}"
                                  enctype="multipart/form-data" onsubmit="return onSubmitPurchse();" id="purchase_form">
                                @csrf
                                <div><h5>{{__('Les champs marqués par ')}} <b class="" style="color: red;">*</b> {{__('sont obligatoires')}}</h5></div>
                                <br>
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                {{--@if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif--}}
                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                {{--<input type="hidden" name="error" id="error" class="form-control @error('error') is-invalid @enderror">
                                @error('error')
                                <span class="invalid-feedback" role="alert" style="position: relative; width: 100%; text-align: center;">
                                        <strong>{{ $message }}</strong>
                                    </span> <br/>
                                @enderror--}}

                                <div class="row mb-3">
                                    <label for="client_id" class="col-md-3 col-form-label text-md-end">{{ __("Client") }}
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
                                    <label for="amount" class="col-md-3 col-form-label text-md-end">{{ __('Montant') }}
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
                                        {{ __("Numéro Ticket / Reçu") }}
                                        <b class="" style="color: red;">*</b>
                                    </label>

                                    <div class="col-md-9">

                                        <input id="receiptnumber" type="hidden"  name="receiptnumber" value="">
                                        <input list="receiptnumbers" id="receipt_number" name="receipt_number" class="form-control @error('receipt_number') is-invalid @enderror"
                                               value="{{ old('receipt_number') }}" required autocomplete="receipt_number" autofocus
                                               onchange="setReceiptNumber(this.value);"/>

                                        <datalist id="receiptnumbers" class="@error('receiptnumbers') is-invalid @enderror" >
                                            @foreach(\App\Models\Purchase::all() as $purchase)
                                                <option value="{{$purchase->receiptnumber}}" label="" data-value="{{$purchase->receiptnumber}}">{{$purchase->receiptnumber}}</option>
                                            @endforeach
                                        </datalist>

                                        {{--<input id="receiptnumber" type="text" class="form-control @error('receiptnumber') is-invalid @enderror" name="receiptnumber" value="{{ old('receiptnumber') }}" required autocomplete="receiptnumber">--}}

                                        @error('receipt_number')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        @if(session('purchase'))
                                            <small style="color: red; font-size: x-small;">
                                                {{__("Créé le")}}: {{\Illuminate\Support\Carbon::parse(session('purchase')->created_at)->format('d-m-Y H:i:s')}}
                                                <?php
                                                    $clientReceipt = \App\Models\Client::where('id', session('purchase')->clientid)->first();
                                                ?>
                                                {{__("au nom du client")}}: {{$clientReceipt->name}}, {{__("Montant")}}: {{session('purchase')->amount}}
                                                (ID: {{session('purchase')->id}})
                                            </small>
                                        @endif
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
                                <input id="montant" type="hidden" name="montant" >

                                <div class="row mb-3" id="product-content" {{--style="display: none;"--}}>
                                    <input type="hidden" id="numitem" name="numitem" value="0">
                                    <label for="products" class="col-md-3 col-form-label text-md-end">
                                        {{ __("Articles")}}
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

                                <span style="display: none;" id="label_unit_price">{{__("Prix Unitaire (TTC)")}}</span>
                                <span style="display: none;" id="label_quantity">{{__("QTE")}}</span>
                                <span style="display: none;" id="label_item_name">{{__("Nom Article")}}</span>

                                <div id="product_datalist" style="display: none;">
                                    <datalist id="productnames"  >
                                        @foreach(\App\Models\Product::all() as $product)
                                            <option value="{{$product->name}}" label="" data-value="{{$product->id}}">{{$product->name}}</option>
                                        @endforeach
                                    </datalist>
                                </div>

                                {{--<div class="input-group-btn" style="text-align: right;">
                                    <button class="btn btn-link" type="button"  > <span class="glyphicon glyphicon-plus" style="font-size: large;">Ajouter</span> </button>
                                </div>--}}

                                        <script type="text/javascript">
                                            function setClientId(nameAndTel){
                                                //var regExp = /([A-Z]|[a-z]|[0-9])+(\s([A-Z]|[a-z]|[0-9])+)*(\s+\(Tel\:\s[0-9]{9,15}\))/g;
                                                var regExp2 = /\+[0-9]{8,20}/g;
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

                                            /*function resetForm(){
                                                document.getElementById("client_id").setAttribute('value', '');
                                            }
                                            resetForm();*/

                                            //setClientId(document.getElementById("client_id").value);

                                            function setReceiptNumber(receiptNumberValue){
                                                var receiptnumber = document.getElementById("receiptnumber");
                                                receiptnumber.setAttribute('value', receiptNumberValue);
                                            }
                                            //setReceiptNumber(document.getElementById("receipt_number").value);

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
                                                                '<input type="hidden" name="productname' + index + '" value="" id="productname' + index + '" />'+
                                                                '<input list="productnames" class="form-control" id="product_name' + index + '" name="product_name' + index + '" value="" placeholder="' + document.getElementById("label_item_name").innerHTML + '" onblur="filterProducts(this);">' +
                                                                document.getElementById("product_datalist").innerHTML +
                                                            '</div>' +
                                                        '</div>' +
                                                        '<div class="col-sm-3 nopadding">' +
                                                            '<div class="form-group">' +
                                                                '<input id="unitprice' +  index + '" type="number" class="form-control" name="unitprice' + index + '" value="" placeholder="'  +  document.getElementById("label_unit_price").innerHTML + '" onblur="displayTotal();">'+
                                                            '</div>' +
                                                        '</div>' +
                                                        '<div class="col-sm-3 nopadding">' +
                                                            '<div class="form-group">' +
                                                                '<input id="' +  index + '" type="number" class="form-control" name="quantity' + index + '" value="" placeholder="' + document.getElementById("label_quantity").innerHTML + '" onblur="displayTotal();">' +
                                                           '</div>' +
                                                        '</div>' +
                                                        '<div class="col-sm-3 nopadding">' +
                                                            '<div class="form-group">' +
                                                                '<div class="input-group" >' +
                                                                    '<input type="number" class="form-control col-sm-9" id="total' + index + '" name="total' + index + '" value="" placeholder="0" disabled > &nbsp;' +
                                                                    '<div class="input-group-btn col-sm-3">' +
                                                                        '<a href="#"  title="' + index + '" onclick="removeProductLine(this.title);" style="text-decoration: none; font-size: inherit; color: red; padding-top: 5px;"> <span class="glyphicon glyphicon-plus" style="">&nbsp;&nbsp;<img src="{{asset('images/icons8-remove-24.png')}}" alt="" style="margin-top: 7px;"/></span> </a>' +
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
                                                //onchange="setReceiptNumber(this.value);"
                                                var index = inputNamei.name.substring("product_name".length);
                                                //console.log("INDEX: " + index);
                                                var productnameid = "productname" + index;
                                                //console.log("productnameid: " + productnameid);
                                                var inputProductName = document.getElementById(productnameid);
                                                var pname = inputNamei.value;
                                                //console.log('pname: ' + pname);
                                                inputProductName.setAttribute('value', pname);

                                                var inputPriceId = "unitprice" + index;
                                                var inputPrice = document.getElementById(inputPriceId);
                                                var productsJsonString = document.getElementById('produits').innerHTML;
                                                var produits =  JSON.parse(productsJsonString);
                                                var nomProduit = inputNamei.value;
                                                //console.log(produits);
                                                //console.log(inputPriceId);
                                                //console.log(nomProduit);
                                                for(var i = 0; i < produits.length; i++){
                                                    if(nomProduit.toUpperCase() === produits[i].name.toUpperCase()){
                                                        console.log(produits[i].price);
                                                        inputPrice.setAttribute("value", produits[i].price);
                                                        /*var event = new Event('change');
                                                        element.dispatchEvent(event);*/
                                                        break;
                                                    }
                                                }
                                            }

                                            function removeProductLine(indexStr) {
                                                //console.log(product);
                                                //var indexStr = product.substring("product".length);
                                                console.log(indexStr);
                                                var index = parseInt(indexStr);

                                                //index = index - 1;
                                                var numItem = parseInt(document.getElementById('numitem').value);
                                                /*if(!(numItem - 1 === index)) {
                                                    index = numItem - 1;
                                                }*/

                                                //console.log(index);
                                                document.getElementById("product" + indexStr).remove();
                                                var newNumItem = numItem - 1;

                                                document.getElementById('numitem').setAttribute("value", "" + newNumItem);
                                                console.log("new num item: " + document.getElementById('numitem').value);


                                               var  productElem = document.getElementById('products');
                                               var rows = productElem.getElementsByClassName('row');
                                                console.log("rows: " + rows.length);
                                                var general_total = document.getElementById('general_total');
                                                var generaltotal = 0;
                                                for (var i = 0; i < rows.length; i++) {
                                                    var idxStr = rows[i].id.substring("product".length)
                                                    var indice = parseInt(idxStr);
                                                    console.log("indice: " + indice);
                                                    console.log("(" + i + ", " + index + ")");
                                                    var inputs = rows[i].getElementsByTagName('input');
                                                    if(i < index){
                                                        console.log("(" + inputs[0].value + ", " + inputs[1].value + ", " + inputs[2].value
                                                            + ", " + inputs[3].value + ", " + inputs[4].value + ", " + i + ")");
                                                        var unitprice1 = parseFloat(inputs[2].value);
                                                        var quantity1 = parseFloat(inputs[3].value);
                                                        if(!Number.isNaN(unitprice1) && !Number.isNaN(quantity1)) {
                                                            inputs[4].setAttribute("value", unitprice1 * quantity1);
                                                            generaltotal += unitprice1 * quantity1;
                                                        }
                                                    }else{

                                                        inputs[0].setAttribute("name", 'productname' + i);
                                                        inputs[0].setAttribute("id", 'productname' + i);
                                                        inputs[1].setAttribute("name", 'product_name' + i);
                                                        inputs[1].setAttribute("id", 'product_name' + i);
                                                        inputs[2].setAttribute("name", 'unitprice' + i);
                                                        inputs[2].setAttribute("id", 'unitprice' + i);
                                                        inputs[3].setAttribute("name", 'quantity' + i);
                                                        inputs[3].setAttribute("id", '' + i);
                                                        inputs[4].setAttribute("name", 'total' + i);
                                                        inputs[4].setAttribute("id", 'total' + i);
                                                        console.log("(" + inputs[0].value + ", " + inputs[1].value + ", " + inputs[2].value
                                                            + ", " + inputs[3].value + ", " + inputs[4].value + ", " + i + ")");
                                                        var unitprice = parseFloat(inputs[2].value);
                                                        var quantity = parseFloat(inputs[3].value);
                                                        if(!Number.isNaN(unitprice) && !Number.isNaN(quantity)) {
                                                            inputs[4].setAttribute("value", unitprice * quantity);
                                                            generaltotal += unitprice * quantity;
                                                        }
                                                        var as = rows[i].getElementsByTagName('a');
                                                        as[0].setAttribute('title', "" + i);
                                                        rows[i].setAttribute('id', "product" + i);
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

                                                    var unitprice = parseFloat(inputs[2].value);
                                                    var quantity = parseFloat(inputs[3].value);
                                                    if(!Number.isNaN(unitprice) && !Number.isNaN(quantity)) {
                                                        inputs[4].setAttribute("value", unitprice * quantity);
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
                                            {{ __('Enregistrer') }}
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
                                                            {{ __("Confirmez les informations") }}
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
                                                                {{__('Confirmer l\'achat')}}
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
                                                var clientid = document.getElementById('clientid');
                                                var receiptnumber = document.getElementById('receiptnumber');

                                                var client_id = document.getElementById('client_id');
                                                var receipt_number = document.getElementById('receipt_number');

                                                var telephone = clientid.value;
                                                var nameTel = client_id.value;

                                                var numRec = receipt_number.value;

                                                var datalist = document.getElementById('clientids');
                                                var datalistOptions = datalist.options;
                                                var selectedOption = null;

                                                var datalistNumRecu = document.getElementById('receiptnumbers');
                                                var datalistOptionsNumRecu = datalistNumRecu.options;
                                                var selectedOptionNumRecu = null;

                                                for(var i = 0; i < datalistOptions.length; i++){
                                                    //console.log(datalistOptions[i]);
                                                    var regExp2 = /\+[0-9]{8,20}/g;
                                                    var matches = datalistOptions[i].value.match(regExp2);
                                                    //if(matches !== null){
                                                    console.log(datalistOptions[i].value + ' ?= ' + nameTel);
                                                    if(datalistOptions[i].value === nameTel){
                                                        selectedOption = datalistOptions[i];
                                                    }
                                                    //}
                                                }

                                                for(var j = 0; j < datalistOptionsNumRecu.length; j++){

                                                    if(datalistOptionsNumRecu[j].value === numRec){
                                                        selectedOptionNumRecu = datalistOptionsNumRecu[j];
                                                    }
                                                    //}
                                                }

                                                if(selectedOption === null){
                                                    console.log("Selected option is null");
                                                }else{

                                                    clientid.setAttribute('value', selectedOption.innerHTML);
                                                    telephone = clientid.getAttribute('value');

                                                    console.log("Selected option: " + selectedOption.getAttribute('data-value'));
                                                    console.log("Selected option.html: " + selectedOption.innerHTML);
                                                    console.log("Selected option.value: " + selectedOption.getAttribute('value'));

                                                }
                                                var name = '';
                                                if(selectedOption == null){
                                                    name = '';
                                                }else{
                                                    name = selectedOption.getAttribute('data-value');
                                                }

                                                if(selectedOptionNumRecu === null){
                                                    console.log("Selected option selectedOptionNumRecu is null");
                                                }else{
                                                    receiptnumber.setAttribute('value', selectedOptionNumRecu.getAttribute('value'));
                                                }

                                                var amount = document.getElementById('amount').value;
                                                var receiptnumberVal = document.getElementById('receiptnumber').value;
                                                document.getElementById('name-displayer').innerHTML =
                                                    '<h5>Client: ' + name + '</h5>';
                                                document.getElementById('telephone-displayer').innerHTML =
                                                    '<h5>{{__("Téléphone")}}: ' + telephone + '</h5>';
                                                document.getElementById('amount-displayer').innerHTML =
                                                    '<h5>{{__("Montant")}}: ' + amount + '</h5>';
                                                document.getElementById('receiptnumber-displayer').innerHTML =
                                                    '<h5>{{__("Numéro Ticket / Reçu")}}: ' + receiptnumberVal + '</h5>';

                                                /*console.log('datalistOptions.length: ' + datalistOptions.length);
                                                console.log('Name: ' + name + ', Telephone: ' + telephone + ', Amount: ' + amount
                                                 + ', Receiptnumber: ' + receiptnumber);*/
                                            }
                                        </script>
                                    </div>
                                </div>

                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
