# Marketly

Marketly is a local marketplace web application built with Laravel. Users can browse listings, publish and manage items for sale, save favorites, view seller profiles, and message other users.

## Features

- Account registration, sign-in, and sign-out.
- Marketplace browsing with product details, categories, search, and price sorting.
- Seller profiles with active and sold listing counts.
- Create, edit, mark sold, and delete your own listings.
- Upload up to four JPG, PNG, or WebP photos per listing. Each photo can be up to 5 MB.
- Save and remove favorites. Favorites are private to each account.
- Send text messages to sellers. Messages are stored, appear in both participants' inboxes, and are marked read when a conversation is opened.
- Responsive Blade views styled with Tailwind CSS through its CDN script.

## Requirements

- PHP 8.3 or newer with the PHP extensions required by Laravel.
- Composer.
- SQLite (the default local database) or another Laravel-supported database such as MySQL.
- Node.js and npm are optional for this app's current Blade pages. The views load Tailwind from its CDN and do not use the Vite manifest.

## Local Setup

From the project directory:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

The example environment uses SQLite. Create the database file if it does not already exist, then migrate and seed the database:

```bash
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
```

Start the local development server:

```bash
php artisan serve
```

Open the URL printed by Artisan, normally `http://127.0.0.1:8000`.

For MySQL, update the database settings in `.env` before running migrations:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_market
DB_USERNAME=root
DB_PASSWORD=
```

Create the database in MySQL first. Do not run `migrate:fresh` against a database containing data; it drops tables.

## Demo Account

After running the seeder, the local demo account is:

- Email: `test@example.com`
- Password: `password`

This is intended only for local development. Change or remove the demo credentials before deploying an application anywhere accessible to other people.

## User Guide

### Create an account

1. Open the registration page from the sign-in screen.
2. Enter your name, email, password and confirmation, phone number, and location.
3. Submit the form, then sign in with the new account.

### Browse and inspect listings

1. Use the dashboard to browse listings.
2. Search by product text or use a category filter. The price selector sorts the visible products.
3. Select a product to see its photos, condition, price, location, description, and seller profile.
4. Use the gallery thumbnails, when present, to switch the main product photo.
5. Select **Message seller** to open a conversation. On your own listing, the product page shows **Edit listing** instead.

### Sell an item

1. Select **Sell item** from the dashboard or another page header.
2. Enter the title, category, condition, price, location, and description.
3. Choose up to four JPG, PNG, or WebP photos. Each file must be 5 MB or smaller.
4. Select **Publish listing**. The first photo is used as the cover image; additional photos appear in the product gallery.

Photos are uploaded to Laravel's `public` filesystem disk. The `storage:link` step makes them reachable by the browser.

### Manage your listings

Open **My Listings** to view your own listings. The **All**, **Available**, and **Sold** filters narrow the table. Use **Edit** to change listing details or replace its photo gallery, **Mark sold** to change its availability, and **Delete** to remove it.

Selecting new photos while editing replaces the current gallery. Leaving the photo picker empty keeps the existing photos.

### Save favorites

Use the heart control on a product page to save or remove that listing. Select **Favorites** to see the listings saved by your account. Other users cannot see your saved list.

### Message a seller

1. Open a listing and select **Message seller**.
2. Enter a message and select **Send**. Press Enter to submit; Shift+Enter inserts a line break.
3. The message is stored and appears in both participants' inboxes and conversation history.
4. Opening a conversation marks incoming messages as read.

Messages are private to the two participants. Users cannot start a conversation with themselves.

## Application Pages and Routes

All marketplace pages require authentication. Guests are redirected to sign-in.

| Page or action | Route | Purpose |
| --- | --- | --- |
| Home | `/` | Redirects to the dashboard when signed in, otherwise to sign-in. |
| Sign-in | `/login` | Sign in to an existing account. |
| Registration | `/register` | Create an account. |
| Dashboard | `/dashboard` | Browse and search listings. |
| Product detail | `/products/{slug}` | Inspect an item, favorite it, or contact its seller. |
| Seller profile | `/seller/{id}` | View a seller's profile and listings. |
| Favorites | `/favorites` | View the current user's saved listings. |
| My Listings | `/my-listings` | Manage the signed-in user's listings. |
| Create listing | `/sell` | Open the listing form. |
| Messages inbox | `/messages` | View conversations. |
| Conversation | `/messages/{id}` | View a conversation and send text messages. |
| Sign out | `POST /logout` | End the current session. |

## Data and Storage

- `users`: account identity and location/contact information.
- `products`: marketplace listing details and the cover-image path.
- `product_images`: ordered gallery images associated with a product.
- `favorites`: the user/product pairs saved by each account.
- `messages`: sender, receiver, message text, timestamps, and read state.
- Uploaded listing photos are stored under `storage/app/public/products` by default.

Migrations are in `database/migrations`. Sample users and product listings are created by `database/seeders/DatabaseSeeder.php`.

## Tests

Run all feature and unit tests with:

```bash
php artisan test --compact
```

The tests use an in-memory SQLite database and cover authentication-facing marketplace flows, listing CRUD, multi-photo uploads and cleanup, favorites, seller visibility, and message persistence/privacy.

## Troubleshooting

- **Uploaded images do not load:** run `php artisan storage:link` and confirm the `public/storage` link exists. Check that the configured public disk is writable.
- **Database connection fails:** confirm the database exists and `.env` has the correct connection values. For SQLite, ensure `database/database.sqlite` exists and is writable.
- **A migration is pending:** run `php artisan migrate` after updating the code.
- **Changes to `.env` have no effect:** clear cached configuration with `php artisan config:clear`.

## Current Limitations

- Seller verification, profile ratings, response-time statistics, and profile photos are not stored yet. The profile page avoids presenting invented values for these fields.
- Conversation records are grouped by the other participant and are not associated with a specific product. The conversation sidebar can show the seller's latest available listing, not necessarily the exact item that started that conversation.
- Chat supports text messages only. Attachments and real-time updates are not implemented.
