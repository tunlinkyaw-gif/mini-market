<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Messages — Marketly</title>
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
    <header class="sticky top-0 z-40 border-b bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-xl font-black">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-ink text-white">M</span>
                Marketly
            </a>
            <nav class="flex items-center gap-4 text-sm font-bold">
                <a href="{{ route('dashboard') }}" class="hover:text-cobalt">Browse</a>
                <a href="{{ route('my-listings') }}" class="hover:text-cobalt">My Listings</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-5 py-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-cobalt">Inbox</p>
                <h1 class="text-3xl font-black">Messages</h1>
                <p class="mt-2 text-slate-500">Conversations about your marketplace listings.</p>
            </div>
            <div class="w-full sm:w-72">
                <label for="messageSearch" class="sr-only">Search messages</label>
                <input id="messageSearch" type="search" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-cobalt" placeholder="Search messages..." autocomplete="off">
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-3xl bg-white shadow-soft" id="conversationList">
            @forelse ($threads as $thread)
                @php
                    $searchText = strtolower(($thread['name'] ?? '').' '.($thread['preview'] ?? '').' '.($thread['listing'] ?? ''));
                @endphp
                <a href="{{ route('messages.show', $thread['id']) }}" data-search="{{ $searchText }}" class="conversation-row flex items-center gap-4 border-b border-slate-100 p-5 transition-colors last:border-b-0 hover:bg-slate-50">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-cobalt/10 font-bold text-cobalt" aria-hidden="true">
                        {{ strtoupper(substr($thread['name'] ?? 'U', 0, 1)) }}
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center justify-between gap-3">
                            <span class="truncate font-bold">{{ $thread['name'] ?? 'Marketplace member' }}</span>
                            <span class="shrink-0 text-xs text-slate-400">{{ $thread['time'] ?? '' }}</span>
                        </span>
                        <span class="mt-1 block truncate text-sm text-slate-500">{{ $thread['preview'] ?? '' }}</span>
                        @if (! empty($thread['listing']))
                            <span class="mt-2 block truncate text-xs font-semibold text-cobalt">{{ $thread['listing'] }}</span>
                        @endif
                    </span>
                    @if (! empty($thread['unread']))
                        <span class="grid h-5 min-w-5 shrink-0 place-items-center rounded-full bg-cobalt px-1 text-[10px] font-bold text-white">{{ $thread['unread'] }}</span>
                    @endif
                </a>
            @empty
                <p class="px-5 py-12 text-center text-sm text-slate-500">No conversations yet.</p>
            @endforelse
        </div>

        <div id="emptyMessages" class="mt-6 hidden rounded-3xl border border-dashed border-slate-300 bg-white py-14 text-center text-slate-500">No conversations match your search.</div>
    </main>

    <script>
        const searchInput = document.getElementById('messageSearch');
        const conversationRows = [...document.querySelectorAll('.conversation-row')];
        const emptyMessages = document.getElementById('emptyMessages');

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            conversationRows.forEach((row) => {
                const isVisible = !query || row.dataset.search.includes(query);
                row.style.display = isVisible ? 'flex' : 'none';

                if (isVisible) {
                    visibleCount += 1;
                }
            });

            emptyMessages.classList.toggle('hidden', visibleCount !== 0 || conversationRows.length === 0);
        });
    </script>
</body>
</html>
