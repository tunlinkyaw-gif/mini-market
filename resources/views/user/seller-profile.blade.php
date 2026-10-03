<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $seller['name'] }} — Marketly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-mist text-ink">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
        <a href="{{ route('dashboard') }}" class="text-xl font-black">Marketly</a>
        <a href="{{ route('messages') }}" class="rounded-xl bg-ink px-4 py-2 font-bold text-white">Inbox</a>
    </div>
</header>

<main class="mx-auto max-w-6xl px-5 py-10">
    <section class="rounded-3xl bg-white p-6 shadow-soft">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">Seller profile</p>
                <h1 class="mt-2 text-3xl font-black">{{ $seller['name'] }}</h1>
                <p class="mt-2 text-slate-500">{{ $seller['location'] }} · {{ $seller['sold'] }} sales</p>
            </div>
            <div class="rounded-2xl bg-cobalt px-4 py-3 text-white">
                <p class="text-xs uppercase tracking-wide">Rating</p>
                <p class="text-2xl font-black">{{ $seller['rating'] }}</p>
            </div>
        </div>

        <p class="mt-6 text-slate-600">{{ $seller['bio'] }}</p>
        <a href="{{ route('messages.show', $seller['id']) }}" class="mt-6 inline-block rounded-2xl bg-ink px-5 py-3 font-bold text-white">Message seller</a>
    </section>

    <section class="mt-10">
        <h2 class="text-2xl font-black">Seller listings</h2>
        <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($products as $product)
                <a href="{{ route('products.show', $product['slug']) }}" class="overflow-hidden rounded-2xl bg-white shadow-soft">
                    <img src="{{ $product['image_url'] ?? $product['image'] }}" alt="{{ $product['name'] }}" class="h-48 w-full object-cover" />
                    <div class="p-4">
                        <h3 class="font-bold">{{ $product['name'] }}</h3>
                        <p class="mt-2 text-xl font-black">{{ number_format($product['price']) }} MMK</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</main>
</body>
</html>
