<?php
namespace App\Http\Controllers;
use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller {
    public function index(Request $request){
        $divisis=Divisi::withCount(['users','pengajuans'])
            ->when($request->search,fn($q)=>$q->where('nama_divisi','like',"%{$request->search}%")->orWhere('kode_divisi','like',"%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();
        return view('divisi.index',compact('divisis'));
    }
    public function create(){return view('divisi.create');}
    public function store(Request $request){
        $v=$request->validate(['nama_divisi'=>'required|string|max:100','kode_divisi'=>'required|string|max:20|unique:divisis','deskripsi'=>'nullable|string']);
        $v['is_active']=$request->boolean('is_active',true);
        Divisi::create($v);
        return redirect()->route('divisi.index')->with('success','Divisi berhasil ditambahkan.');
    }
    public function show(Divisi $divisi){
        $divisi->loadCount(['users','pengajuans']);
        $divisi->load(['users'=>fn($q)=>$q->limit(10)]);
        return view('divisi.show',compact('divisi'));
    }
    public function edit(Divisi $divisi){return view('divisi.edit',compact('divisi'));}
    public function update(Request $request, Divisi $divisi){
        $v=$request->validate(['nama_divisi'=>'required|string|max:100','kode_divisi'=>'required|string|max:20|unique:divisis,kode_divisi,'.$divisi->id,'deskripsi'=>'nullable|string']);
        $v['is_active']=$request->boolean('is_active',true);
        $divisi->update($v);
        return redirect()->route('divisi.index')->with('success','Divisi berhasil diperbarui.');
    }
    public function destroy(Divisi $divisi){
        if($divisi->users()->count()>0) return back()->with('error','Tidak bisa menghapus divisi yang masih memiliki anggota.');
        $divisi->delete();
        return redirect()->route('divisi.index')->with('success','Divisi berhasil dihapus.');
    }
}
