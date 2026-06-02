<?php
namespace App\Services;
use App\Models\Pengajuan;
use App\Models\User;

class PengajuanService {
    public function getSummary(?User $user=null):array {
        $base=Pengajuan::query();
        if($user&&$user->isStaff()) $base->where('user_id',$user->id);
        elseif($user&&$user->isAdminDivisi()) $base->where('divisi_id',$user->divisi_id);
        return [
            'total'=>(clone $base)->count(),
            'draft'=>(clone $base)->where('status','draft')->count(),
            'menunggu'=>(clone $base)->whereIn('status',['diajukan','review_admin','diteruskan','proses_purchasing','menunggu_approval'])->count(),
            'disetujui'=>(clone $base)->where('status','disetujui')->count(),
            'ditolak'=>(clone $base)->where('status','ditolak')->count(),
            'bulan_ini'=>(clone $base)->whereMonth('tanggal_pengajuan',now()->month)->whereYear('tanggal_pengajuan',now()->year)->count(),
            'nilai_bulan_ini'=>(clone $base)->whereMonth('tanggal_pengajuan',now()->month)->whereYear('tanggal_pengajuan',now()->year)->sum('total_nilai'),
        ];
    }
}
