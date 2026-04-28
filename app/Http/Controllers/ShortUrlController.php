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

        if ($short->expires_at && $short->expires_at->isPast()) {
            abort(410, 'This link has expired.');
        }

        return redirect($short->target_url);
    }
}