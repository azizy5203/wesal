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


class AdminUserController extends Controller
{
    //admin add user whith any type
    public function addUserWithType(Request $request)
    {
        $request->validate(
            [
            //   'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
              'verificationdocuments' => 'required|mimes:txt,xlx,xls,pdf|max:2048'
            ]
        );
        // $newimage = time() . '-' . $request->input('title') . '.' . $request->file('image')->extension();
        // $request->file('image')->move(public_path('orgimages'), $newimage);

        $verificationdocuments = time() . '-' . $request->input('title') . '.' . $request->file('verificationdocuments')->extension();
        $request->file('verificationdocuments')->move(public_path('wesalorganizationdocuments'), $verificationdocuments);

            DB::table('users')->insert([
                'name'=> $request->input('name'),
                'email'=> $request->input('email'),
                'password'=> bcrypt($request->input('password')),
                'phonenumber'=> $request->input('phone'),
                'address'=> $request->input('address'),
                'type'=> $request->input('userType'),]);
                $id= DB::table('users')->where('email',$request->input('email'))->value('id');
               
            if($request->input('userType') =='admin'){
                $date = new DateTime();
                 DB::table('admins')->insert([
                    'id'=> $id,
                    'access_level' =>$request->input('name'),
                    'created_at' =>$date->format('Y-m-d H:i:s'),
                    'updated_at'=>$date->format('Y-m-d H:i:s'),
                ]);
            }
            elseif($request->input('userType') =='organization'){
                $date = new DateTime();
                DB::table('organizations')->insert([
                        'title' =>$request->input('title'),
                        'verificationdocuments' =>$verificationdocuments,
                        'phonenumber' =>$request->input('phonenumber'),
                        // 'image' =>$newimage,
                        'description' =>$request->input('description'),
                        'verified' => true,
                        'verifiedat' =>$date->format('Y-m-d H:i:s'),
                        'verifiedby' => $request->input('admin_id'),
                        'creator_id'=> $id,
                        'created_at' =>$date->format('Y-m-d H:i:s'),
                        'updated_at'=>0,
                     ]);
            }
           
            return response()->json([
                'message' => 'User added successfully',
            ], 200);
    }

    //admin update user to admin
    public function adminupdateusertoadmin(Request $request)
    {
        if ($request->input('adminType')=='admin'){
            // if ($request->file()){
            //     $newimage = time() . '-' . $request->input('name') . '.' . $request->file('user_image')->extension();
            //     $request->file('user_image')->move(public_path('userimages'), $newimage);
            // }
            $user = User::find($request->input('user_id'));
            $user->name = $request->input('name');
            $user->phonenumber = $request->input('phone');
            $user->address = $request->input('address');
            // $user->image = $newimage;
            $user->type = $request->input('userType');
            if($user->type == 'admin'){
                $date = new DateTime();
                 DB::table('admins')->insert([
                    'id'=> $user->id,
                    'access_level' =>$user->name,
                    'created_at' =>$date->format('Y-m-d H:i:s'),
                    'updated_at'=>$date->format('Y-m-d H:i:s'),
                ]);
            }
         }
         else
         {
            return  response()->json([
                'message' => 'You are not an admin',
            ], 401);
         }
    }

    // admin update user to organization
    public function adminupdateusertoorg(Request $request)
    {
        // $request->validate(
        //     [
        //       'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        //     ]
        // );
        if ($request->input('adminType')=='admin')
        {
            $user = User::find($request->input('user_id'));
            $user->type = $request->input('userType');
            if($user->type == 'organization')
            {
                $request->validate(
                    [
                    //   'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                      'verificationdocuments' => 'required|mimes:txt,xlx,xls,pdf|max:2048'
                    ]
                );
                if($request->file()){
                    // $neworgimage = time() . '-' . $request->input('title') . '.' . $request->file('org_image')->extension();
                    // $request->file('org_image')->move(public_path('orgimages'), $neworgimage);
                    
                    $verificationdocuments = time() . '-' . $request->input('title') . '.' . $request->file('verificationdocuments')->extension();
                    $request->file('verificationdocuments')->move(public_path('wesalorganizationdocuments'), $verificationdocuments);
                }
                $date = new DateTime();
                DB::table('organizations')->insert([
                    'title' =>$request->input('name'),
                    'verificationdocuments' =>$verificationdocuments,
                    'phonenumber' =>$request->input('phone'),
                    // 'image' =>$neworgimage,
                    'description' =>$request->input('description'),
                    'verified' => true,
                    'verifiedat' =>$date->format('Y-m-d H:i:s'),
                    'verifiedby' => $request->input('adminId'),
                    'creator_id'=> $user->id,
                    'created_at' =>$date->format('Y-m-d H:i:s'),
                    'updated_at'=>$date->format('Y-m-d H:i:s'),
                 ]);

            }
            $user->save();
        }
        else
        {
            return  response()->json([
                'message' => 'You are not an admin',
            ], 401);
        }
    }

    // admin update user profile
    public function adminupdateuserprofile(Request $request)
    {

        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|:users,email',
                'password' => 'required|string|min:6|confirmed',
                //'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]
        );

        $user = User::find($request->input('user_id'));
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phonenumber = $request->input('phone');
        $user->address = $request->input('address');
        $user->password = bcrypt($request->input('password'));
        $user->type = $request->input('userType');
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
        ], 200);
    }

    //admin delete user
    public function adminDeleteUserByType(Request $request ){
       
            $user = User::find($request->input('user_id'));
            if($user['type'] = 'organization'){
                DB::table('users')->where('id', $request->input('user_id'))->delete();
                DB::table('organizations')->where('creator_id', $request->input('user_id'))->delete();
                DB::table('donation_cases')->where('organization_id', $request->input('user_id'))->delete();

            }
            elseif($user['type'] ='admin') {
                DB::table('users')->where('id', $request->input('user_id'))->delete();
                DB::table('admins')->where('id', $request->input('user_id'))->delete();
                DB::table('organizations')->where('creator_id', $request->input('user_id'))->delete();
                DB::table('donation_cases')->where('organization_id', $request->input('user_id'))->delete();

                
            }
            else{
                DB::table('users')->where('id', $request->input('user_id'))->delete();
            }
            
     
            return response()->json([
                'message' => 'User deleted successfully',
            ], 200);
    }

    //retrieve the organization requests
    public function retrieverequests(Request $request)
    {
        $organization = DB::table('Organizations')->where('verified', '=', 0)->get();
            return response()->json([
                'organization' => $organization
            ]);
            // return view('layouts.deciderequest', ['pendingorganizations' => $organization]);
            return response()->json([
                'message' => 'Requests retrieved successfully',
            ], 200);
        
    }
    //accept the organization requests 
    public function acceptrequest(Request $request, $id)
    {
            $accept = DB::table('Organizations')->where('id', $id)->update(['verified' => 1, 'verifiedby' =>  Auth::user()->id]);
            return response()->json([
                'message' => 'Request accepted successfully',
            ], 200);
        
    }
    //reject the organizations requests
    public function rejectrequest($id)
    {
            $reject = DB::table('Organizations')->where('id', $id)->delete();
            return response()->json([
                'message' => 'Request rejected successfully',
            ], 200);
        
    }


}