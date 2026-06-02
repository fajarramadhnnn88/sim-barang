<?php
namespace App\Models;
use App\Enums\RoleUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    protected $fillable = ['name','nip','email','password','role','divisi_id','no_hp','foto','is_active'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['role'=>RoleUser::class,'is_active'=>'boolean','email_verified_at'=>'datetime','password'=>'hashed'];

    public function divisi(): BelongsTo { return $this->belongsTo(Divisi::class); }
    public function pengajuans(): HasMany { return $this->hasMany(Pengajuan::class); }
    public function approvalLogs(): HasMany { return $this->hasMany(ApprovalLog::class); }
    public function barangMasuks(): HasMany { return $this->hasMany(BarangMasuk::class); }
    public function barangKeluars(): HasMany { return $this->hasMany(BarangKeluar::class); }

    public function isStaff(): bool         { return $this->role === RoleUser::Staff; }
    public function isAdminDivisi(): bool   { return $this->role === RoleUser::AdminDivisi; }
    public function isPurchasing(): bool    { return $this->role === RoleUser::Purchasing; }
    public function isWakilDirektur(): bool { return $this->role === RoleUser::WakilDirektur; }
    public function isDirektur(): bool      { return $this->role === RoleUser::Direktur; }
    public function isSuperadmin(): bool    { return $this->role === RoleUser::Superadmin; }
    public function canApprove(): bool      { return $this->role->canApprove(); }

    public function getFotoUrlAttribute(): string {
        return $this->foto ? asset('storage/'.$this->foto) : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=4F46E5&color=fff&size=128';
    }
    public function scopeActive($q) { return $q->where('is_active',true); }
}
