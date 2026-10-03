<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>My Listings — Marketly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#121826',
                        mist: '#F6F7FB',
                        cobalt: '#3157E5'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-mist text-ink">
    <header class="border-b bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
            <a href="{{ route('dashboard') }}" class="text-xl font-black">Marketly</a>
            <a href="{{ route('sell') }}" class="rounded-xl bg-ink px-4 py-2.5 text-sm font-bold text-white">+ Sell item</a>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-5 py-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-cobalt">Seller area</p>
                <h1 class="text-3xl font-black">My Listings</h1>
                <p class="mt-2 text-slate-500">Manage everything you're selling in one place.</p>
            </div>

            <div class="flex gap-2">
                @foreach (['all' => 'All', 'available' => 'Available', 'sold' => 'Sold'] as $filter => $label)
                    <a href="{{ route('my-listings', ['status' => $filter]) }}" class="rounded-full px-4 py-2 text-sm font-bold {{ $statusFilter === $filter ? 'bg-ink text-white' : 'bg-white text-slate-700' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <div class="mt-8 overflow-hidden rounded-3xl bg-white shadow-sm">
            <div class="hidden grid-cols-[1.5fr_.6fr_.5fr_.6fr] gap-4 border-b px-5 py-3 text-xs font-bold uppercase tracking-wide text-slate-400 md:grid">
                <span>Listing</span>
                <span>Price</span>
                <span>Status</span>
                <span>Actions</span>
            </div>

            <div class="divide-y">
                @foreach ($products as $product)
                    @php
                        $isSold = ($product['listing_status'] ?? 'Available') === 'Sold';
                        $statusLabel = $isSold ? 'Sold' : 'Available';
                        $statusClasses = $isSold
                            ? 'w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600'
                            : 'w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700';
                    @endphp

                    <div class="grid gap-4 p-5 md:grid-cols-[1.5fr_.6fr_.5fr_.6fr] md:items-center">
                        <div class="flex gap-4">
                            <img class="h-20 w-20 rounded-2xl object-cover" src="{{ $product['image_url'] ?? $product['image'] ?? 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=300&q=80' }}" alt="{{ $product['name'] }}">
                            <div>
                                <p class="font-bold">{{ $product['name'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ ucfirst($product['category'] ?? 'General') }} · {{ $product['views'] ?? 0 }} views</p>
                            </div>
                        </div>

                        <p class="font-bold">{{ number_format((int) ($product['price'] ?? 0)) }} MMK</p>

                        <span class="{{ $statusClasses }}">{{ $statusLabel }}</span>

                        <div class="flex gap-2">
                            <a href="{{ route('listings.edit', $product['id']) }}" class="rounded-lg border px-3 py-2 text-sm font-bold">Edit</a>
                            @if ($isSold)
                                <form action="{{ route('listings.destroy', $product['id']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border px-3 py-2 text-sm font-bold">Delete</button>
                                </form>
                            @else
                                <form action="{{ route('listings.status', $product['id']) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg border px-3 py-2 text-sm font-bold">Mark sold</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if (empty($products))
                    <p class="px-5 py-10 text-center text-sm text-slate-500">No listings found for this filter.</p>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
