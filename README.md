# Google Chat Logger

Monolog handler that posts Slack-style log cards (Message, Level, Context, footer) to a Google Chat webhook.

## Install

Add the repository to your app's `composer.json`:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/FBIS-Tech/google-chat-logger.git" }
]
```

```bash
composer require fbis-tech/google-chat-logger:^1.0
```

## Configure

In `config/logging.php`:

```php
'google_chat' => [
    'driver'  => 'monolog',
    'handler' => \FbisTech\GoogleChatLogger\GoogleChatHandler::class,
    'with'    => [
        'webhookUrl' => env('GOOGLE_CHAT_WEBHOOK_URL'),
    ],
    'level'   => env('LOG_LEVEL', 'error'),
],
```

Add `google_chat` to your `stack` channel, and set `GOOGLE_CHAT_WEBHOOK_URL` in `.env`.

The footer title is built from `APP_ENV` and `APP_NAME`.
