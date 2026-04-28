<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitrixService
{
    protected string $baseUrl;

    public function __construct()
    {
        // Store in config/services.php or .env
        $this->baseUrl =
            'https://axisuae.bitrix24.com/rest/5266/kbee4fdpvsj1uxp4/crm.timeline.comment.add.json';
    }

    /**
     * Add a timeline comment to a Bitrix24 deal.
     *
     * @param  int|string  $dealId
     * @param  string      $comment
     * @return bool
     */
    public function addDealComment(int|string $dealId, string $comment): bool
    {
        if (!$dealId) return false;

        try {
            $response = Http::timeout(8)->post(
                $this->baseUrl . 'crm.timeline.comment.add.json',
                [
                    'fields' => [
                        'COMMENT'     => $comment,
                        'ENTITY_ID'   => (string) $dealId,
                        'ENTITY_TYPE' => 'deal',
                    ],
                ]
            );

            if (!$response->successful()) {
                Log::warning('Bitrix comment failed', [
                    'deal_id'  => $dealId,
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Bitrix comment exception', [
                'deal_id' => $dealId,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Format a chat message for Bitrix comment.
     * Labels: [Agent] or [Customer]
     */
    public static function formatMessageComment(
        string $senderName,
        string $senderRole,
        string $body,
        array  $attachments = []
    ): string {
        $roleLabel = match ($senderRole) {
            'agent'  => '[Agent]',
            'admin'  => '[Agent]',
            default  => '[Customer]',
        };

        $text = "{$roleLabel} {$senderName}: {$body}";

        if (!empty($attachments)) {
            $fileNames = implode(', ', array_column($attachments, 'original_name'));
            $text .= "\n📎 Attachments: {$fileNames}";
        }

        return $text;
    }
}