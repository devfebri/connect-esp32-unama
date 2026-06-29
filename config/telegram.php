<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    |
    | Token bot Telegram yang digunakan untuk mengirim notifikasi ke grup.
    | Atur nilai ini di file .env dengan key TELEGRAM_BOT_TOKEN.
    |
    */
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Telegram Chat ID (Grup)
    |--------------------------------------------------------------------------
    |
    | ID grup Telegram tujuan pengiriman notifikasi offline.
    | Atur nilai ini di file .env dengan key TELEGRAM_CHAT_ID.
    |
    */
    'chat_id' => env('TELEGRAM_CHAT_ID', '-5526968660'),
];
