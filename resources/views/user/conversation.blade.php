<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Conversation with {{ $conversation['name'] }} — Marketly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#121826',
                        mist: '#F6F7FB',
                        cobalt: '#3157E5'
                    },
                    boxShadow: {
                        soft: '0 12px 40px rgba(18,24,38,.08)'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-mist text-ink">
    <header class="border-b bg-white">
        <div class="mx-auto grid max-w-5xl grid-cols-3 items-center px-5 py-4">
            <a href="{{ route('messages') }}" class="text-sm font-bold">← Messages</a>
            <a href="{{ route('dashboard') }}" class="text-center font-black">Marketly</a>
            @if (! empty($listing))
                <a href="{{ route('products.show', $listing['slug']) }}" class="text-right text-sm font-bold text-cobalt">View listing</a>
            @else
                <span></span>
            @endif
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-5 py-6">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_280px]">
            <section class="flex h-[76vh] min-h-[480px] flex-col overflow-hidden rounded-3xl bg-white shadow-soft">
                <div class="flex items-center justify-between gap-3 border-b p-4">
                    <a href="{{ route('seller.profile', $seller['id']) }}" class="flex min-w-0 items-center gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-cobalt/10 font-bold text-cobalt" aria-hidden="true">{{ strtoupper(substr($conversation['name'], 0, 1)) }}</span>
                        <span class="min-w-0">
                            <span class="block truncate font-bold">{{ $conversation['name'] }}</span>
                            <span class="block truncate text-sm text-slate-500">{{ $conversation['status'] }} · View seller profile</span>
                        </span>
                    </a>
                </div>

                <div id="chatBody" class="flex-1 space-y-4 overflow-y-auto bg-slate-50 p-5">
                    <p class="text-center text-xs font-semibold text-slate-400">{{ now()->format('F j') }}</p>
                    @forelse ($messages as $message)
                        @php
                            $isCurrentUser = ($message['from'] ?? '') === 'buyer';
                        @endphp
                        <div class="{{ $isCurrentUser ? 'ml-auto' : '' }} max-w-[78%]">
                            <div class="rounded-2xl {{ $isCurrentUser ? 'rounded-tr-sm bg-cobalt text-white' : 'rounded-tl-sm bg-white text-ink shadow-sm' }} p-3 text-sm">{{ $message['text'] }}</div>
                            @if (! empty($message['time']))
                                <p class="mt-1 {{ $isCurrentUser ? 'pr-1 text-right' : 'pl-1' }} text-[11px] text-slate-400">{{ $message['time'] }}{{ $isCurrentUser && ! empty($message['read']) ? ' · Seen' : '' }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="py-10 text-center text-sm text-slate-500">Start the conversation by sending a message.</p>
                    @endforelse
                </div>

                <form id="messageForm" action="{{ route('messages.send', $conversation['id']) }}" method="POST" class="border-t bg-white p-4">
                    @csrf
                    <div class="flex items-end gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-2 focus-within:border-cobalt">
                        <button type="button" aria-label="Add attachment" title="Attachments are not available yet" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-lg text-slate-500 hover:bg-white">＋</button>
                        <textarea id="messageInput" name="body" rows="1" class="max-h-28 min-w-0 flex-1 resize-none bg-transparent px-2 py-2.5 text-sm outline-none" placeholder="Type a message..." aria-label="Type a message" required>{{ old('body') }}</textarea>
                        <button type="submit" class="rounded-xl bg-ink px-4 py-2.5 text-sm font-bold text-white">Send</button>
                    </div>
                    @error('body')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </form>
            </section>

            <aside class="h-fit">
                @if (! empty($listing))
                    <a href="{{ route('products.show', $listing['slug']) }}" class="block overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-soft">
                        <img class="h-40 w-full object-cover" src="{{ $listing['image_url'] ?? $listing['image'] }}" alt="{{ $listing['name'] }}">
                        <div class="p-4">
                            <p class="text-xs font-bold text-cobalt">Seller listing</p>
                            <h2 class="mt-1 font-black">{{ $listing['name'] }}</h2>
                            <p class="mt-2 text-lg font-black">{{ number_format($listing['price']) }} MMK</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $listing['status'] }} · {{ $listing['location'] }}</p>
                        </div>
                    </a>
                @endif
                <div class="{{ ! empty($listing) ? 'mt-4' : '' }} rounded-2xl bg-white p-4 text-xs leading-5 text-slate-600 shadow-soft"><strong class="text-ink">Safety tip:</strong> Meet in a public place and inspect the item before paying.</div>
            </aside>
        </div>
    </main>

    <script>
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const chatBody = document.getElementById('chatBody');

        messageInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                messageForm.requestSubmit();
            }
        });

        chatBody.scrollTop = chatBody.scrollHeight;
    </script>
</body>
</html>
