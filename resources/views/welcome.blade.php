{{-- <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $c['site_name'] }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($c['hero_subtitle'], 150) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <style>
        :root { --green: #0f5f46; --green-d: #0b4a37; --orange: #f58220; --clay: #d17b3f; --beige: #f1e6d6; --sage: #8fb3a5; --ink: #1f2937; }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-padding-top: 4.5rem; }
        body { margin: 0; font-family: 'Lato', system-ui, sans-serif; color: var(--ink); background: #fff; line-height: 1.6; }
        a { color: inherit; text-decoration: none; }
        .container { max-width: 68rem; margin: 0 auto; padding: 0 1.25rem; }

        /* Header */
        .header { position: fixed; top: 0; left: 0; right: 0; z-index: 50; transition: background .3s, box-shadow .3s; }
        .header.scrolled, .header.open { background: var(--green); box-shadow: 0 2px 12px rgba(0,0,0,.25); }
        .bar { display: flex; align-items: center; justify-content: space-between; height: 4.5rem; }
        .logo { font-size: 1.7rem; font-weight: 900; color: var(--orange); letter-spacing: .02em; text-shadow: 0 1px 6px rgba(0,0,0,.3); }
        .nav { display: flex; align-items: center; gap: 1.5rem; }
        .nav a.link { color: #fff; font-weight: 700; font-size: .95rem; padding: .35rem 0; border-bottom: 2px solid transparent; text-shadow: 0 1px 4px rgba(0,0,0,.35); }
        .header.scrolled .nav a.link, .header.open .nav a.link { color: var(--orange); text-shadow: none; }
        .nav a.link.active { border-bottom-color: var(--orange); }
        .btn-login { border: 2px solid #fff; color: #fff; padding: .4rem 1.1rem; border-radius: .4rem; font-weight: 700; font-size: .9rem; }
        .btn-login:hover { background: #fff; color: var(--green); }
        .header.scrolled .btn-login, .header.open .btn-login { border-color: var(--orange); color: var(--orange); }
        .header.scrolled .btn-login:hover, .header.open .btn-login:hover { background: var(--orange); color: #fff; }
        .burger { display: none; background: none; border: 2px solid #fff; color: #fff; padding: .3rem .7rem; border-radius: .4rem; font-weight: 700; cursor: pointer; }

        /* Hero */
        .hero { position: relative; min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; color: #fff;
                background: linear-gradient(135deg, var(--green-d), var(--green)); overflow: hidden; }
        .slide { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: 0; transition: opacity 1s; }
        .slide.active { opacity: 1; }
        .slide video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .hero::after { content: ""; position: absolute; inset: 0; background: linear-gradient(rgba(0,0,0,.25), rgba(0,0,0,.5)); }
        .hero-inner { position: relative; z-index: 2; padding: 6rem 1.25rem 4rem; }
        .hero-title { display: inline-block; margin: 0; padding: .6rem 1.5rem; background: rgba(15,95,70,.88); font-size: clamp(1.5rem, 4vw, 2.6rem); font-weight: 900; text-transform: uppercase; }
        .hero-sub { display: block; width: fit-content; margin: .4rem auto 1.6rem; padding: .45rem 1.2rem; background: rgba(15,95,70,.85); font-size: clamp(1rem, 2vw, 1.3rem); }
        .hero-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn { display: inline-block; padding: .8rem 1.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; border-radius: .2rem; }
        .btn-outline { border: 2px solid #fff; color: #fff; background: rgba(0,0,0,.25); }
        .btn-outline:hover { background: #fff; color: var(--green); }
        .btn-fill { background: var(--clay); color: #fff; }
        .btn-fill:hover { background: #b9672f; }
        .arrow { position: absolute; top: 50%; transform: translateY(-50%); z-index: 3; width: 3rem; height: 3rem; border: 2px solid #fff; background: transparent; color: #fff; font-size: 1.5rem; cursor: pointer; }
        .arrow:hover { background: rgba(255,255,255,.2); }
        .arrow.prev { left: 1.5rem; }
        .arrow.next { right: 1.5rem; }
        .scroll-down { position: absolute; bottom: 1.2rem; left: 50%; transform: translateX(-50%); z-index: 3; color: #fff; font-size: 2rem; }

        /* Section umum */
        .section { padding: 5rem 0; }
        .section h2 { text-align: center; font-size: clamp(1.8rem, 4vw, 2.8rem); font-weight: 300; margin: 0 0 2rem; }
        .section h2 b { font-weight: 900; }
        #about { background: var(--sage); color: #fff; }
        #menu { background: #faf6ef; color: var(--green); }
        #kontak { background: var(--beige); color: var(--green); }
        #galeri { background: #fff; color: var(--green); }
        .hours { text-align: center; font-size: 1.2rem; font-weight: 700; margin: 0 0 1.2rem; }
        .about-text { max-width: 46rem; margin: 0 auto; white-space: pre-line; text-align: justify; font-size: 1.05rem; }
        .empty { text-align: center; opacity: .7; }

        /* Bingkai foto */
        .frame { background: var(--beige); padding: .75rem; display: grid; gap: .75rem; grid-template-columns: repeat(auto-fill, minmax(14rem, 1fr)); }
        .frame figure { margin: 0; position: relative; overflow: hidden; cursor: zoom-in; background: #ddd; }
        .frame img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; display: block; transition: transform .4s; }
        .menu-frame img { aspect-ratio: 3 / 4; }
        .frame figure:hover img { transform: scale(1.05); }
        .frame figcaption { position: absolute; left: 0; right: 0; bottom: 0; padding: 1.5rem .8rem .6rem; color: #fff; font-weight: 700; font-size: .95rem; background: linear-gradient(transparent, rgba(0,0,0,.7)); }

        /* Kontak */
        .contact { text-align: center; }
        .btn-wa { display: inline-flex; align-items: center; gap: .7rem; padding: .9rem 2rem; background: var(--green); color: var(--orange); border-radius: .4rem; font-weight: 900; text-transform: uppercase; letter-spacing: .03em; }
        .btn-wa:hover { background: var(--green-d); }
        .btn-maps { display: inline-block; margin-top: .6rem; padding: .5rem 1.3rem; border: 2px solid var(--green); border-radius: .4rem; font-weight: 700; }
        .btn-maps:hover { background: var(--green); color: #fff; }

        /* Reservasi & Peta */
        .contact-grid { display: grid; gap: 2rem; grid-template-columns: 1fr 1fr; align-items: start; text-align: left; }
        .card { background: #fff; border-radius: .6rem; padding: 1.5rem; box-shadow: 0 4px 16px rgba(0,0,0,.08); }
        .card h3 { margin: 0 0 1rem; font-size: 1.1rem; text-transform: uppercase; letter-spacing: .05em; }
        .rsv-form { display: grid; gap: .9rem; grid-template-columns: 1fr 1fr; }
        .rsv-form .full { grid-column: 1 / -1; }
        .rsv-form label { display: grid; gap: .25rem; font-size: .85rem; font-weight: 700; }
        .rsv-form input, .rsv-form textarea { font: inherit; padding: .6rem .7rem; border: 1px solid #cdbfa8; border-radius: .35rem; background: #fffdf9; color: var(--ink); width: 100%; }
        .rsv-form input:focus, .rsv-form textarea:focus { outline: 2px solid var(--orange); border-color: var(--orange); }
        .rsv-form button { cursor: pointer; border: 0; font: inherit; font-weight: 900; }
        .map-wrap { overflow: hidden; border-radius: .6rem; box-shadow: 0 4px 16px rgba(0,0,0,.08); background: #ddd; }
        .map-wrap iframe { display: block; width: 100%; height: 26rem; border: 0; }
        .map-info { background: #fff; padding: 1rem 1.25rem; }
        .map-info h3 { margin: 0 0 .3rem; font-size: 1rem; text-transform: uppercase; letter-spacing: .05em; }
        .map-info p { margin: 0 0 .5rem; white-space: pre-line; }
        .note { font-size: .85rem; opacity: .75; }

        #lokasi { background: #faf6ef; color: var(--green); }
        #ulasan { background: var(--beige); color: var(--green); }

        /* Ulasan */
        .stars { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: .2rem; }
        .rsv-form .stars input { position: absolute; opacity: 0; width: 0; height: 0; padding: 0; border: 0; }
        .rsv-form .stars label { display: block; font-size: 2rem; line-height: 1; color: #cdbfa8; cursor: pointer; font-weight: 400; }
        .stars input:checked ~ label, .stars label:hover, .stars label:hover ~ label { color: var(--orange); }
        .hp { position: absolute; left: -9999px; height: 0; overflow: hidden; }
        .review-list { display: grid; gap: 1rem; }
        .review { background: #fff; border-radius: .6rem; padding: 1.1rem 1.25rem; box-shadow: 0 4px 16px rgba(0,0,0,.08); }
        .review-head { display: flex; justify-content: space-between; align-items: baseline; gap: .5rem; flex-wrap: wrap; }
        .review-name { font-weight: 900; }
        .review-stars { color: var(--orange); letter-spacing: .1em; }
        .review p { margin: .4rem 0 0; white-space: pre-line; color: var(--ink); }
        .review small { opacity: .6; }
        .alert-ok { background: #e3f4ea; color: #0b4a37; padding: .7rem 1rem; border-radius: .4rem; margin-bottom: 1rem; font-weight: 700; }
        .alert-err { background: #fdeaea; color: #a12626; padding: .7rem 1rem; border-radius: .4rem; margin-bottom: 1rem; font-size: .9rem; }

        .footer { background: var(--green-d); color: #cfe3da; text-align: center; padding: 1.5rem 1rem; font-size: .9rem; }

        /* Pembesar gambar */
        .lightbox { position: fixed; inset: 0; z-index: 100; display: none; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(0,0,0,.88); cursor: zoom-out; }
        .lightbox.show { display: flex; }
        .lightbox img { max-width: 100%; max-height: 100%; object-fit: contain; }

        @media (max-width: 860px) {
            .burger { display: block; }
            .nav { display: none; position: absolute; top: 4.5rem; left: 0; right: 0; flex-direction: column; align-items: stretch;
                   gap: .4rem; padding: 1rem 1.25rem 1.25rem; background: var(--green); }
            .header.open .nav { display: flex; }
            .nav a.link { color: var(--orange); padding: .5rem 0; }
            .btn-login { text-align: center; border-color: var(--orange); color: var(--orange); }
            .arrow { width: 2.4rem; height: 2.4rem; }
            .arrow.prev { left: .6rem; }
            .arrow.next { right: .6rem; }
            .contact-grid { grid-template-columns: 1fr; }
            .rsv-form { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

@php
    $waNumber = \App\Models\SiteContent::whatsappNumber($c);
    $mapEmbed = \App\Models\SiteContent::mapEmbedUrl($c);
    $reviews     = \App\Models\Review::approved()->latest()->take(12)->get();
    $reviewCount = \App\Models\Review::approved()->count();
    $reviewAvg   = $reviewCount ? round(\App\Models\Review::approved()->avg('rating'), 1) : 0;
@endphp

{{-- Navbar --}}
<header id="header" class="header">
    <div class="container bar">
        <a href="#home" class="logo">{{ $c['site_name'] }}</a>

        <button type="button" id="burger" class="burger" aria-label="Menu">Menu</button>

        <nav class="nav">
            <a href="#home" class="link active">Beranda</a>
            <a href="#about" class="link">Tentang</a>
            <a href="#menu" class="link">Menu</a>
            <a href="#kontak" class="link">Kontak &amp; Reservasi</a>
            <a href="#lokasi" class="link">Lokasi</a>
            <a href="#galeri" class="link">Galeri</a>
            <a href="#ulasan" class="link">Ulasan</a>

            @auth
                <a href="{{ auth()->user()->homeRoute() }}" class="btn-login">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-login">Masuk</a>
            @endauth
        </nav>
    </div>
</header>

{{-- Hero --}}
<section id="home" class="hero">
    @foreach ($slides as $slide)
        <div class="slide {{ $loop->first ? 'active' : '' }}" style="background-image:url('{{ $slide->image_url }}')">
            @if ($slide->video_url)
                <video muted playsinline preload="metadata" poster="{{ $slide->image_url }}">
                    <source src="{{ $slide->video_url }}" type="{{ str_ends_with($slide->video, '.webm') ? 'video/webm' : 'video/mp4' }}">
                </video>
            @endif
        </div>
    @endforeach

    @if ($slides->count() > 1)
        <button type="button" id="prev" class="arrow prev" aria-label="Sebelumnya">&#10094;</button>
        <button type="button" id="next" class="arrow next" aria-label="Berikutnya">&#10095;</button>
    @endif

    <div class="hero-inner">
        <h1 class="hero-title">{{ $c['hero_title'] }}</h1>
        @if ($c['hero_subtitle'])
            <span class="hero-sub">{{ $c['hero_subtitle'] }}</span>
        @endif

        <div class="hero-actions">
            <a href="#about" class="btn btn-outline">Tentang Kami</a>
            <a href="#kontak" class="btn btn-fill">Reservasi</a>
        </div>
    </div>

    <a href="#about" class="scroll-down" aria-label="Gulir ke bawah">&#8964;</a>
</section>

{{-- Tentang --}}
<section id="about" class="section">
    <div class="container">
        @if ($c['open_hours'])
            <p class="hours">{{ $c['open_hours'] }}</p>
        @endif
        <h2>{{ $c['about_title'] }}</h2>
        <div class="about-text">{{ $c['about_text'] }}</div>
    </div>
</section>

{{-- Menu --}}
<section id="menu" class="section">
    <div class="container">
        <h2>Menu <b>{{ $c['site_name'] }}</b></h2>

        @if ($menus->isNotEmpty())
            <div class="frame menu-frame">
                @foreach ($menus as $item)
                    <figure data-zoom="{{ $item->image_url }}">
                        <img src="{{ $item->image_url }}" alt="{{ $item->title ?: 'Menu' }}" loading="lazy">
                        @if ($item->title)
                            <figcaption>{{ $item->title }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        @else
            <p class="empty">Menu akan segera hadir.</p>
        @endif
    </div>
</section>

{{-- Kontak & Reservasi --}}
<section id="kontak" class="section">
    <div class="container contact">
        <h2>Kontak <b>&amp; Reservasi</b></h2>

        <div class="contact-grid">
            {{-- Form reservasi --}}
            <div class="card">
                <h3>Reservasi Meja</h3>

                @if ($waNumber)
                    <form id="rsv-form" class="rsv-form">
                        <label class="full">Nama
                            <input type="text" name="nama" required maxlength="60" placeholder="Nama Anda">
                        </label>
                        <label>Tanggal
                            <input type="date" name="tanggal" required>
                        </label>
                        <label>Jam
                            <input type="time" name="jam" required>
                        </label>
                        <label class="full">Jumlah Orang
                            <input type="number" name="jumlah" required min="1" max="100" value="2">
                        </label>
                        <label class="full">Catatan (opsional)
                            <textarea name="catatan" rows="3" maxlength="300" placeholder="Contoh: dekat jendela, ada anak kecil"></textarea>
                        </label>
                        <button type="submit" class="btn-wa full" style="justify-content:center">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20z"/></svg>
                            Kirim Reservasi via WhatsApp
                        </button>
                    </form>
                    <p class="note" style="margin-top:.8rem">Anda akan diarahkan ke WhatsApp. Reservasi dikonfirmasi setelah kami membalas pesan Anda.</p>
                @else
                    <p class="empty">Reservasi via WhatsApp akan segera tersedia.</p>
                @endif

                @if ($c['open_hours'])
                    <p style="margin:1.2rem 0 0"><b>Jam Buka:</b> {{ $c['open_hours'] }}</p>
                @endif
            </div>

            {{-- Info kontak --}}
            <div class="card">
                <h3>Informasi</h3>
                @if ($c['address'])
                    <p><b>Alamat</b><br>{{ $c['address'] }}</p>
                @endif
                @if ($c['open_hours'])
                    <p><b>Jam Buka</b><br>{{ $c['open_hours'] }}</p>
                @endif
                <div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-top:1rem">
                    @if ($waUrl)
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="btn-wa" style="padding:.6rem 1.2rem">Chat WhatsApp</a>
                    @endif
                    <a href="#lokasi" class="btn-maps" style="margin-top:0">Lihat Peta</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Lokasi --}}
<section id="lokasi" class="section">
    <div class="container">
        <h2>Lokasi <b>{{ $c['site_name'] }}</b></h2>

        @if ($mapEmbed)
            <div class="map-wrap">
                <iframe src="{{ $mapEmbed }}" loading="lazy" allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Lokasi {{ $c['site_name'] }}"></iframe>
                <div class="map-info">
                    @if ($c['address'])
                        <h3>Alamat</h3>
                        <p>{{ $c['address'] }}</p>
                    @endif
                    @if ($c['maps_url'])
                        <a href="{{ $c['maps_url'] }}" target="_blank" rel="noopener" class="btn-maps">Buka di Google Maps</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>

{{-- Galeri --}}
<section id="galeri" class="section">
    <div class="container">
        <h2>Galeri <b>{{ $c['site_name'] }}</b></h2>

        @if ($galleries->isNotEmpty())
            <div class="frame">
                @foreach ($galleries as $photo)
                    <figure data-zoom="{{ $photo->image_url }}">
                        <img src="{{ $photo->image_url }}" alt="{{ $photo->title ?: 'Foto galeri' }}" loading="lazy">
                        @if ($photo->title)
                            <figcaption>{{ $photo->title }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        @else
            <p class="empty">Foto akan segera hadir.</p>
        @endif
    </div>
</section>

{{-- Ulasan --}}
<section id="ulasan" class="section">
    <div class="container">
        <h2>Ulasan <b>Pelanggan</b></h2>

        @if ($reviewCount)
            <p class="hours"><span class="review-stars">&#9733;</span> {{ $reviewAvg }} / 5 &middot; {{ $reviewCount }} ulasan</p>
        @endif

        <div class="contact-grid">
            <div class="card">
                <h3>Tulis Ulasan</h3>

                @if (session('review_ok'))
                    <div class="alert-ok">{{ session('review_ok') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert-err">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('reviews.store') }}" class="rsv-form">
                    @csrf
                    <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">

                    <label class="full">Nama
                        <input type="text" name="name" required maxlength="60" value="{{ old('name') }}" placeholder="Nama Anda">
                    </label>

                    <div class="full">
                        <div style="font-size:.85rem;font-weight:700;margin-bottom:.25rem">Penilaian</div>
                        <div class="stars">
                            @for ($i = 5; $i >= 1; $i--)
                                <input type="radio" id="st{{ $i }}" name="rating" value="{{ $i }}" required @checked(old('rating') == $i)>
                                <label for="st{{ $i }}" title="{{ $i }} bintang">&#9733;</label>
                            @endfor
                        </div>
                    </div>

                    <label class="full">Ulasan
                        <textarea name="comment" rows="4" required minlength="5" maxlength="500" placeholder="Ceritakan pengalaman Anda">{{ old('comment') }}</textarea>
                    </label>

                    <button type="submit" class="btn btn-fill full">Kirim Ulasan</button>
                </form>
            </div>

            <div class="review-list">
                @forelse ($reviews as $r)
                    <div class="review">
                        <div class="review-head">
                            <span class="review-name">{{ $r->name }}</span>
                            <span class="review-stars">@for ($i = 1; $i <= 5; $i++){{ $i <= $r->rating ? '★' : '☆' }}@endfor</span>
                        </div>
                        <p>{{ $r->comment }}</p>
                        <small>{{ $r->created_at->format('d/m/Y') }}</small>
                    </div>
                @empty
                    <p class="empty">Belum ada ulasan. Jadilah yang pertama!</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

<footer class="footer">&copy; {{ date('Y') }} {{ $c['site_name'] }}. Semua hak dilindungi.</footer>

<div id="lightbox" class="lightbox"><img src="" alt=""></div>

<script>
(function () {
    const header = document.getElementById('header');

    // Navbar berubah hijau saat di-scroll
    const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 40);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    // Menu HP
    document.getElementById('burger').addEventListener('click', () => header.classList.toggle('open'));
    document.querySelectorAll('.nav a').forEach((a) =>
        a.addEventListener('click', () => header.classList.remove('open')));

    // Slider hero (mendukung slide video)
    const slides = [...document.querySelectorAll('.slide')];
    let current = 0;
    let timer = null;

    const videoOf = (slide) => slide.querySelector('video');

    function startCurrent() {
        clearTimeout(timer);
        const video = videoOf(slides[current]);

        if (!video) {
            // slide gambar: pindah tiap 5 detik
            if (slides.length > 1) timer = setTimeout(() => show(current + 1), 5000);
            return;
        }

        video.muted = true;
        video.currentTime = 0;
        video.loop = slides.length === 1;            // satu-satunya slide: diulang terus
        video.onended = () => show(current + 1);     // beberapa slide: lanjut setelah video selesai

        video.play().catch(() => {
            // pemutaran otomatis diblokir: tampilkan sampul lalu lanjut
            if (slides.length > 1) timer = setTimeout(() => show(current + 1), 5000);
        });
    }

    function show(n) {
        if (!slides.length) return;

        const old = videoOf(slides[current]);
        if (old) old.pause();

        slides[current].classList.remove('active');
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');

        startCurrent();
    }

    document.getElementById('prev')?.addEventListener('click', () => show(current - 1));
    document.getElementById('next')?.addEventListener('click', () => show(current + 1));

    if (slides.length) startCurrent();

    // Tandai menu yang sedang dilihat
    const links = [...document.querySelectorAll('.nav a.link')];
    const sections = links.map((l) => document.querySelector(l.getAttribute('href'))).filter(Boolean);
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (!e.isIntersecting) return;
            links.forEach((l) => l.classList.toggle('active', l.getAttribute('href') === '#' + e.target.id));
        });
    }, { rootMargin: '-45% 0px -50% 0px' });
    sections.forEach((s) => observer.observe(s));

    // Pembesar gambar
    const box = document.getElementById('lightbox');
    const boxImg = box.querySelector('img');

    document.querySelectorAll('[data-zoom]').forEach((fig) =>
        fig.addEventListener('click', () => { boxImg.src = fig.dataset.zoom; box.classList.add('show'); }));
    box.addEventListener('click', () => box.classList.remove('show'));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') box.classList.remove('show'); });

    @if (session('review_ok') || $errors->any())
    document.getElementById('ulasan').scrollIntoView();
    @endif

    // Form reservasi -> WhatsApp
    const form = document.getElementById('rsv-form');
    if (form) {
        const WA_NUMBER = @json($waNumber);
        const WA_INTRO  = @json($c['whatsapp_message']);

        // Tanggal minimal hari ini
        const today = new Date();
        today.setMinutes(today.getMinutes() - today.getTimezoneOffset());
        form.tanggal.min = today.toISOString().slice(0, 10);

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const tgl = new Date(form.tanggal.value + 'T00:00:00').toLocaleDateString('id-ID', {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
            });

            const lines = [
                WA_INTRO,
                '',
                'Nama: ' + form.nama.value.trim(),
                'Tanggal: ' + tgl,
                'Jam: ' + form.jam.value + ' WIB',
                'Jumlah orang: ' + form.jumlah.value,
            ];
            if (form.catatan.value.trim()) lines.push('Catatan: ' + form.catatan.value.trim());

            const url = 'https://wa.me/' + WA_NUMBER + '?text=' + encodeURIComponent(lines.join('\n'));
            window.open(url, '_blank', 'noopener');
        });
    }
})();
</script>
</body>
</html> --}}