<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginClientController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home-client';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }


    public function loginClientView(): View
    {
        return view('auth.client.login');
    }

    public function username()
    {
        return 'telephone';
    }

    public function postLoginClientView(Request $request): RedirectResponse
    {
        $request->validate([
            'telephone' => 'required|string|exists:clients,telephone',
            'password' => 'required|string|min:8',
        ]);

        $client = Client::where('telephone', $request->get('telephone'))->first();
        if (!$client->active) {
            return back()->withErrors([
                'error' => 'The client is not active.',
            ]);
        }

        /*$credentials = array();
        $credentials['email'] = $request->get('telephone');
        $credentials['password'] = $request->get('password');*/

        //$request->all()['email'] =  $request->get('telephone');
        $credentials = $request->only('telephone', 'password');

        //$client = $client->save();
        //return json_encode($credentials);



        if (Auth::guard('client')->attempt($credentials)) {
            /*$h = fopen('test.txt', 'w+');
            fwrite($h, json_encode( json_encode($credentials)));
            fclose($h);*/
            //return redirect()->route('home.client')->withSuccess('status', 'You have Successfully loggedin');
            return redirect()->intended('home-client')->withSuccess('status', 'You have Successfully loggedin');
        }
        //return redirect("auth/client")->back()->withError(['message' => 'Invalid Telephone or password']);
        return back()->withErrors([
            'error' => 'The provided credentials do not match our records.',
        ]);
    }

}
