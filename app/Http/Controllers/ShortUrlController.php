<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class ShortUrlController extends Controller
{
    public function redirect(string $code)
    {
        $short = ShortUrl::where('code', $code)->firstOrFail();

        return redirect($short->target_url);
    }
}