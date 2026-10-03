<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function dashboard()
    {
        $products = Product::allProducts();

        return view('user.dashboard', compact('products'));
    }

    public function showProduct(string $slug)
    {
        $productRecord = Product::query()->with('images')->where('slug', $slug)->first();
        $product = $productRecord?->toArray() ?? Product::findBySlug($slug) ?? Product::allProducts()[0];
        $sellerRecord = User::query()->find($product['seller_id'] ?? null);
        $isFavorite = $productRecord !== null
            && auth()->user()->favorites()->whereKey($productRecord->id)->exists();
        $isOwner = $productRecord !== null
            && (int) $productRecord->seller_id === (int) auth()->id();
        $seller = [
            'id' => $sellerRecord?->id ?? ($product['seller_id'] ?? 1),
            'name' => $sellerRecord?->name ?? 'Marketly seller',
            'member_since' => $sellerRecord?->created_at?->format('Y') ?? '2024',
            'listing_count' => $sellerRecord
                ? Product::query()->where('seller_id', $sellerRecord->id)->count()
                : 0,
        ];

        return view('user.product', [
            'product' => $product,
            'seller' => $seller,
            'canFavorite' => $productRecord !== null,
            'isFavorite' => $isFavorite,
            'isOwner' => $isOwner,
        ]);
    }

    public function toggleFavorite(string $slug)
    {
        $product = Product::query()->where('slug', $slug)->firstOrFail();
        $favorites = auth()->user()->favorites();

        if ($favorites->whereKey($product->id)->exists()) {
            $favorites->detach($product->id);
        } else {
            $favorites->attach($product->id);
        }

        return redirect()->route('products.show', $slug);
    }

    public function sellerProfile(string $id)
    {
        $seller = User::query()->findOrFail($id);
        $products = Product::query()
            ->where('seller_id', $seller->id)
            ->latest()
            ->get()
            ->toArray();
        $sellerProfile = [
            'id' => $seller->id,
            'name' => $seller->name,
            'location' => $seller->location ?? 'Location not provided',
            'member_since' => $seller->created_at?->format('Y') ?? 'Unknown',
            'active_listings' => Product::query()
                ->where('seller_id', $seller->id)
                ->where('listing_status', 'Available')
                ->count(),
            'sold_listings' => Product::query()
                ->where('seller_id', $seller->id)
                ->where('listing_status', 'Sold')
                ->count(),
        ];
        $isOwner = (int) auth()->id() === (int) $seller->id;

        return view('user.seller-profile', compact('sellerProfile', 'products', 'isOwner'));
    }

    public function messages()
    {
        $threads = Message::threadsForUser(auth()->id());

        return view('user.messages', compact('threads'));
    }

    public function conversation(int $id)
    {
        $seller = User::query()->findOrFail($id);
        Message::markThreadAsRead($id, (int) auth()->id());
        $messages = Message::messagesForThread($id, (int) auth()->id());

        $conversation = [
            'id' => $id,
            'name' => $seller['name'] ?? 'Aye Chan',
            'status' => 'Active now',
        ];
        $listing = Product::query()
            ->where('seller_id', $id)
            ->where('listing_status', 'Available')
            ->latest()
            ->first()?->toArray();

        return view('user.conversation', compact('conversation', 'messages', 'seller', 'listing'));
    }

    public function sendMessage(Request $request, int $id): RedirectResponse
    {
        $receiver = User::query()->findOrFail($id);

        abort_if($receiver->id === auth()->id(), 404);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        Message::query()->create([
            'sender_id' => auth()->id(),
            'receiver_id' => $receiver->id,
            'body' => $validated['body'],
        ]);

        return redirect()->route('messages.show', $receiver->id);
    }

    public function favorites()
    {
        $products = auth()->user()->favorites()
            ->latest('products.created_at')
            ->get()
            ->toArray();

        return view('user.favorites', compact('products'));
    }

    public function myListings()
    {
        $statusFilter = request()->query('status', 'all');
        $query = Product::query()->where('seller_id', auth()->id());

        if (in_array($statusFilter, ['available', 'sold'], true)) {
            $query->where('listing_status', ucfirst($statusFilter));
        }

        $products = $query->latest()->get()->toArray();

        return view('user.my-listings', compact('products', 'statusFilter'));
    }

    public function editListing(int $id)
    {
        $product = Product::query()
            ->with('images')
            ->where('seller_id', auth()->id())
            ->findOrFail($id)
            ->toArray();

        return view('user.sell', compact('product'));
    }

    public function updateListing(Request $request, int $id)
    {
        $product = Product::query()
            ->where('seller_id', auth()->id())
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:1'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['required', 'in:New,Like new,Good,Fair'],
        ]);

        $validated = $this->storeListingImages($request, $validated);

        Product::updateListing($product->id, $validated);

        return redirect()->route('my-listings');
    }

    public function markListingSold(int $id)
    {
        $product = Product::query()
            ->where('seller_id', auth()->id())
            ->findOrFail($id);

        $product->update(['listing_status' => 'Sold']);

        return redirect()->route('my-listings');
    }

    public function deleteListing(int $id)
    {
        $product = Product::query()
            ->where('seller_id', auth()->id())
            ->findOrFail($id);

        Product::deleteListing($product->id);

        return redirect()->route('my-listings');
    }

    public function sell()
    {
        return view('user.sell');
    }

    public function storeListing(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:1'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $validated = $this->storeListingImages($request, $validated);

        Product::createListing($validated);

        return redirect()->route('my-listings');
    }

    private function storeListingImages(Request $request, array $validated): array
    {
        $galleryPaths = collect($request->file('images', []))
            ->map(fn ($image) => $image->store('products', 'public'))
            ->all();

        unset($validated['images']);

        if ($galleryPaths !== []) {
            $validated['image'] = $galleryPaths[0];
            $validated['gallery_paths'] = $galleryPaths;
        }

        return $validated;
    }
}
