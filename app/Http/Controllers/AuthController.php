<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function showLogin(){
        if(Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }
    public function login(Request $request){
        $creds=$request->validate(['email'=>['required','email'],'password'=>['required']]);
        if(Auth::attempt($creds,$request->boolean('remember'))){
            if(!Auth::user()->is_active){Auth::logout();return back()->withErrors(['email'=>'Akun Anda tidak aktif.']);}
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['email'=>'Email atau password tidak sesuai.'])->onlyInput('email');
    }
    public function logout(Request $request){
        Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();
        return redirect()->route('login')->with('success','Berhasil logout.');
    }
}
