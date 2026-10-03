<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Create account — Marketly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = {theme: {extend: {colors: {ink: '#121826', mist: '#F6F7FB', cobalt: '#3157E5'}}}}</script>
</head>

<body class="bg-mist text-ink">
    <main class="min-h-screen">
        <div class="mx-auto grid min-h-screen max-w-6xl lg:grid-cols-[.8fr_1.2fr]">
            <aside class="hidden p-10 lg:flex lg:flex-col lg:justify-between"><a href="index.html"
                    class="text-2xl font-black">Marketly</a>
                <div class="rounded-3xl bg-cobalt p-8 text-white">
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-100">One account</p>
                    <h1 class="mt-4 text-4xl font-black">Buy and sell with the same profile.</h1>
                    <ul class="mt-6 space-y-3 text-blue-50">
                        <li>✓ Save favorite listings</li>
                        <li>✓ Message buyers and sellers</li>
                        <li>✓ Publish and manage your own items</li>
                    </ul>
                </div>
                <p class="text-sm text-slate-400">Local, simple, useful.</p>
            </aside>
            <section class="flex items-center px-5 py-10 sm:px-10">
                <div class="w-full max-w-xl"><a href="index.html" class="font-black lg:hidden">Marketly</a>
                    <h2 class="mt-6 text-3xl font-black">Create your account</h2>
                    <p class="mt-2 text-slate-500">You can buy and sell with the same account.</p>
                    <form class="mt-8 grid gap-5 sm:grid-cols-2" method="post" action="{{route('register.store')}}">
                        @csrf
                        <div class="sm:col-span-2"><label class="font-bold" >Full name</label>
                        <input name="name" value="{{old('name')}}" class="mt-2 w-full rounded-2xl border bg-white px-4 py-3" placeholder="Your name">
                        @error('name')
                        <div class="text-red-500">{{$message}}</div>
                        @enderror
                            </div>
                        <div class="sm:col-span-2">
                            <label class="font-bold" >Email</label>
                            <input type="email" name="email" value="{{old('email')}}"
                                class="mt-2 w-full rounded-2xl border bg-white px-4 py-3" placeholder="you@example.com">
                                @error('email')
                        <div class="text-red-500" >{{$message}}</div>
                        @enderror
                        </div>
                        <div>
                            <label class="font-bold" >Phone</label>
                            <input name="phone" value="{{old('phone')}}" class="mt-2 w-full rounded-2xl border bg-white px-4 py-3" placeholder="09xxxxxxxxx">
                            @error('phone')
                        <div class="text-red-500" >{{$message}}</div>
                        @enderror
                        </div>
                        <div>
                            <label class="font-bold">Location</label>
                            <input name="location" value="{{old('location')}}"
                                class="mt-2 w-full rounded-2xl border bg-white px-4 py-3" placeholder="Township">
                                @error('location')
                        <div class="text-red-500">{{$message}}</div>
                        @enderror</div>
                                
                        <div>
                            <label class="font-bold">Password</label>
                            <input type="password" name="password" value="{{old('password')}}" class="mt-2 w-full rounded-2xl border bg-white px-4 py-3" placeholder="••••••••">
                            @error('password')
                        <div class="text-red-500" >{{$message}}</div>
                        @enderror
                    </div>
                                
                        <div>
                            <label class="font-bold" >Confirm password</label>
                            <input type="password" name="password_confirmation" value="{{old('password_confirmation')}}" class="mt-2 w-full rounded-2xl border bg-white px-4 py-3" placeholder="••••••••">
                            @error('password_confirmation')
                        <div class="text-red-500">{{$message}}</div>
                        @enderror</div>
                        <button class="sm:col-span-2 rounded-2xl bg-ink py-3.5 font-bold text-white">Create account</button>
                    </form>
                    <p class="mt-6 text-sm text-slate-500">Already have an account? <a href="/login"
                            class="font-bold text-cobalt">Sign in</a></p>
                </div>
            </section>
        </div>
    </main>
</body>

</html>