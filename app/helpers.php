<?php

use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

if (! function_exists('toPersianDate')) {
    function toPersianDate($date)
    {
        if (! $date) return '';
        try {
            return Jalalian::fromCarbon(Carbon::parse($date))->format('Y/m/d');
        } catch (\Throwable $e) {
            return '';
        }
    }
}

if (! function_exists('en_digits')) {
    function en_digits($string)
    {
        $map = ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
                '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9'];
        return strtr((string)$string, $map);
    }
}

if (! function_exists('fa_digits')) {
    function fa_digits($string)
    {
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        $fa = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        return str_replace($en, $fa, (string)$string);
    }
}

// Backward compatibility
if (! function_exists('toPersianNumber')) {
    function toPersianNumber($string) { return fa_digits($string); }
}

if (! function_exists('ticket_status_badge')) {
    function ticket_status_badge(string $status, ?string $lastReplyBy = null): string
    {
        $map = [
            'open' => ['text' => 'باز', 'class' => 'bg-success'],
            'waiting_admin' => ['text' => 'در انتظار ادمین', 'class' => 'bg-warning text-dark'],
            'waiting_user' => ['text' => 'در انتظار شما', 'class' => 'bg-info text-dark'],
            'closed' => ['text' => 'بسته', 'class' => 'bg-secondary'],
        ];

        $item = $map[$status] ?? $map['open'];
        $suffix = '';
        if ($lastReplyBy === 'admin') {
            $suffix = ' (آخرین پاسخ: ادمین)';
        } elseif ($lastReplyBy === 'user') {
            $suffix = ' (آخرین پاسخ: کاربر)';
        }

        return '<span class="badge '.$item['class'].'">'.$item['text'].$suffix.'</span>';
    }
}