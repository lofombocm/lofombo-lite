<?php

namespace App\Http\Controllers\Reward;

use App\Http\Controllers\Controller;
use App\Models\ConversionPointReward;
use App\Models\Reward;
use App\Models\Threshold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RewardController extends Controller
{
    public function indexReward(): View
    {
        return view('reward.index');
    }


    public function registerReward(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'nature'               => 'required|string|in:MATERIAL,FINANCIAL',
            'value'                  => 'required|numeric|min:1',
           // 'is_applicable'                   => 'required|string|in:on,off'
        ]);

        if($validator->fails()){
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        Reward::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->get('name'),
            'nature' => $request->get('nature'),
            'value' => $request->get('value'),
            'active' => true,
            'registered_by' => Auth::user()->id,
        ]);

        session()->flash('status', 'Recompense enregistree avec succes!');

        return redirect("/home");//->with('status', ['message' => 'Great! You have Successfully Registered the conversion.', 'conversion' => $conversion]);
    }
    public static function getBestRewards(int $point){

        $bestReward = null;

        $conversionPointRewards = ConversionPointReward::where('is_applicable', true)->get();

        if (count($conversionPointRewards) === 0){
            return null;
        }

        $conversionUsed = $conversionPointRewards[0];
        foreach ($conversionPointRewards as $conversionPointReward){

            $rewardid = $conversionPointReward->reward;

            $reward = Reward::where('id', $rewardid)->where('active', true)->first();

            if ($conversionPointReward->min_point <= $point){

                if ($bestReward === null){
                    $bestReward = $reward;
                    $conversionUsed = $conversionPointReward;
                }else{
                    if ($bestReward->value < $reward->value){
                        $bestReward = $reward;
                        $conversionUsed = $conversionPointReward;
                    }
                }
            }
        }

        if ($bestReward === null){
            return null;
        }
        return ['bestreward' => $bestReward, 'conversionused' => $conversionUsed];
    }
}
