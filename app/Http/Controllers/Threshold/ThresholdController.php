<?php

namespace App\Http\Controllers\Threshold;

use App\Http\Controllers\Controller;
use App\Models\ConversionAmountPoint;
use App\Models\Threshold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ThresholdController extends Controller
{

    public function indexThreshold(): View
    {
        return view('threshold.index');
    }


    public function registerThreshold(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'classic_threshold'               => 'required|numeric|min:1',
            'premium_threshold'               => 'required|numeric|min:1',
            'gold_threshold'                  => 'required|numeric|min:1',
            'is_applicable'                   => 'required|string|in:on,off'
        ]);

        if($validator->fails()){
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }


        DB::beginTransaction();

        if ($request->get('is_applicable') == 'on'){
            Threshold::where('is_applicable', true)->update(['is_applicable' => false]);
        }

        /*$fp = fopen('is_applicable', 'w+');
        fwrite($fp, $request->get('is_applicable'));
        fclose($fp);*/

        Threshold::create([
            'id' => Str::uuid()->toString(),
            'classic_threshold' => $request->get('classic_threshold'),
            'premium_threshold' => $request->get('premium_threshold'),
            'gold_threshold' => $request->get('gold_threshold'),
            'active' => true,
            'is_applicable' => ($request->get('is_applicable') == 'on')?true:false,
            'defined_by' => Auth::user()->id,
        ]);

        DB::commit();

        session()->flash('status', 'Seuils enregistree avec succes!');

        return redirect("/home");//->with('status', ['message' => 'Great! You have Successfully Registered the conversion.', 'conversion' => $conversion]);
    }

}
