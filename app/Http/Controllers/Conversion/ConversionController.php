<?php

namespace App\Http\Controllers\Conversion;

use App\Http\Controllers\Controller;
use App\Models\Conversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConversionController extends Controller
{
    public function index(): View
    {
        return view('conversion.index');
    }

    public function conversionList(): View{
        return view('conversion.list');
    }

    public function setConversonToUse(Request $request){
        $validator = Validator::make($request->all(), [
            'conversionid' => 'required|string|exists:conversions,id',
        ]);

        Conversion::where('is_applicable', true)->update(['is_applicable' =>false]);
        Conversion::where('id', $request->get('conversionid'))->update(['is_applicable' =>true]);

        session()->flash('status', 'Convertion definie avec succes!');

        return redirect('/home');

    }
    public function registerConversion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount_to_point_amount' => 'required|numeric|min:1',
            'amount_to_point_point' => 'required|numeric|min:1',
            'point_to_amount_point' => 'required|numeric|min:1',
            'point_to_amount_amount' => 'required|numeric|min:1',
            'birthdate_rate' => 'required|numeric|min:1',
        ]);

        if($validator->fails()){
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        Conversion::where('is_applicable', true)->update(['is_applicable' => false]);

        $conversion = Conversion::create([
            'id' => Str::uuid()->toString(),
            'amount_to_point_amount' => $request->get('amount_to_point_amount'),
            'amount_to_point_point' => $request->get('amount_to_point_point'),
            'point_to_amount_point' => $request->get('point_to_amount_point'),
            'point_to_amount_amount' => $request->get('point_to_amount_amount'),
            'birthdate_rate' => $request->get('birthdate_rate'),
            'active' => true,
            'is_applicable' => false,
            'defined_by' => Auth::user()->id,
        ]);

        session()->flash('status', 'Convertion enregistree avec succes!');

        return redirect("/home");//->with('status', ['message' => 'Great! You have Successfully Registered the conversion.', 'conversion' => $conversion]);
    }


}
