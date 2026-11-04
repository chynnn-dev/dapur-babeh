<!DOCTYPE html>
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
        .info { max-width: 46rem; margin: 2.5rem auto 0; display: grid; gap: 1.4rem; text-align: center; }
        .info h3 { margin: 0 0 .2rem; font-size: 1rem; text-transform: uppercase; letter-spacing: .06em; }
        .info p { margin: 0; white-space: pre-line; }
        .btn-maps { display: inline-block; margin-top: .6rem; padding: .5rem 1.3rem; border: 2px solid var(--green); border-radius: .4rem; font-weight: 700; }
        .btn-maps:hover { background: var(--green); color: #fff; }

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
        }
    </style>
</head>
<body>

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
            <a href="#galeri" class="link">Galeri</a>

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
        <div class="slide {{ $loop->first ? 'active' : '' }}" style="background-image:url('{{ $slide->image_url }}')"></div>
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
            @if ($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="btn btn-fill">Reservasi</a>
            @endif
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

        @if ($waUrl)
            <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="btn-wa">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20z"/></svg>
                Reservasi via WhatsApp
            </a>
        @endif

        <div class="info">
            @if ($c['open_hours'])
                <div>
                    <h3>Jam Buka</h3>
                    <p>{{ $c['open_hours'] }}</p>
                </div>
            @endif

            @if ($c['address'])
                <div>
                    <h3>Alamat</h3>
                    <p>{{ $c['address'] }}</p>
                    @if ($c['maps_url'])
                        <a href="{{ $c['maps_url'] }}" target="_blank" rel="noopener" class="btn-maps">Buka di Google Maps</a>
                    @endif
                </div>
            @endif
        </div>
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

    // Slider hero
    const slides = [...document.querySelectorAll('.slide')];
    let current = 0;
    let timer = null;

    const show = (n) => {
        if (!slides.length) return;
        slides[current].classList.remove('active');
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');
    };
    const auto = () => {
        clearInterval(timer);
        if (slides.length > 1) timer = setInterval(() => show(current + 1), 5000);
    };

    document.getElementById('prev')?.addEventListener('click', () => { show(current - 1); auto(); });
    document.getElementById('next')?.addEventListener('click', () => { show(current + 1); auto(); });
    auto();

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
})();
</script>
</body>
</html>