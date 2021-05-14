<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\ApiToken;
use App\Models\UserPasswordReset;
use App\Models\UserAgent;
use App\Mail\SignupMail;
use App\Mail\ForgetPasswordMail;
use Twilio\Rest\Client as TwilioClient;
use Carbon\Carbon;
use DB;
use Auth;

class UsersController extends Controller
{
    public function sellerSignUp(Request $request){
	    	$this->validate($request, [
	      		'first_name' => 'required',
	          'last_name' => 'required',
	          'phone' => 'required',
	      		'email' => 'required|email'
	      ]);

	    	if (Users::where('email', $request->email)->exists()) {
	        	return $this->sendResponse("Email already exists!",200,false);
	      }

	      if (Users::where('phone', $request->phone)->exists()) {
	        	return $this->sendResponse("Phone no. already exists!",200,false);
	      }

	      $time = strtotime(Carbon::now());
        $uuid = "usr".$time.rand(10,99)*rand(10,99);
	      $user = new Users;
        $user->uuid = $uuid;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->role = "SELLER";
        $user->phone_verified = "NO";
        $user->email_verified = "NO";
        $user->image = "default.png";
        $result = $user->save();

        if ($result) {
						$this->configSMTP();
						$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
      			Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);
			      $data = ['name'=>$request->first_name.' '.$request->last_name, 
				                'verification_token'=>$verification_token, 
				                'email'=>$request->email,
				                'app_url'=>env('APP_URL')
			              ];
			      try{
			          Mail::to($request->email)->send(new SignupMail($data));  
			      }catch(\Exception $e){
			          $msg = $e->getMessage();
			          return $this->sendResponse($msg,200,false);
			      }

					  return $this->sendResponse("Signup successfull!");
				}
    }

    public function buyerSignUp(Request $request){
	    	$this->validate($request, [
	      		'first_name' => 'required',
	          'last_name' => 'required',
	          'phone' => 'required',
	      		'email' => 'required|email'
	      ]);

	    	if (Users::where('email', $request->email)->exists()) {
	        	return $this->sendResponse("Email already exists!",200,false);
	      }

	      if (Users::where('phone', $request->phone)->exists()) {
	        	return $this->sendResponse("Phone no. already exists!",200,false);
	      }

	      $time = strtotime(Carbon::now());
        $uuid = "usr".$time.rand(10,99)*rand(10,99);
	      $user = new Users;
        $user->uuid = $uuid;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->role = "BUYER";
        $user->phone_verified = "NO";
        $user->email_verified = "NO";
        $user->image = "default.png";
        $result = $user->save();

        if ($result) {
						$this->configSMTP();
						$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
      			Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);
			      $data = ['name'=>$request->first_name.' '.$request->last_name, 
				                'verification_token'=>$verification_token, 
				                'email'=>$request->email,
				                'app_url'=>env('APP_URL')
			              ];
			      try{
			          Mail::to($request->email)->send(new SignupMail($data));  
			      }catch(\Exception $e){
			          $msg = $e->getMessage();
			          return $this->sendResponse($msg,200,false);
			      }

					  return $this->sendResponse("Signup successfull!");
				}
    }

    public function agentSignUp(Request $request){
	    	$this->validate($request, [
	      		'first_name' => 'required',
	          'last_name' => 'required',
	          'phone' => 'required',
	      		'email' => 'required|email',
	          'mls_id' => 'required',
	          'mls_name' => 'required'
	      ]);

	    	if (Users::where('email', $request->email)->exists()) {
	        	return $this->sendResponse("Email already exists!",200,false);
	      }

	      if (Users::where('phone', $request->phone)->exists()) {
	        	return $this->sendResponse("Phone no. already exists!",200,false);
	      }

	      $time = strtotime(Carbon::now());
        $uuid = "usr".$time.rand(10,99)*rand(10,99);
	      $user = new Users;
        $user->uuid = $uuid;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->role = "AGENT";
        $user->mls_id = $request->mls_id;
        $user->mls_name = $request->mls_name;
        $user->phone_verified = "NO";
        $user->email_verified = "NO";
        $user->image = "default.png";
        $result = $user->save();

        if ($result) {
						$this->configSMTP();
						$verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
      			Users::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);
			      $data = ['name'=>$request->first_name.' '.$request->last_name, 
				                'verification_token'=>$verification_token, 
				                'email'=>$request->email,
				                'app_url'=>env('APP_URL')
			              ];
			      try{
			          Mail::to($request->email)->send(new SignupMail($data));  
			      }catch(\Exception $e){
			          $msg = $e->getMessage();
			          return $this->sendResponse($msg,200,false);
			      }

					  return $this->sendResponse("Signup successfull!");
				}
    }

    public function getSingleUser(Request $request){
    		$this->validate($request, [
	      		'user_id' => 'required'
	      ]);

	      $user = Users::where('uuid', $request->user_id)->first();

	      if ($user) {
    				return $this->sendResponse($user);
    		}else{
    				return $this->sendResponse("Sorry, User not found!", 200, false);
    		}
    }

    public function addAgent(Request $request){
    		$this->validate($request, [
	      		'user_id' => 'required',
	          'agent_id' => 'required'
	      ]);

    		$user_agent = new UserAgent;
    		$user_agent->user_id = $request->user_id;
    		$user_agent->agent_id = $request->agent_id;
    		$result = $user_agent->save();

    		if ($result) {
    				return $this->sendResponse("Agent add successfully!");
    		}else{
    				return $this->sendResponse("Sorry, Something went wrong!", 200, false);
    		}
    }
}