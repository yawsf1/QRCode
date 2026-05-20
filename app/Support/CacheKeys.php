<?php
namespace App\Support;
class CacheKeys
{
    public static function adminDashboard(int $adminId): string
    {
        return "admin_dashboard_stats_{$adminId}";
    }
    public static function employeDashboard(int $employeId): string
    {
        return "employe_dashboard_stats_{$employeId}";
    }
    public static function superAdminDashboard(): string
    {
        return 'super_admin_dashboard_stats';
    }
    public static function userScannedToday(int $userId, string $date): string
    {
        return "user_scanned_{$userId}_{$date}";
    }
    public static function qrSession(int $adminId): string
    {
        return "qr_session_{$adminId}";
    }

    public static function registrationEmailVerify(string $email): string
    {
        return 'registration_email_verify_' . md5(strtolower(trim($email)));
    }
}
