<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Agent
    |--------------------------------------------------------------------------
    | Agent assigned to new customer chats when the API caller does not
    | specify an agent_id. Set this in your .env as CHAT_DEFAULT_AGENT_ID.
    */
    'default_agent_id' => env('CHAT_DEFAULT_AGENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Session lifetime (days)
    |--------------------------------------------------------------------------
    */
    'session_lifetime_days' => env('CHAT_SESSION_LIFETIME_DAYS', 30),
];