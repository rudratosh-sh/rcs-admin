<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DataTables,Auth;
use App\RcsBalance;
use App\RcsAccount;
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('users');
    }

    public function getUserList(Request $request)
    {
        
        $data  = User::get();

        return Datatables::of($data)
                ->addColumn('roles', function($data){
                    $roles = $data->getRoleNames()->toArray();
                    $badge = '';
                    if($roles){
                        $badge = implode(' , ', $roles);
                    }

                    return $badge;
                })
                ->addColumn('permissions', function($data){
                    $roles = $data->getAllPermissions();
                    $badges = '';
                    foreach ($roles as $key => $role) {
                        $badges .= '<span class="badge badge-dark m-1">'.$role->name.'</span>';
                    }

                    return $badges;
                })
                ->addColumn('action', function($data){
                    if($data->name == 'Super Admin'){
                        return '';
                    }
                    if (Auth::user()->can('manage_user')){
                        return '<div class="table-actions">
                                <a href="'.url('user/'.$data->id).'" ><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                <a href="'.url('user/delete/'.$data->id).'"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                            </div>';
                    }else{
                        return '';
                    }
                })
                ->rawColumns(['roles','permissions','action'])
                ->make(true);
    }

    public function create()
    {
        try
        {
            $roles = Role::pluck('name','id');
            return view('create-user', compact('roles'));

        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);

        }
    }

    public function store(Request $request)
    {
          
        // //if role is reseller 
        // if($request->role>2){
        //     $validator = Validator::make($request->all(), [
        //         'name'     => 'required | string ',
        //         'email'    => 'required | email | unique:users',
        //         'password' => 'required | confirmed',
        //         'role'     => 'required',
        //         'company_name' => 'required',
        //         'company_address' =>'required',
        //         'balance' =>'required',
        //         'assigned' =>'required',
        //         'mobile_no' =>'required | unique:users'
        //     ]);
        // }else{
        // // create user 
        // $validator = Validator::make($request->all(), [
        //     'name'     => 'required | string ',
        //     'email'    => 'required | email | unique:users',
        //     'password' => 'required | confirmed',
        //     'role'     => 'required',
        //     'mobile_no' =>'required | unique:users'
        // ]);
        // }

        $validator = Validator::make($request->all(), [
            'name'     => 'required | string ',
            'email'    => 'required | email | unique:users',
            'password' => 'required | confirmed',
            'role'     => 'required',
            'company_name' => 'required',
            'company_address' =>'required',
            'balance' =>'required',
            'assigned' =>'required',
            'mobile_no' =>'required | unique:users'
        ]);
       
        if($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            
            // store user information
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'mobile_no' => $request->mobile_no,
                'company_name' => $request->company_name,
                'company_address' => $request->company_address
            ]);
    
           
             //store sms balance
            $valid_from = date('Y-m-d');
           $valid_till =  date('Y-m-d', strtotime($valid_from. ' + '.(int)$request->validity.'month'));    
            // store RCS Balance information
            $rcs_balance = RcsBalance::create([
                'user_id'     => User::latest()->first()->id,
                'recharge'    => $request->balance,
                'credit_remaining' => $request->balance,
                'valid_from' => $valid_from,
                'valid_till' => $valid_till,
                'assigned' => $request->assigned,
            ]);

            $rcs_account = array(
                'user_id' => User::latest()->first()->id,
                'type' => 'CREDIT',
                'balance' => $request->balance,
                'validity' => $valid_till,
                'created_by' => Auth::user()->id
            );

            RcsAccount::create($rcs_account);
            // assign new role to the user
            $user->syncRoles($request->role);

            if($user){ 
                return redirect('users')->with('success', 'New user created!');
            }else{
                return redirect('users')->with('error', 'Failed to create new user! Try again.');
            }
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try
        {
            $user  = User::with('roles','permissions')->find($id);

            if($user){
                $user_role = $user->roles->first();
                $roles     = Role::pluck('name','id');

                return view('user-edit', compact('user','user_role','roles'));
            }else{
                return redirect('404');
            }

        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request)
    {

        // update user info
        $validator = Validator::make($request->all(), [
            'id'       => 'required',
            'name'     => 'required | string ',
            'email'    => 'required |unique:users,email,'.$request->id,
            'role'     => 'required',
            'mobile_no' => 'required |unique:users,mobile_no,'.$request->id,
        ]);

        // check validation for password match
        if(isset($request->password)){
            $validator = Validator::make($request->all(), [
                'password' => 'required | confirmed'
            ]);
        }
        
        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try{
            
            $user = User::find($request->id);

            $update = $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
                'company_name' => $request->company_name,
                'company_address' => $request->company_address
            ]);

            // update password if user input a new password
            if(isset($request->password)){
                $update = $user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            // sync user role
            $user->syncRoles($request->role);

            return redirect()->back()->with('success', 'User information updated succesfully!');
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);

        }
    }


    public function delete($id)
    {
        $user   = User::find($id);
        if($user){
            $user->delete();
            return redirect('users')->with('success', 'User removed!');
        }else{
            return redirect('users')->with('error', 'User not found');
        }
    }

    public function profileEdit()
    {
        try
        {
            $user  = User::with('roles','permissions')->find(Auth::user()->id);

            if($user){
                $user_role = $user->roles->first();
                $roles     = Role::pluck('name','id');
                return view('profile', compact('user','user_role','roles'));
            }else{
                return redirect('404');
            }
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function profileUpdate(Request $request)
    {

        // update user info
        $validator = Validator::make($request->all(), [
            'id'       => 'required',
            'name'     => 'required | string ',
            'email'    => 'required |unique:users,email,'.Auth::user()->id,
            'mobile_no' => 'required |unique:users,mobile_no,'.Auth::user()->id,
        ]);

        // check validation for password match
        if(isset($request->password)){
            $validator = Validator::make($request->all(), [
                'password' => 'required | confirmed'
            ]);
        }
        
        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try{
            
            $user = User::find(Auth::user()->id);

            $update = $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
                'company_name' => $request->company_name,
                'company_address' => $request->company_address

            ]);

            // update password if user input a new password
            if(isset($request->password)){
                $update = $user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            return redirect()->back()->with('success', 'User information updated succesfully!');
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);

        }
    }

}