<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ $sellerProfile['name'] }} — Seller Profile | Marketly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#121826',
                        mist: '#F6F7FB',
                        cobalt: '#3157E5',
                        lime: '#DDF76F'
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
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-xl font-black">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-ink text-white">M</span>
                Marketly
            </a>
            <nav class="hidden items-center gap-6 text-sm font-semibold text-slate-600 md:flex">
                <a href="{{ route('dashboard') }}" class="hover:text-ink">Browse</a>
                <a href="{{ route('favorites') }}" class="hover:text-ink">Favorites</a>
                <a href="{{ route('messages') }}" class="hover:text-ink">Messages</a>
                <a href="{{ route('my-listings') }}" class="hover:text-ink">My Listings</a>
            </nav>
            <a href="{{ route('sell') }}" class="rounded-xl bg-ink px-4 py-2.5 text-sm font-bold text-white">+ Sell item</a>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-5 py-8">
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-500">← Back to marketplace</a>

        <section class="mt-5 overflow-hidden rounded-3xl bg-white shadow-soft">
            <div class="h-28"></div>
            <div class="px-6 pb-7 sm:px-8">
                <div class="-mt-10 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex items-end gap-4">
                        <span class="grid h-24 w-24 shrink-0 place-items-center rounded-3xl border-4 border-white bg-cobalt text-3xl font-black text-white shadow-soft" aria-hidden="true">{{ strtoupper(substr($sellerProfile['name'], 0, 1)) }}</span>
                        <div class="pb-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-2xl font-black">{{ $sellerProfile['name'] }}</h1>
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Marketly seller</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-500">{{ $sellerProfile['location'] }} · Member since {{ $sellerProfile['member_since'] }}</p>
                        </div>
                    </div>
                </div>

                @if ($isOwner)
                    <a href="{{ route('my-listings') }}" class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-ink px-5 py-4 text-base font-black text-white shadow-soft transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200">Manage my listings</a>
                @else
                    <a href="{{ route('messages.show', $sellerProfile['id']) }}" class="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-cobalt px-5 py-4 text-base font-black text-white shadow-soft transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z" />
                        </svg>
                        Send Message
                    </a>
                @endif

                <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl bg-mist p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Active listings</p>
                        <p class="mt-2 text-xl font-black">{{ $sellerProfile['active_listings'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-mist p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Sold</p>
                        <p class="mt-2 text-xl font-black">{{ $sellerProfile['sold_listings'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-mist p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Rating</p>
                        <p class="mt-2 text-xl font-black">Not rated</p>
                    </div>
                    <div class="rounded-2xl bg-mist p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Response</p>
                        <p class="mt-2 text-xl font-black">Not available</p>
                    </div>
                </div>

                <div class="mt-6 border-t border-slate-100 pt-5">
                    <h2 class="font-black">About seller</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Browse {{ $sellerProfile['name'] }}'s current marketplace listings. Send a message with questions about an item or to arrange a viewing.</p>
                </div>
            </div>
        </section>

        <section class="py-9">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-bold text-cobalt">Seller listings</p>
                    <h2 class="text-2xl font-black">Items from {{ $sellerProfile['name'] }}</h2>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-500">
                    <span class="sr-only">Sort listings</span>
                    <select id="listingSort" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-ink">
                        <option value="newest">Newest</option>
                        <option value="low">Price: low to high</option>
                        <option value="high">Price: high to low</option>
                    </select>
                </label>
            </div>

            @if (count($products))
                <div id="sellerProducts" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($products as $product)
                        @php
                            $isAvailable = ($product['listing_status'] ?? 'Available') === 'Available';
                        @endphp
                        <a href="{{ route('products.show', $product['slug']) }}" data-price="{{ $product['price'] }}" class="seller-product group overflow-hidden rounded-2xl bg-white shadow-soft">
                            <img class="h-56 w-full object-cover" src="{{ $product['image_url'] ?? $product['image'] }}" alt="{{ $product['name'] }}">
                            <div class="p-4">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-bold group-hover:text-cobalt">{{ $product['name'] }}</h3>
                                    @if ($isAvailable)
                                        <span class="shrink-0 rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-bold text-emerald-700">Available</span>
                                    @else
                                        <span class="shrink-0 rounded-full bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-600">Sold</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-slate-500">{{ $product['status'] }} · {{ $product['location'] }}</p>
                                <p class="mt-4 text-xl font-black">{{ number_format($product['price']) }} MMK</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center text-sm text-slate-500">This seller has no listings yet.</div>
            @endif
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl justify-between px-5 py-7 text-sm text-slate-500">
            <span>© {{ now()->year }} Marketly</span>
            <a href="{{ route('messages') }}" class="font-semibold text-cobalt">Messages</a>
        </div>
    </footer>

    <script>
        const listingSort = document.getElementById('listingSort');
        const sellerProducts = document.getElementById('sellerProducts');

        if (listingSort && sellerProducts) {
            listingSort.addEventListener('change', () => {
                const listings = [...sellerProducts.querySelectorAll('.seller-product')];
                listings.sort((left, right) => {
                    const leftPrice = Number(left.dataset.price);
                    const rightPrice = Number(right.dataset.price);

                    if (listingSort.value === 'low') {
                        return leftPrice - rightPrice;
                    }

                    if (listingSort.value === 'high') {
                        return rightPrice - leftPrice;
                    }

                    return 0;
                });

                listings.forEach((listing) => sellerProducts.append(listing));
            });
        }
    </script>
</body>
</html>
