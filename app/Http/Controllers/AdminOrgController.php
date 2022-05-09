<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Models\Admin;
use App\Models\DonationCase;
use App\Models\Category;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;


class AdminOrgController extends Controller
{
    // admin update organization data
    public function adminupdateorganizationprofile(Request $request)
    {
        $request->validate(
            [
              'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
              'verificationdocuments' => 'required|mimes:txt,xlx,xls,pdf|max:2048'
            ]
        );
        if (Gate::allows('isAdmin'))
        {
            if ($request->file()){
                $newimage = time() . '-' . $request->input('title') . '.' . $request->file('image')->extension();
                $request->file('image')->move(public_path('orgimages'), $newimage);
                $verificationdocuments = time() . '-' . $request->input('title') . '.' . $request->file('verificationdocuments')->extension();
                $request->file('verificationdocuments')->move(public_path('wesalorganizationdocuments'), $verificationdocuments);
            }
            $organization = Organization::find($request->input('organization_id'));
            $organization->title = $request->input('title');
            $organization->phonenumber = $request->input('phonenumber');
            $organization->image = $newimage;
            $organization->description = $request->input('description');
            $organization->verificationdocuments = $verificationdocuments;
            $organization->save();
        }
        else
        {
            dd('you are not admin');
        }
    }

    // //admin delete organization
    // public function adminDeleteOrg(Request $request){
    //     if (Gate::allows('isAdmin')){
    //         DB::table('organizations')->where('id', $request->input('org_id'))->delete();  
    //         DB::table('donation_cases')->where('organization_id', $request->input('case_id'))->delete();

    //     }
    //     else{
    //         dd('you are not admin');
    //         }
    // }


}
