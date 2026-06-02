<?php
namespace App\Enums;
enum StatusPengajuan: string {
    case Draft            = 'draft';
    case Diajukan         = 'diajukan';
    case ReviewAdmin      = 'review_admin';
    case Diteruskan       = 'diteruskan';
    case ProsesPurchasing = 'proses_purchasing';
    case MenungguApproval = 'menunggu_approval';
    case Disetujui        = 'disetujui';
    case Ditolak          = 'ditolak';
    case Selesai          = 'selesai';
    public function label(): string {
        return match($this) {
            self::Draft            => 'Draft',
            self::Diajukan         => 'Diajukan',
            self::ReviewAdmin      => 'Review Admin',
            self::Diteruskan       => 'Diteruskan',
            self::ProsesPurchasing => 'Proses Purchasing',
            self::MenungguApproval => 'Menunggu Approval',
            self::Disetujui        => 'Disetujui',
            self::Ditolak          => 'Ditolak',
            self::Selesai          => 'Selesai',
        };
    }
    public function badgeClass(): string {
        return match($this) {
            self::Draft            => 'badge-secondary',
            self::Diajukan         => 'badge-info',
            self::ReviewAdmin,
            self::Diteruskan,
            self::ProsesPurchasing,
            self::MenungguApproval => 'badge-warning',
            self::Disetujui        => 'badge-success',
            self::Ditolak          => 'badge-danger',
            self::Selesai          => 'badge-dark',
        };
    }
    public function isFinal(): bool {
        return in_array($this,[self::Disetujui,self::Ditolak,self::Selesai]);
    }
}
