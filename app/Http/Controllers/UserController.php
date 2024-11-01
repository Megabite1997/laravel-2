<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function logout(){
        auth()->logout();
        // return 'You are now logged out';
        return redirect('/')->with('success', 'You are now logged out.');
    }

    public function showCorrectHomepage(){
        if(auth()->check()){//retun boolean, you login or not
            return view('homepage-feed');
        }else{
            return view('homepage');
            
        }
    }


    public function register(Request $request){ //$request, takes all the incoming data request
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')], //name of table, name of column
            'email' => ['required', 'email', Rule::unique('users','username')],
            'password' => ['required', 'min:8', 'confirmed'], 
        ]);

        //Looks like Lavavel has auto bcrypt without using this below
        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);
        auth()->login($user);
        
        return redirect('/')->with('success', 'Thank you for creating an account');
    }

    public function login(Request $request){
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if(auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])){ 
            //it will compare user's attempted password with the hashed value in the database
            //if match, will return true
            $request->session()->regenerate(); //give session value to the browser
            return redirect('/')->with('success', 'You have succesfully logged in');
            // return 'Congrats!!!';

        }else{
             return redirect('/')->with('failure', 'Invalid login.');
            // return 'Sorry!!!';
        }
    }
}