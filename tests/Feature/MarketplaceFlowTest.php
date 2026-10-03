<?php

use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Demo User',
        'location' => 'Yangon',
    ]);

    $this->actingAs($this->user);
});

test('dashboard marketplace page loads', function () {
    $this->get('/dashboard')
        ->assertOk()
        ->assertSee('Marketly');
});

test('dashboard uses Laravel product and navigation routes', function () {
    $this->get('/dashboard')
        ->assertOk()
        ->assertSee(route('products.show', 'iphone-13'))
        ->assertSee(route('favorites'))
        ->assertSee(route('messages'));
});

test('product detail page loads', function () {
    $this->get('/products/iphone-13')
        ->assertOk()
        ->assertSee('iPhone 13');
});

test('product detail page shows the listing and seller information', function () {
    $product = Product::createListing([
        'name' => 'Camera for sale',
        'category' => 'electronics',
        'price' => 420000,
        'location' => 'Bahan',
        'description' => 'A camera in excellent condition.',
        'status' => 'Like new',
    ]);

    $this->get(route('products.show', $product->slug))
        ->assertSee('Camera for sale')
        ->assertSee('420,000 MMK')
        ->assertSee('Like new')
        ->assertSee('Bahan')
        ->assertSee('Demo User')
        ->assertSee(route('seller.profile', $this->user->id));
});

test('users can save and remove products from favorites', function () {
    $product = Product::createListing([
        'name' => 'Favorite camera',
        'category' => 'electronics',
        'price' => 420000,
        'location' => 'Bahan',
        'description' => 'A camera worth saving.',
    ]);

    $this->get(route('products.show', $product->slug))
        ->assertSee('aria-pressed="false"', false);

    $this->post(route('favorites.toggle', $product->slug))
        ->assertRedirect(route('products.show', $product->slug));

    $this->assertDatabaseHas('favorites', [
        'user_id' => $this->user->id,
        'product_id' => $product->id,
    ]);

    $this->get(route('favorites'))
        ->assertSee('Favorite camera');

    $this->get(route('products.show', $product->slug))
        ->assertSee('aria-pressed="true"', false);

    $this->post(route('favorites.toggle', $product->slug))
        ->assertRedirect(route('products.show', $product->slug));

    $this->assertDatabaseMissing('favorites', [
        'user_id' => $this->user->id,
        'product_id' => $product->id,
    ]);

    $this->get(route('favorites'))
        ->assertSee('No favorites yet.')
        ->assertDontSee('Favorite camera');
});

test('favorites are only visible to the user who saved them', function () {
    $product = Product::createListing([
        'name' => 'Private favorite',
        'category' => 'electronics',
        'price' => 420000,
        'location' => 'Bahan',
        'description' => 'A saved item for one account.',
    ]);

    $this->post(route('favorites.toggle', $product->slug));

    $otherUser = User::factory()->create(['location' => 'Yangon']);

    $this->actingAs($otherUser)
        ->get(route('favorites'))
        ->assertSee('No favorites yet.')
        ->assertDontSee('Private favorite');
});

test('messages inbox page loads', function () {
    $this->get('/messages')
        ->assertOk()
        ->assertSee('Inbox')
        ->assertSee('Conversations about your marketplace listings.')
        ->assertSee('Search messages...')
        ->assertSee('No conversations yet.');
});

test('conversation page shows participant messages and their available listing', function () {
    $seller = User::factory()->create([
        'name' => 'Aung Kyaw',
        'location' => 'Sanchaung',
    ]);
    $product = Product::createListing([
        'name' => 'iPhone 13 128GB',
        'category' => 'electronics',
        'price' => 690000,
        'location' => 'Sanchaung',
        'description' => 'A phone in good condition.',
        'status' => 'Like new',
        'seller_id' => $seller->id,
    ]);
    Message::create([
        'sender_id' => $seller->id,
        'receiver_id' => $this->user->id,
        'body' => 'Hi! The item is still available.',
    ]);

    $this->get(route('messages.show', $seller->id))
        ->assertSee('Aung Kyaw')
        ->assertSee('Hi! The item is still available.')
        ->assertSee('iPhone 13 128GB')
        ->assertSee('690,000 MMK')
        ->assertSee(route('products.show', $product->slug))
        ->assertSee(route('seller.profile', $seller->id))
        ->assertSee('id="messageForm"', false)
        ->assertSee('id="messageInput"', false);
});

