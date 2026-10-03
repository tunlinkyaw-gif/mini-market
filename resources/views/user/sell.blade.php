<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Sell an item — Marketly</title>
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
        <div class="mx-auto flex max-w-5xl items-center justify-between px-5 py-4">
            <a href="{{ route('dashboard') }}" class="text-xl font-black">Marketly</a>
            <a href="{{ route('my-listings') }}" class="text-sm font-bold text-slate-700">My Listings</a>
        </div>
    </header>

    <main class="mx-auto max-w-4xl px-5 py-10">
        <div class="mb-8">
            <p class="text-sm font-bold text-cobalt">{{ isset($product) ? 'Edit listing' : 'New listing' }}</p>
            <h1 class="text-3xl font-black">{{ isset($product) ? 'Update your item' : 'Sell an item' }}</h1>
            <p class="mt-2 text-slate-500">Add clear details so buyers know exactly what you're offering.</p>
        </div>

        <form action="{{ isset($product) ? route('listings.update', $product['id']) : route('sell.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 rounded-3xl bg-white p-6 shadow-sm sm:p-8">
            @csrf
            @if (isset($product))
                @method('PUT')
            @endif

            <div>
                <label class="font-bold">Photos</label>
                <div id="photoPreviewList" class="mt-3 flex flex-wrap items-center gap-3">
                    @foreach (($product['gallery_urls'] ?? []) as $photoUrl)
                        <img src="{{ $photoUrl }}" alt="Current product photo" class="h-24 w-24 rounded-2xl object-cover">
                    @endforeach
                    <label for="photoInput" class="grid h-24 w-24 cursor-pointer place-items-center rounded-2xl border-2 border-dashed border-slate-300 text-center text-sm font-semibold text-slate-500">
                        <span>+ Add photo</span>
                        <input id="photoInput" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only">
                    </label>
                </div>
                <p class="mt-2 text-sm text-slate-500">Upload up to 4 photos. JPG, PNG, or WebP, maximum 5 MB each. New photos replace the current gallery.</p>
                @if ($errors->has('images') || $errors->has('images.0'))
                    <p class="mt-2 text-sm font-semibold text-red-600">{{ $errors->first('images') ?: $errors->first('images.0') }}</p>
                @endif
            </div>

            <div>
                <label class="font-bold">Title</label>
                <input name="name" value="{{ old('name', $product['name'] ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-cobalt" placeholder="e.g. iPhone 13 128GB" required />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="font-bold">Category</label>
                    <select name="category" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                        @foreach (['electronics' => 'Electronics', 'fashion' => 'Fashion', 'books' => 'Books', 'furniture' => 'Furniture', 'sports' => 'Sports', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('category', $product['category'] ?? 'electronics') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="font-bold">Condition</label>
                    <select name="status" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                        @foreach (['New', 'Like new', 'Good', 'Fair'] as $condition)
                            <option value="{{ $condition }}" @selected(old('status', $product['status'] ?? 'New') === $condition)>{{ $condition }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="font-bold">Price (MMK)</label>
                    <input name="price" value="{{ old('price', $product['price'] ?? '') }}" type="number" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" placeholder="0" required />
                </div>
                <div>
                    <label class="font-bold">Location</label>
                    <input name="location" value="{{ old('location', $product['location'] ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" placeholder="Township or area" required />
                </div>
            </div>

            <div>
                <label class="font-bold">Description</label>
                <textarea name="description" rows="6" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" placeholder="Describe condition, what's included, and anything the buyer should know." required>{{ old('description', $product['description'] ?? '') }}</textarea>
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('my-listings') }}" class="rounded-2xl border px-5 py-3 text-center font-bold">Cancel</a>
                <button type="submit" class="rounded-2xl bg-ink px-6 py-3 font-bold text-white">{{ isset($product) ? 'Save changes' : 'Publish listing' }}</button>
            </div>
        </form>
    </main>
    <script>
        const photoInput = document.getElementById('photoInput');
        const photoPreviewList = document.getElementById('photoPreviewList');
        const photoPicker = photoInput.closest('label');

        photoInput.addEventListener('change', () => {
            if (photoInput.files.length > 4) {
                photoInput.value = '';
                window.alert('Choose no more than four photos.');
                return;
            }

            photoPreviewList.querySelectorAll('img').forEach((preview) => preview.remove());
            [...photoInput.files].forEach((photo) => {
                const preview = document.createElement('img');
                preview.src = URL.createObjectURL(photo);
                preview.alt = 'Selected product photo';
                preview.className = 'h-24 w-24 rounded-2xl object-cover';
                photoPreviewList.insertBefore(preview, photoPicker);
            });
        });
    </script>
</body>
</html>
