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
    public function showNotificationView(string $notificationid){
        $notification = Notification::where('id', $notificationid)->first();
        return view('notification.detail', ['notification' => $notification, 'data' => json_decode($notification->data, true)]);
    }

    public function setAsReadOrUnread(Request $request, string $notificationid){
        if (!Auth::check() && !Auth::guard('client')->check()) {
            session()->flash('error', 'Vous n\'etes pas autorise');
            return back()->withErrors(['error' => 'Vous n\'etes pas autorise']);
        }
        $validator = Validator::make($request->all(), [
            'action'            => 'required|string|in:read,unread',
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $notification = Notification::where('id', $notificationid)->first();
        if ($notification == null) {
            session()->flash('error', 'Notification not found');
            return back()->withErrors(['error' => 'Notification not found']);
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

        $msg = 'Notification marquee comme ' . ($read ? ' lue ' : ' non lue') . ' avec succes!';
        session()->flash('status', $msg);
        return back()->with('status', $msg);
    }

    public function showNotifs(int $userid)
    {
        $user = Auth::user();
        $utilsateur = User::where('id', $userid)->first();
        if ($utilsateur == null) {
            session()->flash('error', 'Quelque chose s\'est mal deroule.');
            return back()->with(['error' => 'Quelque chose s\'est mal deroule.']);
        }

        if ($user->id !== $utilsateur->id) {
            session()->flash('error', 'Quelque chose s\'est mal deroule.');
            return back()->with(['error' => 'Quelque chose s\'est mal deroule.']);
        }


        $notifications = Notification::where('sender_address', Auth::user()->email)->orWhere('recipient_address', Auth::user()->email)->where('read', false)->orderBy('created_at', 'desc')->get();
        $unreadMsgNum = count($notifications);

        return view('notification.index', ['notifications' => $notifications, 'unreadMsgNum' => $unreadMsgNum]);

    }


    public function showClientNotifs(String  $clientid)
    {
        $client = Auth::guard('client')->user();
        $customer = Client::where('id', $clientid)->first();
        if ($customer == null) {
            session()->flash('error', 'Quelque chose s\'est mal deroule.');
            return back()->with(['error' => 'Quelque chose s\'est mal deroule.']);
        }

        if ($client->id !== $customer->id) {
            session()->flash('error', 'Quelque chose s\'est mal deroule.');
            return back()->with(['error' => 'Quelque chose s\'est mal deroule.']);
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
