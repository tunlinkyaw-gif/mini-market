<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $appends = ['image_url', 'gallery_urls'];

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

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
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

    public function getGalleryUrlsAttribute(): array
    {
        $coverPath = $this->getRawOriginal('image');
        $gallery = $this->relationLoaded('images')
            ? $this->getRelation('images')
            : collect();
        $galleryPaths = $gallery->pluck('path')->all();
        $coverUrl = $this->image_url;
        $urls = $gallery->map(fn (ProductImage $image) => Storage::disk('public')->url($image->path))->all();

        if ($coverUrl !== null && ! in_array($coverPath, $galleryPaths, true)) {
            array_unshift($urls, $coverUrl);
        }

        return array_values(array_unique($urls));
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
            'image' => $data['gallery_paths'][0] ?? $data['image'] ?? 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=800&q=80',
            'seller_id' => $data['seller_id'] ?? auth()->id() ?? 1,
            'status' => $status,
            'listing_status' => $data['listing_status'] ?? 'Available',
        ]);

        if (! empty($data['gallery_paths'])) {
            $record->images()->createMany(array_map(
                fn (string $path, int $position) => ['path' => $path, 'position' => $position],
                $data['gallery_paths'],
                array_keys($data['gallery_paths']),
            ));
        }

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

        $galleryPaths = $data['gallery_paths'] ?? null;
        $previousImage = $product->getRawOriginal('image');
        $previousGalleryPaths = $product->images()->pluck('path')->all();

        $updateData = [
            'name' => $data['name'] ?? $product->name,
            'category' => $data['category'] ?? $product->category,
            'price' => isset($data['price']) ? (int) $data['price'] : $product->price,
            'location' => $data['location'] ?? $product->location,
            'description' => $data['description'] ?? $product->description,
            'image' => $galleryPaths[0] ?? $data['image'] ?? $product->image,
            'status' => $data['status'] ?? $data['condition'] ?? $product->status,
            'listing_status' => $data['listing_status'] ?? $product->listing_status,
        ];
        if (isset($data['name'])) {
            $updateData['slug'] = Str::slug($data['name']).'-'.$product->id;
        }

        $product->fill($updateData);
        $product->save();

        if ($galleryPaths !== null) {
            $product->images()->delete();
            $product->images()->createMany(array_map(
                fn (string $path, int $position) => ['path' => $path, 'position' => $position],
                $galleryPaths,
                array_keys($galleryPaths),
            ));

            foreach (array_unique(array_merge([$previousImage], $previousGalleryPaths)) as $previousPath) {
                self::deleteStoredImage($previousPath);
            }
        } elseif (isset($data['image']) && $data['image'] !== $previousImage) {
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

        $storedImages = array_unique(array_merge(
            [$product->getRawOriginal('image')],
            $product->images()->pluck('path')->all(),
        ));
        $deleted = $product->delete();

        if ($deleted) {
            foreach ($storedImages as $image) {
                self::deleteStoredImage($image);
            }
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
