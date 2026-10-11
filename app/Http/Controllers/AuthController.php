<?php


namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Models\Coa;
use App\Models\CoaGroup;
use App\Models\Company;
use App\Models\Customer;
use App\Models\BusinessSetup;
use App\Models\PaymentOption;
use App\Models\Pricebook;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\UserGroup;
use App\Services\SmsService;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;


class AuthController extends Controller{

   public function login(LoginUserRequest $request){

       $request->validated($request->all());

       if (!Auth::attempt(['userid' => $request->userid, 'password' => $request->password])) {
           return $this->error('', message: "Credentials do not match", statuscode: 401);
       }

       $user = User::where('userid',$request->userid)->first();

       if ($user->status == 0) {
           return $this->error('Account Blocked', message: "Your account is currently blocked.", statuscode: 401);
       }




      // $token = $user->createToken($request->userid)->plainTextToken;

      // abilities ('*') go in the second argument, expiration in the third
        $token = $user->createToken($request->userid, ['*'], now()->endOfDay())->plainTextToken;


       // Fetch all required additional data
       $coa          = Coa::where('businessid', $user->businessid)->get();
       $pricebook    = Pricebook::where('businessid', $user->businessid)->get();
       $products     = Product::where('businessid', $user->businessid)->get();
       $coagroups    = Coagroup::where('businessid', $user->businessid)->get();
       $stores       = Store::where('businessid', $user->businessid)->get();
       $usergroups  = UserGroup::where('businessid', $user->businessid)->get();
       $ledgersetup = BusinessSetup::where('businessid', $user->businessid)->first();
       $business = Company::where('businessid', $user->businessid)->first();
       $paymentoptions = PaymentOption::where('businessid', $user->businessid)->get();


        return $this->success([
            'token' => $token,
            'user' => $user,
            'coa'        => $coa,
            'pricebook'  => $pricebook,
            'products'   => $products,
            'coagroups'   => $coagroups,
            'stores'      => $stores,
            'usergroups'   => $usergroups,
            'ledgersetup' => $ledgersetup,
            'business'     => $business,
            'paymentoptions' => $paymentoptions,
            'serverdate'   => date("Y-m-d"),
        ]);
   }

    public function register(Request $request){

    $user = User::create([
        'userid'      => $request['userid'],
        'firstname'   => $request['firstname'],
        'surname'     => $request['surname'],
        'othername'   => $request['othername'],
        'password'    => bcrypt($request['password']),   // now using password field
        'status'      => 1,
        'usergroup'   => $request['usergroup'],
        'email'       => $request['email'],
        'telephone'   => $request['telephone'],
        'storeid'     => $request['storeid'],
        'businessid'  => $request['businessid'],
        'createdby'   => auth()->user()->userid ?? 'system',
        'modifiedby' => $request['modifiedby'] ?? 'system',
    ]);

        $sender = Company::where('businessid', $request['businessid'])->first();
        $sendernumber = $sender['shortcode'];
        //$sendernumber = 'Busia';

        $smsService = new SmsService();
        $test = $smsService->sendSms( $request['telephone'],
            'Welcome to '.$request['businessid'].'. Your ID is '.$user->userid.' and password is '.$request['password'],
        );


    return $this->success($user,$test );

}
    public function userupdate(Request $request){
        // $user = auth()->user(); used to update self records - where user is already logged in

        // 1. Find user by userid from the request
        $user = User::where('userid', $request->input('userid'))->firstOrFail();

        // 2. Update user fields
        $othername = $request->input('othername');
        $othername = ($othername === "" || $othername === null) ? null : $othername;

        $user->update([
            'firstname'   => $request->input('firstname'),
            'surname'     => $request->input('surname'),
            'othername'   => $othername,//$request->input('othername' ?: ''),
            'email'       => $request->input('email'),
            'telephone'   => $request->input('telephone'),
            'storeid'     => $request->input('storeid'),
            'businessid'  => $request->input('businessid'),
            'status'      => $request->input('status', $user->status),
            'usergroup'   => $request->input('usergroup', $user->usergroup),
            'modifiedby'  => auth()->user()->userid ?? 'system',
        ]);

        return $this->success($user);

        // 3. Update password ONLY if provided
//        if ($request->filled('password')) {
//            $user->password = Hash::make($request->input('password'));
//            $user->save();
//        }

//        return response()->json([
//            'message' => 'User updated successfully',
//            'user'    => $user
//        ]);
    }

    public function userupdatepassword(Request $request){
        // 1. Find user by userid from the request
        $user = User::where('userid', $request->input('userid'))->firstOrFail();


         //3. Update password ONLY if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
            $user->save();
        }

        return $this->success(['Password changed, successfully'],'Password changed, successfully');
//        return response()->json([
//            'message' => 'User updated successfully',
//            'user'    => $user
//        ]);

    }

    public function userforgotpassword(Request $request){
        // 1. Find user where BOTH userid and telephone match
        $user = User::where('userid', $request['userid'])
            ->where('telephone', $request['telephone']) // Added telephone check
            ->first();

        // 2. Validate if user exists
        if (!$user) {
            return $this->error('User not found', message: "User not found");

           // return response()->json(['message' => 'User not found with those credentials.'], 404);
        }

        // 3. Generate random password from your specific characters (max 6)
        $pool = 'abcdefgh123456789';
        $newPassword = substr(str_shuffle(str_repeat($pool, 6)), 0, 6);

        // 4. Update password in Database
        $user->password = Hash::make($newPassword);
        $user->save();

        // 5. Send SMS using your snippet
        //$sender = Company::where('businessid', $request['businessid'])->first();
        //$sendernumber = $sender['shortcode'];

        $smsService = new SmsService();
        $smsService->sendSms(
            $request['telephone'],
            'Password reset. Your ID is '.$user->userid.' and password is '.$newPassword,
        );

        return $this->success(['Password reset and sent successfully to ' . $request->input('telephone')],'Password reset and sent successfully');

    }

    public function logout(Request $request){
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully'
    ]);
}

    public function allUsers(Request $request){
    $users = User::where('businessid', $request->businessid)->get();

    return $this->success($users);

    }

    public function allUserGroups(Request $request)
    {
         $usersgroup = UserGroup::where('businessid', $request->businessid)->get();
        return $this->success($usersgroup);
    }

    public function addUserGroup(Request $request)
    {
        $user = UserGroup::create([
            'group_id' => $request->input('group_id'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'roles' => $request->input('roles'),
            'businessid' => $request->input('businessid'),
            'createdby'   => auth()->user()->userid ?? 'system',
            'modifiedby' => auth()->user()->userid ?? 'system',
        ]);

        return $this->success($user);
    }

    public  function UserGroupUpdate(Request $request)
    {
        $usergroup = UserGroup::where('group_id', $request->input('group_id'))->firstOrFail();

        $usergroup->update([
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'roles' => $request->input('roles'),
            'modifiedby' => auth()->user()->userid ?? 'system'
        ]);

        return $this->success($usergroup);

    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out from all devices'
        ]);
    }

    use HttpResponses;



}