test('buyers can send a message that appears in the sellers inbox and conversation', function () {
    $seller = User::factory()->create(['name' => 'Aung Kyaw', 'location' => 'Yangon']);

    $this->post('/messages/'.$seller->id, [
        'body' => 'Is the phone still available?',
    ])->assertRedirect('/messages/'.$seller->id);

    $this->assertDatabaseHas('messages', [
        'sender_id' => $this->user->id,
        'receiver_id' => $seller->id,
        'body' => 'Is the phone still available?',
    ]);

    $this->actingAs($seller)
        ->get(route('messages'))
        ->assertSee('Demo User')
        ->assertSee('Is the phone still available?');

    $this->get(route('messages.show', $this->user->id))
        ->assertSee('Is the phone still available?');

    $this->assertDatabaseHas('messages', [
        'sender_id' => $this->user->id,
        'receiver_id' => $seller->id,
        'read_at' => now(),
    ]);
});

test('message threads do not show messages from unrelated users', function () {
    $seller = User::factory()->create(['location' => 'Yangon']);
    $otherBuyer = User::factory()->create(['location' => 'Yangon']);
    Message::create([
        'sender_id' => $this->user->id,
        'receiver_id' => $seller->id,
        'body' => 'This message belongs to the current thread.',
    ]);
    Message::create([
        'sender_id' => $otherBuyer->id,
        'receiver_id' => $this->user->id,
        'body' => 'This belongs to a different thread.',
    ]);

    $this->get(route('messages.show', $seller->id))
        ->assertSee('This message belongs to the current thread.')
        ->assertDontSee('This belongs to a different thread.');
});

test('users cannot send an empty message', function () {
    $seller = User::factory()->create(['location' => 'Yangon']);

    $this->from(route('messages.show', $seller->id))
        ->post('/messages/'.$seller->id, ['body' => ''])
        ->assertRedirect(route('messages.show', $seller->id))
        ->assertSessionHasErrors('body');

    $this->assertDatabaseCount('messages', 0);
});

test('sell page loads', function () {
    $this->get('/sell')
        ->assertOk()
        ->assertSee('Sell an item');
});

test('users can publish a new listing from the sell page', function () {
    $response = $this->from('/sell')
        ->post('/sell', [
            'name' => 'Gaming headset',
            'category' => 'electronics',
            'price' => 180000,
            'location' => 'Dagon',
            'description' => 'Wireless headset with USB receiver and extra ear pads.',
        ]);

    $response->assertRedirect('/my-listings');
    $products = $response->getSession()->get('marketplace_products');

    expect($products)->not->toBeEmpty()
        ->and($products[0]['name'])->toBe('Gaming headset');
});

test('users can upload a listing photo that appears on product pages', function () {
    Storage::fake('public');

    $this->get('/sell')
        ->assertSee('enctype="multipart/form-data"', false)
        ->assertSee('type="file"', false);

    $this->post('/sell', [
        'name' => 'Photo lamp',
        'category' => 'furniture',
        'price' => 30000,
        'location' => 'Bahan',
        'description' => 'A lamp with an uploaded image.',
        'image' => UploadedFile::fake()->image('lamp.jpg'),
    ])->assertRedirect('/my-listings');

    $product = Product::query()->where('name', 'Photo lamp')->firstOrFail();
    $imagePath = $product->getRawOriginal('image');

    Storage::disk('public')->assertExists($imagePath);

    $this->get(route('products.show', $product->slug))
        ->assertSee(Storage::disk('public')->url($imagePath));
});

test('replacing and deleting a listing removes its uploaded photos', function () {
    Storage::fake('public');
    $oldImagePath = UploadedFile::fake()->image('old.jpg')->store('products', 'public');
    $product = Product::createListing([
        'name' => 'Photo frame',
        'category' => 'furniture',
        'price' => 20000,
        'location' => 'Bahan',
        'description' => 'A framed photo.',
        'image' => $oldImagePath,
    ]);
    $newPhoto = UploadedFile::fake()->image('new.jpg');

    $this->put(route('listings.update', $product->id), [
        'name' => 'Photo frame',
        'category' => 'furniture',
        'price' => 20000,
        'location' => 'Bahan',
        'description' => 'A framed photo.',
        'status' => 'Good',
        'image' => $newPhoto,
    ])->assertRedirect(route('my-listings'));

    $updatedProduct = Product::findOrFail($product->id);
    $newImagePath = $updatedProduct->getRawOriginal('image');

    Storage::disk('public')->assertMissing($oldImagePath);
    Storage::disk('public')->assertExists($newImagePath);

    $this->delete(route('listings.destroy', $product->id))
        ->assertRedirect(route('my-listings'));

    Storage::disk('public')->assertMissing($newImagePath);
});

