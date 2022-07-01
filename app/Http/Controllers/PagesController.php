<?php

namespace App\Http\Controllers;

use App\Http\Controllers\UserController;
use App\Models\User;
use App\Models\DonationCase;
use App\Models\Organization;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use PhpParser\Node\Stmt\Foreach_;
use Illuminate\Support\Facades\Storage;

class PagesController extends Controller
{
    //data shown in user homepage
    public function userhomepage($id)
    {
        $users = User::find($id);
        if(count($users->donationOperations)>0)
        {
            $n = count($users->donationOperations) - 1; 
            $lastdonation = $users->donationOperations[$n];
            $reminders = $users->reminders;
            $users['donation_operations'] = $lastdonation;
    
            
            $cases = DonationCase::with(['organization' => function ($query) {
                $query->select(['id', 'title']);
            }])->get();    
            return response()->json([
                'users' => $users,
                'cases' => $cases,
                'lastdonation' => $lastdonation
            ]);
        }
        else{
            return response()->json([
                'message'=>'no donation operations'
            ]);
        }
     /*   $n = count($users->donationOperations) - 1; //returns Undefined array key -1 after id = 3
        $lastdonation = $users->donationOperations[$n];
        $reminders = $users->reminders;
        $users['donation_operations'] = $lastdonation;

        
        $cases = DonationCase::with(['organization' => function ($query) {
            $query->select(['id', 'title']);
        }])->get();*/
        // $cases = DonationCase::all();
        // $rg3ytitle = DB::table('organization')->where()->get('title') ;

        // $kimocases = DonationCase::get();
        // $cases = [];
        // foreach($kimocases as $case){
        //     $cases[]= [[$case , $case->organization['title']]] ;
        //     // $cases[]= $case;
        // }
    }

    //data shown in organization homepage
    public function orghomepage($id)
    {
        $organization = Organization::find($id);
        $orgcases = $organization->orgcases;
        $sumcases = count($orgcases);
        //$sumcases = DB::table('donation_cases')->where('organization_id', $id)->sum('raised_amount');
        // $totaldonations = Organization::find($id)->orgcases->sum('raised_amount');
        
        // SELECT COUNT(donation_operations.user_id) FROM donation_operations LEFT OUTER JOIN donation_cases 
        // ON donation_operations.case_id = donation_cases.id WHERE donation_cases.organization_id=1
        $totaldonations = DB::table('donation_operations')
                                ->leftjoin('donation_cases', 'donation_operations.case_id' ,'=','donation_cases.id')
                                ->where('donation_cases.organization_id', '=', $id)
                                ->sum('donation_operations.amount');
                                

        $n = DB::table('donation_operations')
                                ->join('donation_cases', 'donation_operations.case_id' ,'=','donation_cases.id','left outer')
                                ->where('donation_cases.organization_id', '=', $id)
                                ->groupBy('donation_operations.user_id')
                                ->select('donation_operations.user_id')
                                ->get();
        $totaldonors = count($n);
        return response()->json([
            'organization' => $organization,
            'total cases:'  => $sumcases,
            'total donations:' => $totaldonations,
            'totaldonors:'=> $totaldonors,

        ]);
    }

    //data shown in casepage
    public function casepage($id)
    {
        // first method (eloquent)
        $case = DonationCase::find($id);
        //second method (query builder)
        // $case = DB::table('donation_cases')->where('id', $id)->select('title', 'image', 'goal_amount', 'raised_amount', 'description')->get();
        $caseorg = DonationCase::find($id)->organization['title'];
        $casecat = DonationCase::find($id)->categories['title'];
        $totaldonors = DB::table('donation_operations')->where('case_id', $id)->count();
        return response()->json([
            'case' => $case,
            'organizationtitle' => $caseorg,
            'categorytitle' => $casecat,
            'totaldonors' => $totaldonors

        ]);
    }

    // gates check for admin, organization ,and user
    public function indexadmin()
    {
        if (Gate::allows('isAdmin')) {

            dd('Admin allowed');
        } else {

            dd('You are not Admin');
        }
    }
    public function indexuser()
    {
        if (Gate::allows('isUser')) {

            dd('User allowed');
        } else {

            dd('You are not User');
        }
    }
    public function indexorganization()
    {
        if (Gate::allows('isOrganization')) {

            dd('Organization allowed');
        } else {

            dd('You are not Organization');
        }
    }
   
}