<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function UserDashboard()
    {   
        $id = Auth::user()->id;
        $user = User::find($id);
        return view('index', compact('user'));
    }//end method

    public function UserLogout(Request $request){
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    } // End Mehtod
}
