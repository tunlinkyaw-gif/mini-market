<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Favorites — Marketly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-mist text-ink">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
        <a href="{{ route('dashboard') }}" class="text-xl font-black">Marketly</a>
        <nav class="flex gap-6 text-sm font-semibold text-slate-600">
            <a href="{{ route('dashboard') }}">Browse</a>
            <a href="{{ route('messages') }}">Messages</a>
            <a href="{{ route('my-listings') }}">My Listings</a>
        </nav>
    </div>
</header>

<main class="mx-auto max-w-6xl px-5 py-10">
    <div class="mb-6">
        <p class="text-sm font-bold uppercase tracking-wide text-cobalt">Saved items</p>
        <h1 class="text-3xl font-black">Favorites</h1>
    </div>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($products as $product)
            <a href="{{ route('products.show', $product['slug']) }}" class="overflow-hidden rounded-2xl bg-white shadow-soft">
                <img src="{{ $product['image_url'] ?? $product['image'] }}" alt="{{ $product['name'] }}" class="h-52 w-full object-cover" />
                <div class="p-4">
                    <h3 class="font-bold">{{ $product['name'] }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $product['location'] }}</p>
                    <p class="mt-3 text-xl font-black">{{ number_format($product['price']) }} MMK</p>
                </div>
            </a>
        @endforeach
    </div>
    @if (empty($products))
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center">
            <p class="text-lg font-bold">No favorites yet.</p>
            <p class="mt-2 text-sm text-slate-500">Save a listing with the heart button and it will appear here.</p>
            <a href="{{ route('dashboard') }}" class="mt-5 inline-block rounded-xl bg-ink px-4 py-2.5 text-sm font-bold text-white">Browse listings</a>
        </div>
    @endif
</main>
</body>
</html>
