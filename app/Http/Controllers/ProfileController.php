<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller {
    public function edit(){$user=Auth::user();return view('profile.edit',compact('user'));}
    public function update(Request $request){
        $user=Auth::user();
        $v=$request->validate(['name'=>'required|string|max:100','nip'=>'nullable|string|max:30|unique:users,nip,'.$user->id,'no_hp'=>'nullable|string|max:20','foto'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:2048']);
        if($request->hasFile('foto')){if($user->foto)Storage::disk('public')->delete($user->foto);$v['foto']=$request->file('foto')->store('users','public');}
        $user->update($v);
        return back()->with('success','Profil berhasil diperbarui.');
    }
    public function updatePassword(Request $request){
        $request->validate(['current_password'=>['required','current_password'],'password'=>['required','confirmed','min:8']]);
        Auth::user()->update(['password'=>Hash::make($request->password)]);
        return back()->with('success','Password berhasil diperbarui.');
    }
}
