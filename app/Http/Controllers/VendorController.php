<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;



class VendorController extends Controller
{
    public function VendorDashboard()
    {
        return view('vendor.index');
    }// end of method

    public function VendorLogin(){
        return view('vendor.vendor_login');
    }// end of method

    public function VendorDestroy(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return view('vendor.vendor_login');
    }// end of method

    public function VendorProfile(){
        $id = Auth::user()->id;
        $user = User::find($id);
        return view('vendor.vendor_profile', compact('user'));
    }// end of method

    public function VendorProfileUpdate(Request $request){
        $id = Auth::user()->id;
        $user = User::find($id);
        $user->username = $request->username;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/vendor_images/' . $user->photo));
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/vendor_images'), $filename);
            $user['photo'] = $filename;
        }
        $user->save();
        $notification = array(
            'message' => 'Profile Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }// end of method

    public function VendorPassword(){
        return view('vendor.vendor_password');
    }// end of method

    public function VendorPasswordUpdate(Request $request){
         // Validation 
         $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed', 
        ]);
        // Match The Old Password
        if (!Hash::check($request->old_password, auth::user()->password)) {
            return back()->with("error", "Old Password Doesn't Match!!");
        }
        // Update The new password 
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        return back()->with("status", " Password Changed Successfully");
    }// end of method


}