test('sellers can edit their listing and persist the changes', function () {
    $product = Product::createListing([
        'name' => 'Desk lamp',
        'category' => 'furniture',
        'price' => 30000,
        'location' => 'Bahan',
        'description' => 'A compact lamp.',
    ]);

    $this->get(route('listings.edit', $product->id))
        ->assertOk()
        ->assertSee('value="Desk lamp"', false);

    $this->put(route('listings.update', $product->id), [
        'name' => 'Desk lamp pro',
        'category' => 'furniture',
        'price' => 35000,
        'location' => 'Bahan',
        'description' => 'An improved compact lamp.',
        'status' => 'Like new',
    ])->assertRedirect(route('my-listings'));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'seller_id' => $this->user->id,
        'name' => 'Desk lamp pro',
        'price' => 35000,
        'status' => 'Like new',
        'listing_status' => 'Available',
    ]);
});

test('sellers can mark their listing as sold', function () {
    $product = Product::createListing([
        'name' => 'Desk lamp',
        'category' => 'furniture',
        'price' => 30000,
        'location' => 'Bahan',
        'description' => 'A compact lamp.',
    ]);

    $this->patch(route('listings.status', $product->id))
        ->assertRedirect(route('my-listings'));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'listing_status' => 'Sold',
    ]);
});

test('sellers can delete their listing', function () {
    $product = Product::createListing([
        'name' => 'Desk lamp',
        'category' => 'furniture',
        'price' => 30000,
        'location' => 'Bahan',
        'description' => 'A compact lamp.',
    ]);

    $this->delete(route('listings.destroy', $product->id))
        ->assertRedirect(route('my-listings'));

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

test('sellers cannot manage another users listing', function () {
    $product = Product::createListing([
        'name' => 'Private listing',
        'category' => 'furniture',
        'price' => 30000,
        'location' => 'Bahan',
        'description' => 'Owned by another seller.',
        'seller_id' => User::factory()->create(['location' => 'Yangon'])->id,
    ]);

    $this->get(route('listings.edit', $product->id))->assertNotFound();
    $this->put(route('listings.update', $product->id), [])->assertNotFound();
    $this->patch(route('listings.status', $product->id))->assertNotFound();
    $this->delete(route('listings.destroy', $product->id))->assertNotFound();

    $this->assertModelExists($product);
});

test('sellers only see their own listings and can filter sold listings', function () {
    Product::createListing([
        'name' => 'Available desk lamp',
        'category' => 'furniture',
        'price' => 30000,
        'location' => 'Bahan',
        'description' => 'A compact lamp.',
    ]);

    Product::createListing([
        'name' => 'Sold chair',
        'category' => 'furniture',
        'price' => 95000,
        'location' => 'Kamayut',
        'description' => 'A chair already sold.',
        'listing_status' => 'Sold',
    ]);

    Product::createListing([
        'name' => 'Another seller item',
        'category' => 'books',
        'price' => 10000,
        'location' => 'Yangon',
        'description' => 'Not owned by the signed-in seller.',
        'seller_id' => User::factory()->create(['location' => 'Yangon'])->id,
    ]);

    $this->get(route('my-listings'))
        ->assertSee('Available desk lamp')
        ->assertSee('Sold chair')
        ->assertDontSee('Another seller item');

    $this->get(route('my-listings', ['status' => 'sold']))
        ->assertSee('Sold chair')
        ->assertDontSee('Available desk lamp')
        ->assertDontSee('Another seller item');
});

test('product model supports listing crud operations', function () {
    $user = User::factory()->create([
        'location' => 'Yangon',
    ]);

    $product = Product::createListing([
        'name' => 'Desk lamp',
        'category' => 'furniture',
        'price' => 30000,
        'location' => 'Bahan',
        'description' => 'Minimal desk lamp with warm LED bulb.',
        'image' => 'https://example.com/lamp.jpg',
        'seller_id' => $user->id,
    ]);

    expect($product->name)->toBe('Desk lamp')
        ->and(Product::findById($product->id)?->name)->toBe('Desk lamp');

    $updated = Product::updateListing($product->id, [
        'name' => 'Desk lamp pro',
        'price' => 35000,
    ]);

    expect($updated)->not->toBeNull()
        ->and($updated->name)->toBe('Desk lamp pro')
        ->and(Product::findById($product->id)?->price)->toBe(35000);

    expect(Product::deleteListing($product->id))->toBeTrue()
        ->and(Product::findById($product->id))->toBeNull();
});
