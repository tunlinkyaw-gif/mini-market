<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $appends = ['image_url'];

    protected $fillable = [
        'name',
        'slug',
        'category',
        'price',
        'location',
        'description',
        'image',
        'seller_id',
        'status',
        'listing_status',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function getImageUrlAttribute(): ?string
    {
        $image = $this->getRawOriginal('image');

        if ($image === null) {
            return null;
        }

        return Str::startsWith($image, ['http://', 'https://'])
            ? $image
            : Storage::disk('public')->url($image);
    }

    public static function sampleProducts(): array
    {
        return [
            [
                'id' => 1,
                'slug' => 'iphone-13',
                'name' => 'iPhone 13',
                'category' => 'electronics',
                'price' => 690000,
                'location' => 'Sanchaung',
                'description' => 'Unlocked iPhone 13 in excellent condition with original box and charger.',
                'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=800&q=80',
                'seller_id' => 1,
                'status' => 'Like new',
            ],
            [
                'id' => 2,
                'slug' => 'minimal-desk-chair',
                'name' => 'Minimal desk chair',
                'category' => 'furniture',
                'price' => 95000,
                'location' => 'Kamayut',
                'description' => 'Compact chair with comfortable cushion and smooth rolling castors.',
                'image' => 'https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=800&q=80',
                'seller_id' => 1,
                'status' => 'Good',
            ],
            [
                'id' => 3,
                'slug' => 'programming-books-bundle',
                'name' => 'Programming books bundle',
                'category' => 'books',
                'price' => 28000,
                'location' => 'Hledan',
                'description' => 'Bundle of beginner and intermediate programming books for web development.',
                'image' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=800&q=80',
                'seller_id' => 1,
                'status' => 'Good',
            ],
            [
                'id' => 4,
                'slug' => 'nike-everyday-sneakers',
                'name' => 'Nike everyday sneakers',
                'category' => 'fashion',
                'price' => 75000,
                'location' => 'Bahan',
                'description' => 'Lightly worn sneakers with clean sole and breathable mesh upper.',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
                'seller_id' => 1,
                'status' => 'New',
            ],
        ];
    }

    public static function allProducts(): array
    {
        if (app('db')->getSchemaBuilder()->hasTable('products')) {
            $products = self::query()->latest()->get()->map(fn ($product) => $product->toArray())->all();

            if (! empty($products)) {
                return $products;
            }
        }

        return self::sampleProducts();
    }

    public static function createListing(array $data): self
    {
        $status = $data['status'] ?? $data['condition'] ?? 'New';

        $record = self::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.(string) now()->timestamp,
            'category' => $data['category'],
            'price' => (int) $data['price'],
            'location' => $data['location'],
            'description' => $data['description'],
            'image' => $data['image'] ?? 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=800&q=80',
            'seller_id' => $data['seller_id'] ?? auth()->id() ?? 1,
            'status' => $status,
            'listing_status' => $data['listing_status'] ?? 'Available',
        ]);

        $products = self::allProducts();
        array_unshift($products, $record->toArray());
        session(['marketplace_products' => $products]);

        return $record;
    }

    public static function storeProduct(array $data): array
    {
        return self::createListing($data)->toArray();
    }

    public static function findById(int $id): ?self
    {
        return self::query()->find($id);
    }

    public static function findBySlug(string $slug): ?array
    {
        if (app('db')->getSchemaBuilder()->hasTable('products')) {
            $product = self::query()->where('slug', $slug)->first();

            if ($product) {
                return $product->toArray();
            }
        }

        foreach (self::sampleProducts() as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }

        return null;
    }

    public static function updateListing(int $id, array $data): ?self
    {
        $product = self::findById($id);

        if (! $product) {
            return null;
        }

        $updateData = [
            'name' => $data['name'] ?? $product->name,
            'category' => $data['category'] ?? $product->category,
            'price' => isset($data['price']) ? (int) $data['price'] : $product->price,
            'location' => $data['location'] ?? $product->location,
            'description' => $data['description'] ?? $product->description,
            'image' => $data['image'] ?? $product->image,
            'status' => $data['status'] ?? $data['condition'] ?? $product->status,
            'listing_status' => $data['listing_status'] ?? $product->listing_status,
        ];
        $previousImage = $product->getRawOriginal('image');

        if (isset($data['name'])) {
            $updateData['slug'] = Str::slug($data['name']).'-'.$product->id;
        }

        $product->fill($updateData);
        $product->save();

        if (isset($data['image']) && $data['image'] !== $previousImage) {
            self::deleteStoredImage($previousImage);
        }

        session(['marketplace_products' => self::allProducts()]);

        return $product->fresh();
    }

    public static function deleteListing(int $id): bool
    {
        $product = self::findById($id);

        if (! $product) {
            return false;
        }

        $deleted = $product->delete();

        if ($deleted) {
            self::deleteStoredImage($product->getRawOriginal('image'));
        }

        session(['marketplace_products' => self::allProducts()]);

        return $deleted;
    }

    private static function deleteStoredImage(?string $image): void
    {
        if ($image !== null && ! Str::startsWith($image, ['http://', 'https://'])) {
            Storage::disk('public')->delete($image);
        }
    }
}
