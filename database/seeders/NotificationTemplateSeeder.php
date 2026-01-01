<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // Admin facing
            ['event_key' => 'user_registered', 'channel' => 'site', 'title_template' => 'ثبت‌نام جدید', 'body_template' => 'کاربر {{user}} ثبت‌نام خود را تکمیل کرد.', 'sms_template_id' => null],
            ['event_key' => 'payment_created', 'channel' => 'site', 'title_template' => 'پرداخت جدید', 'body_template' => 'پرداخت جدید به مبلغ {{amount}} برای {{context}} ثبت شد.', 'sms_template_id' => null],
            ['event_key' => 'payment_verified', 'channel' => 'site', 'title_template' => 'پرداخت نیازمند بررسی', 'body_template' => 'پرداخت {{amount}} برای {{context}} تایید اولیه شد.', 'sms_template_id' => null],
            ['event_key' => 'ticket_created', 'channel' => 'site', 'title_template' => 'تیکت جدید', 'body_template' => 'تیکت جدیدی توسط {{user}} ایجاد شد.', 'sms_template_id' => null],

            // User facing - site
            ['event_key' => 'registration_approved', 'channel' => 'site', 'title_template' => 'عضویت تایید شد', 'body_template' => 'عضویت شما تایید شد. به باشگاه خوش آمدید.', 'sms_template_id' => null],
            ['event_key' => 'registration_rejected', 'channel' => 'site', 'title_template' => 'عضویت رد شد', 'body_template' => 'متاسفانه عضویت شما رد شد. لطفاً با باشگاه تماس بگیرید.', 'sms_template_id' => null],
            ['event_key' => 'payment_approved', 'channel' => 'site', 'title_template' => 'پرداخت تایید شد', 'body_template' => 'پرداخت شما برای {{context}} تایید شد.', 'sms_template_id' => null],
            ['event_key' => 'payment_rejected', 'channel' => 'site', 'title_template' => 'پرداخت رد شد', 'body_template' => 'پرداخت شما رد شد. لطفاً جزئیات را بررسی کنید.', 'sms_template_id' => null],
            ['event_key' => 'enrollment_approved', 'channel' => 'site', 'title_template' => 'ثبت‌نام برنامه/دوره تایید شد', 'body_template' => 'ثبت‌نام شما برای {{context}} تایید شد.', 'sms_template_id' => null],
            ['event_key' => 'enrollment_rejected', 'channel' => 'site', 'title_template' => 'ثبت‌نام رد شد', 'body_template' => 'ثبت‌نام شما برای {{context}} رد شد.', 'sms_template_id' => null],
            ['event_key' => 'ticket_replied', 'channel' => 'site', 'title_template' => 'پاسخ به تیکت', 'body_template' => 'به تیکت شما پاسخ داده شد.', 'sms_template_id' => null],

            // SMS placeholders (template IDs are examples; replace with real IDs in production)
            ['event_key' => 'registration_approved', 'channel' => 'sms', 'title_template' => null, 'body_template' => 'عضویت شما تایید شد.', 'sms_template_id' => 1001],
            ['event_key' => 'registration_rejected', 'channel' => 'sms', 'title_template' => null, 'body_template' => 'عضویت شما رد شد.', 'sms_template_id' => 1002],
            ['event_key' => 'payment_approved', 'channel' => 'sms', 'title_template' => null, 'body_template' => 'پرداخت تایید شد.', 'sms_template_id' => 1003],
            ['event_key' => 'payment_rejected', 'channel' => 'sms', 'title_template' => null, 'body_template' => 'پرداخت رد شد.', 'sms_template_id' => 1004],
            ['event_key' => 'enrollment_approved', 'channel' => 'sms', 'title_template' => null, 'body_template' => 'ثبت‌نام تایید شد.', 'sms_template_id' => 1005],
            ['event_key' => 'enrollment_rejected', 'channel' => 'sms', 'title_template' => null, 'body_template' => 'ثبت‌نام رد شد.', 'sms_template_id' => 1006],
            ['event_key' => 'ticket_replied', 'channel' => 'sms', 'title_template' => null, 'body_template' => 'پاسخ جدید برای تیکت.', 'sms_template_id' => 1007],
        ];

        foreach ($templates as $tpl) {
            NotificationTemplate::updateOrCreate(
                ['event_key' => $tpl['event_key'], 'channel' => $tpl['channel']],
                $tpl
            );
        }
    }
}
