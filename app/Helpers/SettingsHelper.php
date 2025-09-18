<?php

namespace App\Helpers;

class SettingsHelper
{
    /**
     * Company settings
     */
    public static function companyName()
    {
        return 'PT. PWA Mobile System';
    }

    public static function companyAddress()
    {
        return 'Jl. Contoh Alamat No. 123, Jakarta 12345';
    }

    public static function companyPhone()
    {
        return '(021) 1234-5678';
    }

    public static function companyEmail()
    {
        return 'info@pwa-mobile.com';
    }

    public static function companyWebsite()
    {
        return 'https://www.pwa-mobile.com';
    }

    /**
     * Export settings
     */
    public static function exportHeaderTitle()
    {
        return 'LAPORAN PURCHASE ORDER';
    }

    public static function exportFooterText()
    {
        return 'Dicetak pada: {date} | Halaman: {page}';
    }

    public static function exportLogoUrl()
    {
        return '';
    }

    /**
     * System settings
     */
    public static function appName()
    {
        return 'PWA Mobile System';
    }

    public static function appDescription()
    {
        return 'Sistem Manajemen Purchase Order Terintegrasi';
    }

    public static function timezone()
    {
        return 'Asia/Jakarta';
    }

    public static function currency()
    {
        return 'IDR';
    }

    public static function currencySymbol()
    {
        return 'Rp';
    }
}
