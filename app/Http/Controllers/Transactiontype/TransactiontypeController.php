<?php

namespace App\Http\Controllers\Transactiontype;

use App\Http\Controllers\Controller;
use App\Models\Conversion;
use App\Models\Transactiontype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TransactiontypeController extends Controller
{
    public function transactiontypesView(): View
    {
        return view('transactiontype.index');
    }

    //

    public function createTransactiontypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255|min:2|unique:transactiontypes',
            'description'    => 'required|string|max:10000|min:2',
            'signe'    => 'required|numeric|int:-1,1',
        ]);

        if($validator->fails()){
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        /*$fp = fopen('is_applicable', 'w+');
        fwrite($fp, $request->get('is_applicable'));
        fclose($fp);*/

        $id = Str::uuid()->toString();
        $code = $id;
        $transactiontype = Transactiontype::create([
            'id' => $id,
            'code' => $code,
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'signe' => intval($request->get('signe')),
            'active' => true,
        ]);

        session()->flash('status', 'Type de transaction enregistree avec succes!');

        return redirect("/home");//->with('status', ['message' => 'Great! You have Successfully Registered the conversion.', 'conversion' => $conversion]);
    }

}
