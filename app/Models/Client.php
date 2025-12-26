<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class Client extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $guarded = [];

    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
          'id'
        , 'name'
        , 'email'
        , 'telephone'
        , 'birthdate'
        , 'gender'
        , 'quarter'
        , 'city'
        , 'password'
        , 'registered_by'
        , 'active'
        , 'was_invited'
        , 'invited_by'
        , 'invitation_id'
    ];

    protected $hidden = [
        'password',
    ];

   public function canUpdateBirthdate():bool{
       if ($this->birthdate === null || $this->birthdate == ''){
           return true;
       }
       $birthdateArray = explode("-", $this->birthdate);
       if (count($birthdateArray) >= 2){
           return false;
       }
       return true;
   }

   public function updateBirthdate(Request $request):array
   {
       if ($this->canUpdateBirthdate()){
           if ($request->filled('day') && $request->filled('month')){
               $validatorBirthdate = Validator::make($request->all(), [
                   'day' => 'string|in:01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31',
                   'month' => 'string|in:01,02,03,04,05,06,07,08,09,10,11,12',
                   //'year' => 'integer|between:1900,'.date('Y'),
               ],[
                   'day.in' => __("Le format du jour est representé avec deux chiffres"),
                   'month.in' => __("Le format du mois est representé avec deux chiffres"),
               ]);
               if($validatorBirthdate->fails()){
                   session()->flash('error', $validatorBirthdate->errors()->first());
                   //return redirect()->back()->withErrors(['error' => $validatorBirthdate->errors()->first()]);
                   return ['success' => false, 'message' => $validatorBirthdate->errors()->first()];
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
               //$birthdate = $request->get('year').'-'.$request->get('month').'-'.$request->get('day');
               //$birthdateFormatedArr = explode('-', $birthdate);
               //$secret = $birthdateFormatedArr[2] . $birthdateFormatedArr[1] . $birthdateFormatedArr[0];
               $this->birthdate = $birthdate;
           }
       }
       return ['success' => true, 'message' => 'Birthdate updated'];
   }

}
