# Marketly Mini Marketplace UI

HTML + Tailwind CSS marketplace prototype.

## Pages
- `dashboard.blade.php` — marketplace browse/search/filter
- `product.blade.php` — product details
- `seller-profile.blade.php` — public seller profile and seller listings
- `messages.blade.php` — inbox / message list with search
- `conversation.blade.php` — buyer-seller message box with demo send interaction
- `favorites.blade.php` — saved items
- `my-listings.blade.php` — current user's own listings
- `sell.blade.php` — create/edit listing UI
- `loin.blade.php` — sign in
- `register.blade.php` — create account

## Seller/message flow
`Product Detail → View Seller Profile → Message Seller → Conversation`

The chat send behavior is front-end demo JavaScript only. Laravel/database integration can replace it later.
