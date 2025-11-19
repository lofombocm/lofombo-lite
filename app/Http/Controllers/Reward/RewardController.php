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
use Symfony\Component\HttpFoundation\Response;

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

    public function rewardValidator(Request $request): \Illuminate\Validation\Validator
    {
        return Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'nature'               => 'required|string|in:MATERIAL,FINANCIAL,SERVICE',
            'level'                => 'required|string|max:255',
            'value'                  => 'required|numeric|min:1',
        ]);
    }


    public function registerReward(Request $request)
    {
        $validator = $this->rewardValidator($request);

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

    public function registerRewardApi(Request $request)
    {

        $validator = $this->rewardValidator($request);

        if($validator->fails()){
            return
                response()->json(
                    [
                        'error' => 1,
                        'success'=>0,
                        'errorMessage' => $validator->errors()->first(),
                        'successMessage' =>'',
                        'result' => $validator->errors()
                    ], Response::HTTP_OK);
        }
        $config = Config::where('is_applicable', true)->first();

        if ($config == null) {
            return
                response()->json(
                    [
                        'error' => 1,
                        'success'=>0,
                        'errorMessage' => 'Config not found',
                        'successMessage' =>'',
                        'result' => 'Config not found'
                    ], Response::HTTP_OK);
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
            return
                response()->json(
                    [
                        'error' => 1,
                        'success'=>0,
                        'errorMessage' => 'Niveau de recompense inexistant',
                        'successMessage' =>'',
                        'result' => 'Niveau de recompense inexistant'
                    ], Response::HTTP_OK);
            //session()->flash('error', 'Niveau de recompense inexistant');
            //return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $reward = Reward::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->get('name'),
            'nature' => $request->get('nature'),
            'value' => $request->get('value'),
            'level' =>json_encode($theLevel,JSON_UNESCAPED_UNICODE),
            'active' => true,
            'registered_by' => intval($request->get('userid')),
        ]);

        return
            response()->json(
                [
                    'error' => 0,
                    'success'=>1,
                    'errorMessage' => '',
                    'successMessage' =>'Recompense creee avec succes!',
                    'result' => $reward
                ], Response::HTTP_OK);

        //session()->flash('status', 'Recompense enregistree avec succes!');

        //return back()->with('status', 'Recompense enregistree avec succes!');//->with('status', ['message' => 'Great! You have Successfully Registered the conversion.', 'conversion' => $conversion]);
    }

    // Testing API
    public function test(Request $request)
    {
        return
            response()->json(
                [
                    'error' => 0,
                    'success'=>1,
                    'errorMessage' => '',
                    'successMessage' =>'Test OK!',
                    'result' => $request->get('userid')
                ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param string $rewardId
     * @return \Illuminate\Http\RedirectResponse
     */

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
