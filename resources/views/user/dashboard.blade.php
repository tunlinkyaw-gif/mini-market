<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Marketly — Mini Marketplace</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { ink:'#121826', mist:'#F6F7FB', cobalt:'#3157E5', lime:'#DDF76F' }, boxShadow:{soft:'0 12px 40px rgba(18,24,38,.08)'} } } }
  </script>
</head>
<body class="bg-mist text-ink">
<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
  <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-black text-xl"><span class="grid h-9 w-9 place-items-center rounded-xl bg-ink text-white">M</span>Marketly</a>
    <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
      <a href="{{ route('dashboard') }}" class="text-ink">Browse</a>
      <a href="{{ route('favorites') }}" class="hover:text-ink">Favorites</a>
      <a href="{{ route('messages') }}" class="hover:text-ink">Messages</a>
      <a href="{{ route('my-listings') }}" class="hover:text-ink">My Listings</a>
    </nav>
    <div class="flex items-center gap-3">
      <a href="{{ route('sell') }}" class="rounded-xl bg-ink px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-800">+ Sell item</a>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Logout</button>
      </form>
    </div>
  </div>
</header>

<main>
<section class="mx-auto max-w-7xl px-5 pt-10 pb-8">
  <div class="grid gap-8 lg:grid-cols-[1.15fr_.85fr] lg:items-center">
    <div>
      <span class="inline-flex rounded-full bg-lime px-3 py-1 text-xs font-bold uppercase tracking-wide">Buy better. Sell simply.</span>
      <h1 class="mt-5 max-w-3xl text-4xl font-black leading-tight sm:text-6xl">Great finds from people around you.</h1>
      <p class="mt-4 max-w-2xl text-lg text-slate-600">A simple local marketplace for electronics, books, fashion, furniture and more.</p>
      <div class="mt-7 flex flex-wrap gap-3">
        <a href="{{ route('sell') }}" class="inline-flex items-center rounded-xl bg-ink px-5 py-3 font-bold text-white shadow-soft hover:bg-slate-800">Sell item</a>
        <a href="{{ route('my-listings') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-5 py-3 font-bold text-slate-700 hover:bg-slate-50">View my listings</a>
      </div>
      <div class="mt-7 flex max-w-2xl rounded-2xl border border-slate-200 bg-white p-2 shadow-soft">
        <input id="searchInput" class="min-w-0 flex-1 px-4 py-3 outline-none" placeholder="Search for iPhone, desk, book..." />
        <button id="searchBtn" class="rounded-xl bg-cobalt px-5 py-3 font-bold text-white">Search</button>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <img class="h-56 w-full rounded-3xl object-cover" src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=800&q=80" alt="Phone">
      <img class="mt-10 h-56 w-full rounded-3xl object-cover" src="https://images.unsplash.com/photo-1519947486511-46149fa0a254?auto=format&fit=crop&w=800&q=80" alt="Chair">
    </div>
  </div>
</section>

<section class="mx-auto max-w-7xl px-5 py-5">
  <div class="flex flex-wrap gap-2" id="categoryFilters">
    <button data-filter="all" class="filter-btn rounded-full bg-ink px-4 py-2 text-sm font-bold text-white">All</button>
    <button data-filter="electronics" class="filter-btn rounded-full bg-white px-4 py-2 text-sm font-semibold">Electronics</button>
    <button data-filter="fashion" class="filter-btn rounded-full bg-white px-4 py-2 text-sm font-semibold">Fashion</button>
    <button data-filter="books" class="filter-btn rounded-full bg-white px-4 py-2 text-sm font-semibold">Books</button>
    <button data-filter="furniture" class="filter-btn rounded-full bg-white px-4 py-2 text-sm font-semibold">Furniture</button>
    <button data-filter="sports" class="filter-btn rounded-full bg-white px-4 py-2 text-sm font-semibold">Sports</button>
  </div>
</section>

<section class="mx-auto max-w-7xl px-5 py-8">
  <div class="mb-6 flex items-end justify-between"><div><p class="text-sm font-semibold text-cobalt">Fresh listings</p><h2 class="text-2xl font-black">Explore the marketplace</h2></div><select id="sortSelect" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"><option value="newest">Newest</option><option value="low">Price: low to high</option><option value="high">Price: high to low</option></select></div>
  <div id="productGrid" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ($products as $product)
      <a href="{{ route('products.show', $product['slug']) }}" data-category="{{ $product['category'] }}" data-price="{{ $product['price'] }}" class="product-card group overflow-hidden rounded-2xl bg-white shadow-soft">
        <div class="relative">
          <img class="h-56 w-full object-cover" src="{{ $product['image_url'] ?? $product['image'] }}" alt="{{ $product['name'] }}">
          <span class="absolute left-3 top-3 rounded-full bg-white px-2.5 py-1 text-xs font-bold">{{ $product['status'] }}</span>
        </div>
        <div class="p-4">
          <h3 class="font-bold group-hover:text-cobalt">{{ $product['name'] }}</h3>
          <p class="mt-1 text-sm text-slate-500">{{ $product['location'] }} · 2h ago</p>
          <p class="mt-4 text-xl font-black">{{ number_format($product['price']) }} MMK</p>
        </div>
      </a>
    @endforeach
  </div>
  <div id="emptyState" class="hidden rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center text-slate-500">No listings found for this filter.</div>
</section>
</main>
<footer class="mt-12 border-t border-slate-200 bg-white"><div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between"><span>© 2026 Marketly</span><span>Simple local buying & selling</span></div></footer>
<script>
const buttons=[...document.querySelectorAll('.filter-btn')], cards=[...document.querySelectorAll('.product-card')], empty=document.getElementById('emptyState'), grid=document.getElementById('productGrid');
let current='all';
function apply(){const q=document.getElementById('searchInput').value.toLowerCase().trim();let visible=0;cards.forEach(c=>{const cat=c.dataset.category, text=c.innerText.toLowerCase();const show=(current==='all'||cat===current)&&(!q||text.includes(q));c.style.display=show?'block':'none';if(show)visible++;});empty.classList.toggle('hidden',visible!==0);grid.classList.toggle('hidden',visible===0);}
buttons.forEach(b=>b.addEventListener('click',()=>{current=b.dataset.filter;buttons.forEach(x=>{x.classList.remove('bg-ink','text-white');x.classList.add('bg-white','text-ink')});b.classList.add('bg-ink','text-white');b.classList.remove('bg-white','text-ink');apply();}));
document.getElementById('searchBtn').addEventListener('click',apply);document.getElementById('searchInput').addEventListener('input',apply);
document.getElementById('sortSelect').addEventListener('change',e=>{const visibleCards=cards.slice().sort((a,b)=>e.target.value==='low'?a.dataset.price-b.dataset.price:e.target.value==='high'?b.dataset.price-a.dataset.price:0);visibleCards.forEach(c=>grid.appendChild(c));});
</script>
</body></html>
