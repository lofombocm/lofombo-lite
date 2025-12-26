<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Notification;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function showNotificationView(string $locale, string $notificationid){
        if (!Auth::check() && !Auth::guard('client')->check()) {
            session()->flash('error', __("Désolé, vous n'avez pas l'accès"));
            return back()->withErrors(['error' => __("Désolé, vous n'avez pas l'accès")]);
        }
        $notification = Notification::where('id', $notificationid)->first();
        return view('notification.detail', ['notification' => $notification, 'data' => json_decode($notification->data, true)]);
    }

    public function setAsReadOrUnread(Request $request, string $locale, string $notificationid){
        if (!Auth::check() && !Auth::guard('client')->check()) {
            session()->flash('error', __("Désolé, vous n'avez pas l'accès"));
            return back()->withErrors(['error' => __("Désolé, vous n'avez pas l'accès")]);
        }
        $validator = Validator::make($request->all(), [
            'action'            => 'required|string|in:read,unread',
        ],[
            'action.required' => __("L'action est obligatoire."),
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $notification = Notification::where('id', $notificationid)->first();
        if ($notification == null) {
            $msg = __("La notification n'existe pas.");
            session()->flash('error', );
            return back()->withErrors(['error' => $msg]);
        }

        $read = false;
        if ($request->get('action') == 'read') {
            $read = true;
        }else if ($request->get('action') == 'unread') {
            $read = false;
        }else{
            session()->flash('error', 'Action not allowed');
            return back()->withErrors(['error' => 'Action not allowed']);
        }

        Notification::where('id', $notificationid)->update(['read' => $read]);
        $notification->read = $read;
        $notification->save();

        $msg = 'Notification marquée comme ' . ($read ? ' lue ' : ' non lue') . ' avec succes!';
        session()->flash('status', $msg);
        return back()->with('status', $msg);
    }

    public function showNotifs(string $locale, int $userid)
    {
        $user = Auth::user();
        if ($user === null) {
            return redirect()->route('authentification')->with('error',__("Access Denied"));
        }
        $utilsateur = User::where('id', $userid)->first();
        if ($utilsateur == null) {
            $msg = __("Une erreur est survenue, reessayez de nouveau.");
            session()->flash('error', $msg);
            return back()->with(['error' => $msg]);
        }

        if ($user->id !== $utilsateur->id) {
            $msg = __("Une erreur est survenue, reessayez de nouveau.");
            session()->flash('error', $msg);
            return back()->with(['error' => $msg]);
        }

        $notifications = Notification::where('sender_address', Auth::user()->email)->orWhere('recipient_address', Auth::user()->email)->where('read', false)->orderBy('created_at', 'desc')->get();
        $unreadMsgNum = count($notifications);

        return view('notification.index', ['notifications' => $notifications, 'unreadMsgNum' => $unreadMsgNum]);

    }

    public function showClientNotifs(string $locale, String  $clientid)
    {
        if (!Auth::guard('client')->check()) {
            return redirect()->route('authentification.client')->with('error',__("Access Denied"));
        }

        $client = Auth::guard('client')->user();
        $customer = Client::where('id', $clientid)->first();
        if ($customer == null) {
            $msg = __("Une erreur est survenue, reessayez de nouveau.");
            session()->flash('error', $msg);
            return redirect()->route('authentification.client')->with(['error' => $msg]);
            //return back()->with(['error' => $msg]);
        }

        if ($client->id !== $customer->id) {
            $msg = __("Une erreur est survenue, reessayez de nouveau.");
            session()->flash('error', $msg);
            return back()->with(['error' => $msg]);
        }

        $notifications0 = Notification:: where('recipient_address', Auth::guard('client')->user()->telephone)->where('read', false)->orderBy('created_at', 'desc')->get();
        $notifications = [];
        foreach ($notifications0 as $notification){
            array_push($notifications, $notification);
        }

        if(Auth::guard('client')->user()->email != null){
            $notifications1 = Notification::
            where('recipient_address', Auth::guard('client')->user()->email)->orWhere('recipient_address', Auth::guard('client')->user()->email)->where('read', false)->orderBy('created_at', 'desc')->get();
            foreach ($notifications1 as $notification){
                array_push($notifications, $notification);
            }
        }
        return view('notification.index-client', ['notifications' => $notifications, 'unreadMsgNum' => count($notifications)]);
    }
}
