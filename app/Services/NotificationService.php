<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SmsIr\SmsIr;

class NotificationService
{
    /**
     * Dispatch a notification for an event to a recipient.
     *
     * @param string $eventKey
     * @param \Illuminate\Database\Eloquent\Model $recipient (expected: User)
     * @param array $data key/value pairs used for template rendering (e.g., ['user' => 'علی', 'program' => 'دماوند', 'url' => '...'])
     */
    public function notify(string $eventKey, Model $recipient, array $data = []): void
    {
        try {
            // Resolve site template
            $siteTemplate = NotificationTemplate::active()
                ->where('event_key', $eventKey)
                ->where('channel', 'site')
                ->first();

            $title = $this->renderTemplate($siteTemplate->title_template ?? ($data['title'] ?? $eventKey), $data);
            $message = $this->renderTemplate($siteTemplate->body_template ?? ($data['message'] ?? ''), $data);

            Notification::create([
                'notifiable_id' => $recipient->getKey(),
                'notifiable_type' => get_class($recipient),
                'event_key' => $eventKey,
                'title' => $title,
                'message' => $message,
                'data' => $data ?: null,
            ]);

            // Resolve SMS template and send if allowed
            $smsTemplate = NotificationTemplate::active()
                ->where('event_key', $eventKey)
                ->where('channel', 'sms')
                ->first();

            if ($smsTemplate && $smsTemplate->sms_template_id && $this->shouldSendSms()) {
                $mobile = $recipient->phone ?? $recipient->mobile ?? null;
                if ($mobile) {
                    $parameters = $this->buildSmsParameters($data);
                    SmsIr::verifySend($mobile, (int) $smsTemplate->sms_template_id, $parameters);
                }
            }
        } catch (\Throwable $e) {
            Log::error('NotificationService failure', [
                'event_key' => $eventKey,
                'recipient_type' => get_class($recipient),
                'recipient_id' => $recipient->getKey(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function renderTemplate(string $template, array $data): string
    {
        $placeholders = collect($data)->mapWithKeys(function ($value, $key) {
            return ["{{{$key}}}" => $value, ":$key" => $value];
        })->toArray();

        return trim(strtr($template, $placeholders));
    }

    protected function buildSmsParameters(array $data): array
    {
        return collect($data)->map(function ($value, $key) {
            return ['name' => Str::headline($key), 'value' => (string) $value];
        })->values()->toArray();
    }

    protected function shouldSendSms(): bool
    {
        return config('app.env') !== 'local';
    }
}
