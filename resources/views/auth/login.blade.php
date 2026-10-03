<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Sign in — Marketly</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>tailwind.config={theme:{extend:{colors:{ink:'#121826',mist:'#F6F7FB',cobalt:'#3157E5'}}}}</script>
    </head>
    <body class="bg-mist text-ink">
        <main class="grid min-h-screen lg:grid-cols-2">
            <section class="hidden bg-ink p-12 text-white lg:flex lg:flex-col lg:justify-between">
                <a href="{{ route('dashboard') }}" class="text-2xl font-black">Marketly</a>
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-slate-400">Simple local marketplace</p>
                    <h1 class="mt-4 max-w-xl text-5xl font-black leading-tight">Find what you need. Sell what you don’t.</h1>
                    <p class="mt-5 max-w-lg text-slate-300">One account lets you browse, save, message sellers and create your own listings.</p>
                </div>
                <p class="text-sm text-slate-500">Buy better. Sell simply.</p>
            </section>
            <section class="flex items-center justify-center px-5 py-10">
                <div class="w-full max-w-md"><a href="index.html" class="font-black lg:hidden">Marketly</a>
                    <h2 class="mt-8 text-3xl font-black">Welcome back</h2>
                    <p class="mt-2 text-slate-500">Sign in to continue to your marketplace.</p>
                    <form class="mt-8 space-y-5" action="{{route('login.store')}}" method="post">
                        @csrf
                        <div>
                            <label class="font-bold">Email</label>
                            <input type="email"  name="email"  value="{{old('email')}}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="you@example.com">
                            @error('email')
                        <div class="text-red-500" >{{$message}}</div>
                        @enderror
                        </div>
                        <div>
                            <div class="flex justify-between">
                                <label class="font-bold">Password</label>
                                <a href="#" class="text-sm font-bold text-cobalt">Forgot?</a>
                            </div>
                            <input type="password" name="password" value="{{old('password')}}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="••••••••">
                            @error('password')
                        <div class="text-red-500" >{{$message}}</div>
                        @enderror
                        </div>
                            <button class="w-full rounded-2xl bg-ink py-3.5 font-bold text-white">Sign in</button>
                        </form>
                        <p class="mt-6 text-center text-sm text-slate-500">New to Marketly?
                            <a href="{{ route('register') }}" class="font-bold text-cobalt">Create account</a>
                        </p>
                    </div>
                </section>
            </main>
        </body>
        </html>
