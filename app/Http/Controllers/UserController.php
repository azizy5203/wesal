<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Category;
use App\Models\DonationCase;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
Configuration::instance([
    'cloud' => [
      'cloud_name' => 'dyxbkmyf4', 
      'api_key' => '757473359554448', 
      'api_secret' => '3yRGMnGdyae8hHqqOyVEyqFpMK4'],
    'url' => [
      'secure' => true]]);
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'allusers' => $users,
        ]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $users = User::find($id)->toJson();

        $users = json_decode($users);

        return ($users);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function edittest()
    {
        return view('layouts.donationtest');
    }
    
    
    public function editprofile(Request $request)
    {
            $user = User::find($request->input('id'));
            if($request->input('name')!=null){
                $user->name = $request->input('name');
            }
            if($request->input('phonenumber')!=null){
                $user->phonenumber = $request->input('phonenumber');
            }
            if($request->input('address')!=null){
                $user->address = $request->input('address');
            }
            // $user->image = $newimage;
            $user->save();
            $passWord = $request->input('password');
            if( $passWord != null){
                $request->validate(
                    ['password' => 'required|string|min:6|confirmed', ]);
                $user->password = bcrypt($passWord);
                $user->save();  
            }
            if ($request->hasfile('image')) {
                Cloudinary::uploadApi();
                require 'vendor/autoload.php';
                $request->validate(
                         [
                             'image' => 'image|mimes:jpeg,png,jpg,gif,svg|',
                         ]
                     );
                //upload image
                $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();
                $user->image = $uploadedFileUrl;
                $user->save();
            }
    }


    public function userprofile($id)
    {
        $donationcase = User::find($id)->donationOperations->groupBy('id');
        $user = User::find($id);
        $donationhistory = DB::table('donation_operations')->where('user_id', $id)->get();
        return response()->json([
            'user' => $user,
            'donationhistory' => $donationhistory,
            'donationcase' => $donationcase
        ]);
    }


    public function createfavcase(Request $request)
    {
        $request->validate([
            'case_id' => ['required', 'exists:donation_cases,id'],
        ]);
        
       if(Auth::User()->favoriteCases()->where('case_id', $request->input('case_id'))->exists()){
        return ('already exists');
       }
       else{
        Auth::User()->favoriteCases()->attach($request->input('case_id'));
       }
    }


    public function deletefavcase(Request $request)
    {
        Auth::User()->favoriteCases()->detach($request->input('case_id'));
    }

    public function deletereminder(Request $request)
    {
        Auth::User()->reminders()->delete(Auth::id());
    }

    public function setreminder(Request $request){
        $request->validate(
            [
                'remind_at' => 'required',
                'message' => 'required',
            ]
        );
        Auth::User()->reminders()->create([
            'remind_at'=>$request->input('remind_at'),
            'message'=>$request->input('message'),
            'user_id'=>Auth::id()
        ]);
        
    }

    public function remindertest(){
        return view('layouts.testreminder');   
    }
    public function favcasepage()
    {
        return view('layouts.favcasetest');
    }
    /*user 3ando kaza case we ana bsgl case id we user id fa ana 3ayez ageb cases */
    public function showfavcase()
    {
        $Favcases=User::find(Auth::id())->showfavcases()->get();
        return response()->json([
            'favcase' => $Favcases
        ]);
    }
    
    public function usersadmins()
    {
        // dd(Auth::user());
        $admins = User::all()->where('type', '=', 'admin');
        return response()->json([
            'admins' => $admins,
        ]);
    }

    public function usersnotadmins()
    {
        $users = User::all()->where('type', '!=', 'admin');
        return response()->json([
            'usersnotadmins' => $users,
        ]);
    }
    //function to get all reminders for certain user
    public function getreminder()
    {
        $reminder = Auth::User()->reminders()->get();
        return response()->json([
            'reminder' => $reminder,
        ]);
    }
}