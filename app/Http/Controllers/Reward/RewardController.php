<?php

namespace App\Http\Controllers\Reward;

use App\Http\Controllers\Controller;
use App\Models\Config;
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

    public function indexRewardList(): View
    {
        return view('reward.reward-page-list', ['rewards' => Reward::all()]);
    }



    public function registerReward(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'nature'               => 'required|string|in:MATERIAL,FINANCIAL,SERVICE',
            'level'                => 'required|string|max:255',
            'value'                  => 'required|numeric|min:1',
           // 'is_applicable'                   => 'required|string|in:on,off'
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $config = Config::where('is_applicable', true)->first();
        if ($config == null) {
            session()->flash('error', 'Config not found');
            return back()->withErrors(['error' => 'Config not found']);
        }
        $levels = json_decode($config->levels, true);
        $theLevel = null;
        foreach ($levels as $level) {
            if (strtoupper($level['name']) === strtoupper($request->input('level'))) {
                $theLevel = $level;
                break;
            }
        }
        if ($theLevel === null) {
            session()->flash('error', 'Niveau de recompense inexistant');
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        Reward::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->get('name'),
            'nature' => $request->get('nature'),
            'value' => $request->get('value'),
            'level' =>json_encode($theLevel,JSON_UNESCAPED_UNICODE),
            'active' => true,
            'registered_by' => Auth::user()->id,
        ]);

        session()->flash('status', 'Recompense enregistree avec succes!');

        return back()->with('status', 'Recompense enregistree avec succes!');//->with('status', ['message' => 'Great! You have Successfully Registered the conversion.', 'conversion' => $conversion]);
    }

    public function activateOrDeactivateReward(Request $request, string $rewardId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Vous n\'etes pas autorise');
            return back()->withErrors(['error' => 'Vous n\'etes pas autorise']);
        }
        $validator = Validator::make($request->all(), [
            'user'               => 'required|numeric|min:1',
            'action'            => 'required|string|in:activate,deactivate',
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $reward = Reward::where('id', $rewardId)->first();
        if ($reward == null) {
            session()->flash('error', 'Reward not found');
            return back()->withErrors(['error' => 'Reward not found']);
        }

        $user = Auth::user();
        //dd(['user' => $user, 'userid' => $request->get('user'), 'reward' => $reward, 'action' => $request->get('action'), 'id' => $rewardId]);
        if (intval($request->get('user')) !== $user->id){
            session()->flash('error', 'Something went wrong');
            return back()->withErrors(['error' => 'Something went wrong']);
        }

        $active = true;
        if ($request->get('action') === 'deactivate'){
            $active = false;
        }else{
            if ($request->get('action') !== 'activate'){
                session()->flash('error', 'Something went wrong');
                return back()->withErrors(['error' => 'Something went wrong']);
            }
        }

        Reward::where('id', $rewardId)->update(['active' => $active]);

        $msg = 'Recompense ' . ($active ? 'activee' : 'desactivee') . ' avec succes!';
        session()->flash('status', $msg);

        return back()->with('status', $msg);//->with('status', ['message' => 'Great! You have Successfully Registered the conversion.', 'conversion' => $conversion]);
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
