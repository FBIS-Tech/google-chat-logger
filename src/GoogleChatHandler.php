<?php

namespace FbisTech\GoogleChatLogger;

use Illuminate\Support\Facades\Http;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class GoogleChatHandler extends AbstractProcessingHandler
{
    protected string $webhookUrl;

    // Same colour scheme Slack uses for attachment bars
    protected const LEVEL_COLORS = [
        'DEBUG'     => '#808080',
        'INFO'      => '#2EB67D',
        'NOTICE'    => '#2EB67D',
        'WARNING'   => '#ECB22E',
        'ERROR'     => '#E01E5A',
        'CRITICAL'  => '#E01E5A',
        'ALERT'     => '#E01E5A',
        'EMERGENCY' => '#E01E5A',
    ];

    public function __construct(string $webhookUrl, int|string|Level $level = Level::Error, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->webhookUrl = $webhookUrl;
    }

    protected function write(LogRecord $record): void
    {
        if (empty($this->webhookUrl)) {
            return;
        }

        $title = strtoupper(config('app.env') . '-' . config('app.name') . ' LOG');
        $level = $record->level->getName();
        $color = self::LEVEL_COLORS[$level] ?? '#E01E5A';

        $widgets = [
            $this->field('Message', e(mb_strimwidth($record->message, 0, 3000, '...'))),
            $this->field('Level', "<font color=\"{$color}\"><b>{$level}</b></font>"),
        ];

        if (!empty($record->context)) {
            $context = mb_strimwidth(
                json_encode($record->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR),
                0,
                3000,
                '...'
            );

            $widgets[] = $this->field('Context', '<pre>' . e($context) . '</pre>');
        }

        $widgets[] = [
            'textParagraph' => [
                'text' => "<font color=\"#808080\">{$title} | {$record->datetime->format('M jS, g:i A')}</font>",
            ],
        ];

        try {
            Http::timeout(3)->post($this->webhookUrl, [
                'cardsV2' => [[
                    'cardId' => 'log-' . uniqid(),
                    'card'   => [
                        'sections' => [['widgets' => $widgets]],
                    ],
                ]],
            ]);
        } catch (\Throwable) {
            // Silently ignore webhook failures so logging never crashes the app
        }
    }

    protected function field(string $label, string $value): array
    {
        return [
            'textParagraph' => [
                'text' => "<b>{$label}</b><br>{$value}",
            ],
        ];
    }
}
