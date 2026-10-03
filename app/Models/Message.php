<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'body',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public static function sampleThreads(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Aye Chan',
                'preview' => 'Is the phone still available?',
                'time' => '2m ago',
                'unread' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Mya Lin',
                'preview' => 'The chair is still in good shape.',
                'time' => '1h ago',
                'unread' => 0,
            ],
            [
                'id' => 3,
                'name' => 'Htun Wai',
                'preview' => 'I can meet near downtown on Saturday.',
                'time' => 'Yesterday',
                'unread' => 0,
            ],
        ];
    }

    public static function sampleMessagesForThread(int $threadId): array
    {
        return [
            ['from' => 'seller', 'text' => 'Hi! The item is still available.'],
            ['from' => 'buyer', 'text' => 'Great. Can I ask about the condition?'],
            ['from' => 'seller', 'text' => 'It has only minor wear and the battery is healthy.'],
            ['from' => 'buyer', 'text' => 'Perfect. I will come by this evening.'],
        ];
    }

    public static function threadsForUser(?int $userId): array
    {
        if (! $userId) {
            return [];
        }

        if (! app('db')->getSchemaBuilder()->hasTable('messages')) {
            return self::sampleThreads();
        }

        $threads = self::query()
            ->with(['sender', 'receiver'])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        if ($threads->isEmpty()) {
            return [];
        }

        $result = [];
        foreach ($threads as $message) {
            $otherUserId = $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
            $otherUser = $message->sender_id === $userId ? $message->receiver : $message->sender;
            $threadKey = $otherUserId;

            if (! isset($result[$threadKey])) {
                $result[$threadKey] = [
                    'id' => $otherUserId,
                    'name' => $otherUser?->name ?? 'Seller',
                    'preview' => $message->body,
                    'time' => $message->created_at?->diffForHumans() ?? 'now',
                    'unread' => 0,
                ];
            }

            if ($message->receiver_id === $userId && $message->read_at === null) {
                $result[$threadKey]['unread']++;
            }
        }

        return array_values($result);
    }

    public static function messagesForThread(int $threadId, int $userId): array
    {
        if (! app('db')->getSchemaBuilder()->hasTable('messages')) {
            return self::sampleMessagesForThread($threadId);
        }

        $messages = self::query()
            ->where(function ($query) use ($threadId, $userId) {
                $query->where(function ($participantQuery) use ($threadId, $userId) {
                    $participantQuery->where('sender_id', $threadId)
                        ->where('receiver_id', $userId);
                })->orWhere(function ($participantQuery) use ($threadId, $userId) {
                    $participantQuery->where('sender_id', $userId)
                        ->where('receiver_id', $threadId);
                });
            })
            ->orderBy('created_at')
            ->get();

        return $messages->map(function (self $message) use ($userId) {
            return [
                'from' => $message->sender_id === $userId ? 'buyer' : 'seller',
                'text' => $message->body,
                'time' => $message->created_at?->format('g:i A'),
                'read' => $message->read_at !== null,
            ];
        })->all();
    }

    public static function markThreadAsRead(int $senderId, int $receiverId): void
    {
        self::query()
            ->where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
