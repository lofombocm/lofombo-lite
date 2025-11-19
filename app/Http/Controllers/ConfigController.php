<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showConfigForm()
    {
        return view('config.index');
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function setSystemConfiguration(Request $request): RedirectResponse
    {
        //
        //session()->flash('error', $request->get('amount_per_point'));
        //session()->flash('request', json_encode($request->all()));
        //return back()->withErrors(['error' => $request->get('amount_per_point')]);
        $numLevel = intval($request->get("index"));
        if ($numLevel < 1) {
            session()->flash('error', 'Il est necessaire de definir les niveaux de bon.');
            //session()->flash('request', json_encode($request->all()));
            return back()->withErrors(['error' => 'Il est necessaire de definir les niveaux de bon']);
        }

        $validatorRule1 = [
            'initial_loyalty_points' => 'required|numeric|min:0',
            'amount_per_point' => 'required|numeric|min:1',
            'currency_name' => 'required|string|min:2|max:255',
            'birthdate_bonus_rate' => 'required|numeric|min:1',
        ];

        $levelValidatorRule = [];
        for ($i = 0; $i < $numLevel; $i++) {
            $levelValidatorRule = array_merge($levelValidatorRule, [
                'level_name'.$i =>'required|string|min:2|max:255',
                'level_point'.$i => 'required|numeric|min:1'
            ]);
        }

        $validatorRule2 = [
            'voucher_duration_in_month' => 'required|numeric|min:1',
            'password_recovery_request_duration' => 'required|numeric|min:1',
            'enterprise_name' => 'required|string|min:2|max:255',
            'enterprise_email' => 'required|string|email|max:255',
            'enterprise_phone' => 'required|string|min:2|max:255',
            'enterprise_website' => 'required|string|url',
            'enterprise_address' => 'required|string|min:2|max:255',
            //'enterprise_logo' => 'required|file|mimes:jpeg,jpg,png',
        ];
        $validatorRules = array_merge(array_merge($validatorRule1, $levelValidatorRule), $validatorRule2);
        $validator = Validator::make($request->all(), $validatorRules);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $numHour = intval($request->get('password_recovery_request_duration'));
        $numMinute = $numHour * 60;

        $configId = Str::uuid()->toString();

        $levels = [];
        for ($i = 0; $i < $numLevel; $i++) {
            $levelid = Str::uuid()->toString();
            array_push($levels,
                ['id' => $levelid, 'config' => $configId, 'name' => strtoupper($request->get('level_name'.$i)),
                    'point' => intval($request->get('level_point'.$i))]);
        }

        $path = '';
        if ($request->file('enterprise_logo') != null){
            $logoRule = [
                'enterprise_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240|dimensions:min_width=25,max_width=500,min_height=25,max_height=500',
            ];

            $logoRules = array_merge($validatorRules, $logoRule);
            //dd($logoRules);
            $logoValidator = Validator::make($request->all(), $logoRules);
            //dd($logoValidator->fails());
            if($logoValidator->fails()){
                session()->flash('error', $logoValidator->errors()->first());
                //dd(session('error'));
                return back()->withErrors(['error' => $logoValidator->errors()->first()]);
            }

            $path = $request->file('enterprise_logo')->store('images', 'public');
        }

        DB::beginTransaction();

        try {

            if (strlen($path) === 0) {
                $configs = Config::where('is_applicable', true)->get();
                if (count($configs) === 1) {
                    $path = $configs[0]->enterprise_logo;
                }
            }
            //dd(json_encode($levels));
            Config::where('is_applicable', true)->update(['is_applicable' => false]);
            $data = [
                'id' => $configId,
                'initial_loyalty_points' => doubleval($request->get('initial_loyalty_points')),
                'amount_per_point'=> doubleval($request->get('amount_per_point')),
                'currency_name' => $request->get('currency_name'),
                'levels' => json_encode($levels),
                /*'classic_threshold' => $request->get('classic_threshold'),
                'premium_threshold' => $request->get('premium_threshold'),
                'gold_threshold' => $request->get('gold_threshold'),*/
                'voucher_duration_in_month' => intval($request->get('voucher_duration_in_month')),
                'password_recovery_request_duration' => $numMinute,
                'enterprise_name' => $request->get('enterprise_name'),
                'enterprise_email' => $request->get('enterprise_email'),
                'enterprise_phone' => $request->get('enterprise_phone'),
                'enterprise_website' => $request->get('enterprise_website'),
                'enterprise_address' => $request->get('enterprise_address'),
                'enterprise_logo' => $path,
                'is_applicable' => true,
                'defined_by' => Auth::user()->id,
                'birthdate_bonus_rate' => doubleval($request->get('birthdate_bonus_rate')),
            ];
            //dd($data);
            Config::create($data);

        }catch (\Exception $exception){
            DB::rollBack();
            session()->flash('error', $exception->getMessage());
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        DB::commit();

        //Auth::login($user);
        $msg = 'Bien! la configuration a ete enregistree avec succes.';
        session()->flash('status', $msg);
        session()->flash('logo', $path);
        return back()->with('status', $msg);//->withSuccess('status', 'Great! You have Successfully Registered.');
    }
}
