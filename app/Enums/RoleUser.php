<?php
namespace App\Enums;
enum RoleUser: string {
    case Staff         = 'staff';
    case AdminDivisi   = 'admin_divisi';
    case Purchasing    = 'purchasing';
    case WakilDirektur = 'wakil_direktur';
    case Direktur      = 'direktur';
    case Superadmin    = 'superadmin';
    public function label(): string {
        return match($this) {
            self::Staff         => 'Staff',
            self::AdminDivisi   => 'Admin Divisi',
            self::Purchasing    => 'Purchasing',
            self::WakilDirektur => 'Wakil Direktur',
            self::Direktur      => 'Direktur',
            self::Superadmin    => 'Super Admin',
        };
    }
    public function canApprove(): bool {
        return in_array($this,[self::AdminDivisi,self::Purchasing,self::WakilDirektur,self::Direktur,self::Superadmin]);
    }
}
