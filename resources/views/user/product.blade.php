<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ $product['name'] }} — Marketly</title>
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
    @php
        $productImage = $product['image_url'] ?? $product['image'];
        $productImages = $product['gallery_urls'] ?? [$productImage];
        $productImages = $productImages ?: [$productImage];
        $postedDate = ! empty($product['created_at'])
            ? \Illuminate\Support\Carbon::parse($product['created_at'])->format('M j, Y')
            : 'Featured listing';
    @endphp

    <header class="border-b bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-xl font-black">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-ink text-white">M</span>
                Marketly
            </a>
            <div class="flex gap-3">
                <a href="{{ route('favorites') }}" class="rounded-xl border bg-white px-4 py-2 text-sm font-bold">♡ Favorites</a>
                <a href="{{ route('sell') }}" class="rounded-xl bg-ink px-4 py-2 text-sm font-bold text-white">+ Sell item</a>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-5 py-8">
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-500">← Back to marketplace</a>

        <div class="mt-5 grid gap-8 lg:grid-cols-[1.2fr_.8fr]">
            <div>
                <img id="mainImage" class="h-[420px] w-full rounded-3xl object-cover sm:h-[520px]" src="{{ $productImages[0] }}" alt="{{ $product['name'] }}">
                @if (count($productImages) > 1)
                    <div class="mt-3 grid grid-cols-4 gap-3">
                        @foreach ($productImages as $galleryImage)
                            <button type="button" data-image="{{ $galleryImage }}" aria-label="View product photo {{ $loop->iteration }}" class="gallery-thumb overflow-hidden rounded-xl border-2 {{ $loop->first ? 'border-ink' : 'border-transparent' }}">
                                <img class="h-20 w-full object-cover sm:h-24" src="{{ $galleryImage }}" alt="{{ $product['name'] }} photo {{ $loop->iteration }}">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="space-y-5">
                <section class="rounded-3xl bg-white p-6 shadow-soft">
                    <div class="flex items-center justify-between">
                        <span class="rounded-full bg-lime px-3 py-1 text-xs font-bold">{{ $product['status'] ?? 'Used' }}</span>
                        @if ($canFavorite)
                            <form action="{{ route('favorites.toggle', $product['slug']) }}" method="POST">
                                @csrf
                                <button type="submit" aria-label="{{ $isFavorite ? 'Remove from favorites' : 'Add to favorites' }}" aria-pressed="{{ $isFavorite ? 'true' : 'false' }}" class="grid h-11 w-11 place-items-center rounded-full border text-xl {{ $isFavorite ? 'text-red-500' : '' }}">{{ $isFavorite ? '♥' : '♡' }}</button>
                            </form>
                        @else
                            <button type="button" aria-label="Favorite unavailable for demo listing" disabled class="grid h-11 w-11 place-items-center rounded-full border text-xl text-slate-300">♡</button>
                        @endif
                    </div>

                    <h1 class="mt-5 text-3xl font-black">{{ $product['name'] }}</h1>
                    <p class="mt-2 text-slate-500">{{ ucfirst($product['category']) }} · {{ $product['location'] }}</p>
                    <p class="mt-6 text-3xl font-black">{{ number_format($product['price']) }} MMK</p>

                    <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-2xl bg-mist p-4">
                            <span class="text-slate-500">Condition</span>
                            <p class="mt-1 font-bold">{{ $product['status'] ?? 'Used' }}</p>
                        </div>
                        <div class="rounded-2xl bg-mist p-4">
                            <span class="text-slate-500">Location</span>
                            <p class="mt-1 font-bold">{{ $product['location'] }}</p>
                        </div>
                    </div>

                    @if ($isOwner)
                        <a href="{{ route('listings.edit', $product['id']) }}" class="mt-6 block rounded-2xl bg-ink px-5 py-3.5 text-center font-bold text-white">Edit listing</a>
                    @else
                        <a href="{{ route('messages.show', $seller['id']) }}" class="mt-6 block rounded-2xl bg-cobalt px-5 py-3.5 text-center font-bold text-white">Message seller</a>
                    @endif
                </section>

                <section class="rounded-3xl bg-white p-6 shadow-soft">
                    <p class="text-sm text-slate-500">Seller</p>
                    <div class="mt-3 flex items-center gap-3">
                        <span class="grid h-12 w-12 place-items-center rounded-full bg-cobalt text-lg font-bold text-white" aria-hidden="true">{{ strtoupper(substr($seller['name'], 0, 1)) }}</span>
                        <div>
                            <p class="font-bold">{{ $seller['name'] }}</p>
                            <p class="text-sm text-slate-500">Member since {{ $seller['member_since'] }} · {{ $seller['listing_count'] }} listing{{ $seller['listing_count'] === 1 ? '' : 's' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('seller.profile', $seller['id']) }}" class="mt-4 inline-block text-sm font-bold text-cobalt">View seller profile →</a>
                </section>
            </aside>
        </div>

        <section class="mt-10 max-w-3xl rounded-3xl bg-white p-7 shadow-soft">
            <h2 class="text-xl font-black">Description</h2>
            <p class="mt-4 leading-7 text-slate-600">{{ $product['description'] }}</p>
            <div class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <span class="text-slate-500">Posted</span>
                    <p class="font-semibold">{{ $postedDate }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Listing ID</span>
                    <p class="font-semibold">MK-{{ str_pad((string) ($product['id'] ?? 0), 5, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </section>
    </main>
    <script>
        const mainImage = document.getElementById('mainImage');

        document.querySelectorAll('.gallery-thumb').forEach((thumbnail) => {
            thumbnail.addEventListener('click', () => {
                mainImage.src = thumbnail.dataset.image;
                document.querySelectorAll('.gallery-thumb').forEach((item) => {
                    item.classList.remove('border-ink');
                    item.classList.add('border-transparent');
                });
                thumbnail.classList.remove('border-transparent');
                thumbnail.classList.add('border-ink');
            });
        });
    </script>
</body>
</html>
