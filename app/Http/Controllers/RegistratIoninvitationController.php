<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\RegistratIoninvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistratIoninvitationController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    protected $redirectTo = '/home';

    public function index(string $invitationId){
        $invitation = RegistratIoninvitation::where('id', $invitationId)->where('active', true)->first();
        if($invitation == null){
            $msg = 'Invitation non trouvee';
            session()->flash('error', $msg);
            return view('auth.response-invitation',['error'=>$msg]);
        }

        $expirationdate = Carbon::parse($invitation->expire_at);

        if ($expirationdate->isBefore(Carbon::now())) {
            $msg = 'L\'invitation est expiree le ' . $expirationdate->format('d/m/Y') .
                ' a ' . $expirationdate->hour . ':' . $expirationdate->minute . ':' . $expirationdate->second;
            session()->flash('error', $msg);
            return view('auth.response-invitation',['error'=>$msg]);
        }

        return view('auth.response-invitation',['invitation'=>$invitation]);
    }


    public function postRegistratioInvitationResponse(Request $request, string $invitationId): RedirectResponse
    {

        $validator = Validator::make($request->all(), [
            /*'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users',*/
            'password' => 'required|string|min:8|max:20|confirmed',
            //'is_admin' => 'required|string|in:on,off'
        ]);

        if($validator->fails()){
            $msg = $validator->errors()->first();
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }

        $invitation = RegistratIoninvitation::where('id', $invitationId)->where('active', true)->first();
        if($invitation == null){
            $msg = 'Invitation non trouvee';
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }

        $validator1 = Validator::make(['email' => $invitation->email], [
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if($validator1->fails()){
            $msg = $validator1->errors()->first();
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }

        $data = [
            'name' => $invitation->name,
            'email' => $invitation->email,
            'password' => Hash::make($request->get('password')),
            'active' => true,
            'is_admin' => $invitation->is_admin,
        ];

        $user = User::create($data);
        Auth::login($user);
        $msg = 'Vous avez ete enregistre avec succes.';
        session()->flash('status', $msg);

        return redirect('/home')->with('message', $msg);
        //return back()->with('status', $msg);//->withSuccess('status', 'Great! You have Successfully Registered.');
    }



}
