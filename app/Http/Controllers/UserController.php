<?php
namespace App\Http\Controllers;
use App\Enums\RoleUser;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller {
    public function index(Request $request){
        $users=User::with('divisi')
            ->when($request->search,fn($q)=>$q->where('name','like',"%{$request->search}%")->orWhere('email','like',"%{$request->search}%"))
            ->when($request->role,fn($q)=>$q->where('role',$request->role))
            ->when($request->divisi_id,fn($q)=>$q->where('divisi_id',$request->divisi_id))
            ->latest()->paginate(15)->withQueryString();
        $roles=RoleUser::cases();$divisis=Divisi::active()->get();
        return view('users.index',compact('users','roles','divisis'));
    }
    public function create(){$roles=RoleUser::cases();$divisis=Divisi::active()->get();return view('users.create',compact('roles','divisis'));}
    public function store(Request $request){
        $v=$request->validate(['name'=>'required|string|max:100','nip'=>'nullable|string|max:30|unique:users','email'=>'required|email|unique:users','password'=>'required|string|min:8|confirmed','role'=>'required|in:'.implode(',',array_column(RoleUser::cases(),'value')),'divisi_id'=>'nullable|exists:divisis,id','no_hp'=>'nullable|string|max:20','foto'=>'nullable|image|max:2048']);
        if($request->hasFile('foto'))$v['foto']=$request->file('foto')->store('users','public');
        $v['password']=Hash::make($v['password']);
        User::create($v);
        return redirect()->route('users.index')->with('success','Pengguna berhasil ditambahkan.');
    }
    public function edit(User $user){$roles=RoleUser::cases();$divisis=Divisi::active()->get();return view('users.edit',compact('user','roles','divisis'));}
    public function update(Request $request, User $user){
        $v=$request->validate(['name'=>'required|string|max:100','nip'=>'nullable|string|max:30|unique:users,nip,'.$user->id,'email'=>'required|email|unique:users,email,'.$user->id,'role'=>'required|in:'.implode(',',array_column(RoleUser::cases(),'value')),'divisi_id'=>'nullable|exists:divisis,id','no_hp'=>'nullable|string|max:20','is_active'=>'boolean','foto'=>'nullable|image|max:2048']);
        if($request->filled('password')){$request->validate(['password'=>'string|min:8|confirmed']);$v['password']=Hash::make($request->password);}
        if($request->hasFile('foto')){if($user->foto)Storage::disk('public')->delete($user->foto);$v['foto']=$request->file('foto')->store('users','public');}
        $v['is_active']=$request->boolean('is_active',true);
        $user->update($v);
        return redirect()->route('users.index')->with('success','Data pengguna berhasil diperbarui.');
    }
    public function destroy(User $user){
        if($user->id===auth()->id())return back()->with('error','Tidak dapat menghapus akun sendiri.');
        $user->update(['is_active'=>false]);
        return redirect()->route('users.index')->with('success','Pengguna berhasil dinonaktifkan.');
    }
}
