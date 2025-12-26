<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FriendInvitatin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FriendInvitationController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public static function checkInvitation(string $clientid, string $invitationid)
    {
        $client = Client::where('id', $clientid)->first();
        if($client == null){
            session()->flash("error", __("Client inconnu"));
            return back()->withErrors(['error' => __("Client inconnu")]);
        }

        $invitation = FriendInvitatin::where('id', $invitationid)->first();
        if($invitation == null){
            session()->flash("error", __("Invitation inconnu"));
            return back()->withErrors(['error' => __("Invitation inconnu")]);
        }

        if ($invitation->inviter_id !== $clientid){
            session()->flash('error', __("Une erreur est survenue"));
            return back()->withErrors(['error' => __("Une erreur est survenue")]);
        }
        return [$client, $invitation];

    }
    public function getFriendInvitationAcceptationForm(string $locale, string $clientid, string $invitationid){
        $retVal = self::checkInvitation($clientid, $invitationid);
        $client = $retVal[0];
        $invitation = $retVal[1];
        //dd($client, $invitation);
        return view('client.invitation.accept', ['inviter' => $client, 'invitation' => $invitation, 'clientid' => $client->id, 'invitationid' => $invitation->id]);
    }


    public function postFriendInvitationAcceptationForm(Request $request, string $locale, string $clientid, string $invitationid){

        $retVal = self::checkInvitation($clientid, $invitationid);
        $client = $retVal[0];
        $invitation = $retVal[1];

        if ($invitation->state !== 'PENDING'){
            if ($invitation->state === 'ACCEPTED'){
                session()->flash('error', __("Vous avez déjà rejoint."));
                return back()->withErrors(['error' => __("Vous avez déjà rejoint.")]);
            }
            session()->flash('error', __("Une erreur est survenue"));
            return back()->withErrors(['error' => __("Une erreur est survenue")]);
        }

        if ($client->id !== $invitation->inviter_id){
            session()->flash('error', __("Une erreur est survenue"));
            return back()->withErrors(['error' => __("Une erreur est survenue")]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'telephone' => 'required|phone|unique:clients',
        ],[
            'telephone.required' => __('Le numéro de téléphone est obligatoire'),
            'name.required' => __('Le nom est obligatoire.'),
            'telephone.unique'=> __('Téléphone déja utilisé.'),
            'telephone.phone' => __("Le numéro de téléphone est invalide"),
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return redirect()->back()->withErrors(['error' => $validator->errors()->first()]);
        }


        $secret = null;
        $birthdate = "";
        //dd($request);
        if ($request->filled('day') && $request->filled('month')) {
            $year = 1900;
            $validatorBirthdate = Validator::make($request->all(), [
                'day' => 'required|string|in:01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31',
                'month' => 'required|string|in:01,02,03,04,05,06,07,08,09,10,11,12',
                //'year' => 'integer|between:1900,'.date('Y'),
            ],[
                'day.required' => __('Jour obligatoire.'),
                'day.in' => __("Jour invalide"),
                'month.required' => __('Mois obligatoire.'),
                'month.in' => __("Mois invalide"),
            ]);
            if($validatorBirthdate->fails()){
                session()->flash('error', $validatorBirthdate->errors()->first());
                return redirect()->back()->withErrors(['error' => $validatorBirthdate->errors()->first()]);
            }

            if (intval(strval($request->get('month'))) == 2 && intval(strval($request->get('day'))) > 29) {
                //if($validatorBirthdate->fails()){
                session()->flash('error', __('Date invalide'));
                return redirect()->back()->withErrors(['error' => __('Date invalide')]);
                //}
            }

            if (!$request->filled('year')){
                $year = 1900;
            }else{
                $year = intval(trim($request->get('year')));
                if (!($year >= 1900 && $year <= Carbon::now()->year)){
                    $year = 1900;
                }
            }
            $birthdate = $year . '-'.trim($request->get('month')).'-'.trim($request->get('day'));
            $birthdateFormatedArr = explode('-', $birthdate);
            //$secret = $birthdateFormatedArr[2] . $birthdateFormatedArr[1] . $birthdateFormatedArr[0];
            //dd($birthdate);
        }/*else{
            $secret = "12345678";
        }*/

        if ($request->filled('gender')){
            $validatorGender = Validator::make($request->all(), [
                'gender' => 'required|string|in:M,F,',
            ],[
                'gender.required' => __('Le sexe est obligatoire.'),
                'gender.in' => __("Le sexe est invalide."),
            ]);
            if($validatorGender->fails()){
                session()->flash('error', $validatorGender->errors()->first());
                return redirect()->back()->withErrors(['error' => $validatorGender->errors()->first()]);
            }
        }

        if ($request->filled('quarter')){
            $validatorQuarter = Validator::make($request->all(), [
                'quarter' => 'string|max:255',
            ], [
                'quarter.string' => __('Lieu de résidence invalide'),
            ]);
            if($validatorQuarter->fails()){
                session()->flash('error', $validatorQuarter->errors()->first());
                return redirect()->back()->withErrors(['error' => $validatorQuarter->errors()->first()]);
            }
        }


        if ($request->filled('city')){
            $validatorCity = Validator::make($request->all(), [
                'city' => 'string|max:255',
            ],[
                'city.string' => __('Ville invalide'),
            ]);
            if($validatorCity->fails()){
                session()->flash('error', $validatorCity->errors()->first());
                return redirect()->back()->withErrors(['error' => $validatorCity->errors()->first()]);
            }
        }

        $id = Str::uuid()->toString();
        $data =  [
            'id' => $id,
            'name' => $request->get('name'),
            'email' => $invitation->email,
            'telephone' => $request->get('telephone'),
            'birthdate' => $birthdate,
            'gender' => $request->get('gender'),
            'quarter' => $request->get('quarter'),
            'city' => $request->get('city'),
            //'password' => Hash::make($secret),
            //'registered_by' => Auth::user()->id,
            'active' => true,
            'was_invited' => true,
            'invited_by' => $client->id,
            'invitation_id' => $invitation->id,
        ];

        $invitation->sent_data = json_encode($data);
        $invitation->state = 'ACCEPTED';
        $invitation->save();
        $msg = __("Enregistré avec succès. Un administrateur validera votre requête.");
        session()->flash('status', $msg);
        return redirect()->back()->with('status', $msg);
    }
}
