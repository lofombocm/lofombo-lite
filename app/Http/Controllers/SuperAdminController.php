<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Nanorocks\LicenseManager\Models\License;
use Nanorocks\LicenseManager\Services\LicenseService;

class SuperAdminController extends Controller
{
    public function __construct()
    {

    }

    public function index(){
        return view('super_admin.index');
    }

    public static function checkLicences():array
    {
        $licences = License::all();
        $validLicences = [];
        $service = app(LicenseService::class);

        foreach ($licences as $licence) {
            if($service->validateLicense($licence->license_key)){
                $validLicences[] = $licence;
            }
        }
        return [
            'valid' => count($validLicences) !== 0,
            'reason' => count($validLicences) !== 0 ? null : (count($licences) === 0 ? "NO_LICENSE_ACTIVATED" : "NO_LICENSE_VALID"),
            'valid_licenses' => $validLicences
        ];
    }

    public static function addUserToLicence(User $user, License $license)
    {
        $newMetadata = [];
        $newMetadata['is_trial'] = $license->metadata['is_trial'];
        $metadataUsers = [];
        foreach ($license->metadata['users'] as $u){
            $metadataUsers[] = $u;
        }
        $metadataUsers[] = $user;
        $newMetadata['users'] = $metadataUsers;

        $license->metadata = array_merge($license->metadata, $newMetadata);
        $license->save();
    }

}






