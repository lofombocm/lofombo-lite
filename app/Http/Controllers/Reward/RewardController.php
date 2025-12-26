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
    public function __construct()
    {

    }

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
        ],[
            'name.required'=>__("Le nom est obligatoire."),
            'name.max'=>__("Le nom est trop long."),
            'nature.required'=>__("Le nature est obligatoire."),
            'nature.in'=>__("Les valeurs possibles sont: MATERIEL, FINANCIERE, SERVICE"),
            'value.required'=>__("La valeur est obligatoire."),
            'value.numeric'=>__("Seul les caractères numériques sont acceptés."),
            'level.required'=>__("Le type de bon est requis."),
            'level.max'=>__("Le type de bon trop long."),
        ]);
    }

    public function validateImage(Request $request): array
    {
        //dd($request);
        $pathImage = '';
        if ($request->file('image') !== null){

            $imgValidator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:500000|dimensions:min_width=10,max_width=7000,min_height=10,max_height=7000',
            ],
                [
                    'image.required'=> __("Le logo est obligatoire"),
                    'image.image' => __("Le logo doit être une image"),
                    'image.mimes' => __("Les formats acceptés: jpeg, png, jpg, gif, svg"),
                    'image.dimensions'=>__("Taille maximale du fichier : 500kb. Largeur minimal: 10px, largeur maximal: 7000px. Hauteur minimal: 10px, hauteur maximal: 7000px"),
                ]);

            //dd($imgValidator->errors());
            if ($imgValidator->fails()){
                session()->flash('error', $imgValidator->errors()->first());
                return ['success'=>false, 'message'=> $imgValidator->errors()->first()];
                //return back()->withErrors(['error' => $imgValidator->errors()->first()]);
            }

            $pathImage = $request->file('image')->store('images/rewards', 'public');
        }
        //dd("Not entered");
        return ['success'=>true, 'message'=> $pathImage];
    }


    public function registerReward(Request $request)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Vous n\'etes pas autorise');
            return back()->withErrors(['error' => 'Vous n\'etes pas autorise']);
        }
        $validator = $this->rewardValidator($request);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        //dd(request()->all());
        $result = $this->validateImage($request);
        if (!$result['success']){
            session()->flash('error', $result['message']);
            return back()->withErrors(['error' => $result['message']]);
        }
        $pathImage = $result['message'];
        /*if ($request->file('image') !== null){
            $imgValidator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:500000|dimensions:min_width=25,max_width=700,min_height=25,max_height=700',
            ],
                [
                    'image.required'=> __("Le logo est obligatoire"),
                    'image.image' => __("Le logo doit être une image"),
                    'image.mimes' => __("Les formats acceptés: jpeg, png, jpg, gif, svg"),
                    'image.dimensions'=>__("Taille maximale du fichier : 500kb. Largeur minimal: 25px, largeur maximal: 700px. Hauteur minimal: 25px, hauteur maximal: 700px"),
                ]);

            if ($imgValidator->fails()){
                session()->flash('error', $imgValidator->errors()->first());
                return back()->withErrors(['error' => $imgValidator->errors()->first()]);
            }

            $pathImage = $request->file('image')->store('images/rewards', 'public');
        }*/

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
            session()->flash('error', __("Type de bon inconnu."));
            return back()->withErrors(['error' => __("Type de bon inconnu.")]);
        }

        Reward::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->get('name'),
            'nature' => $request->get('nature'),
            'value' => $request->get('value'),
            'level' =>json_encode($theLevel,JSON_UNESCAPED_UNICODE),
            'active' => true,
            'registered_by' => Auth::user()->id,
            'image'=>$pathImage
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

        $result = $this->validateImage($request);
        if (!$result['success']){
            //session()->flash('error', $result['message']);
            //return back()->withErrors(['error' => $result['message']]);
            return
                response()->json(
                    [
                        'error' => 1,
                        'success'=>0,
                        'errorMessage' => $result['message'],
                        'successMessage' =>'',
                        'result' => $result['message']
                    ], Response::HTTP_OK);
        }
        $pathImage = $result['message'];
        $reward = Reward::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->get('name'),
            'nature' => $request->get('nature'),
            'value' => $request->get('value'),
            'level' =>json_encode($theLevel,JSON_UNESCAPED_UNICODE),
            'active' => true,
            'registered_by' => intval($request->get('userid')),
            'image'=>$pathImage
        ]);

        return
            response()->json(
                [
                    'error' => 0,
                    'success'=>1,
                    'errorMessage' => '',
                    'successMessage' =>__('Récompense enregistée avec succès!'),
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

    public function activateOrDeactivateReward(Request $request, string $local, string $rewardId)
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

    public function deleteReward(Request $request, string $locale, string $rewardid)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Vous n\'etes pas autorise');
            return back()->withErrors(['error' => 'Vous n\'etes pas autorise']);
        }

        $reward = Reward::where('id', $rewardid)->first();
        if ($reward == null) {
            session()->flash('error', __("Récompense non reconnue."));
        }

        $reward->delete();
        $msg = __("Récompense supprimée  avec succès");
        session()->flash('status', $msg);

        return back()->with('status', $msg);

    }
}
