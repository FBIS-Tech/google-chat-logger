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
        'username'   => strtoupper(env('APP_ENV', 'DEV') . '-' . env('APP_NAME') . ' LOG'),
    ],
    'level'   => env('LOG_LEVEL', 'error'),
],
```

Add `google_chat` to your `stack` channel, and set `GOOGLE_CHAT_WEBHOOK_URL` in `.env`.

`username` sets the name shown in each card's footer. If omitted, it defaults to `{APP_ENV}-{APP_NAME} LOG`.
