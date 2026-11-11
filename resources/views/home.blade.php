<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $c['site_name'] }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($c['hero_subtitle'], 150) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <script>document.documentElement.classList.add('js');</script>
    <style>
        :root {
            --green: #0f5f46; --green-d: #0b4a37; --green-l: #14785a;
            --orange: #f58220; --clay: #d17b3f;
            --cream: #faf6ef; --beige: #f1e6d6; --ink: #1f2937; --muted: #6b7280;
            --serif: 'Playfair Display', Georgia, serif;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-padding-top: 4.5rem; }
        body { margin: 0; font-family: 'Lato', system-ui, sans-serif; color: var(--ink); background: #fff; line-height: 1.65; }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; }
        .container { max-width: 70rem; margin: 0 auto; padding: 0 1.25rem; }

        /* Animasi muncul */
        .js .reveal { opacity: 0; transform: translateY(20px); transition: opacity .7s ease, transform .7s ease; }
        .js .reveal.in { opacity: 1; transform: none; }

        /* ===== Header ===== */
        .header { position: fixed; top: 0; left: 0; right: 0; z-index: 50; transition: background .3s, box-shadow .3s; }
        .header.scrolled, .header.open { background: var(--green); box-shadow: 0 2px 14px rgba(0,0,0,.25); }
        .bar { display: flex; align-items: center; justify-content: space-between; height: 4.5rem; }
        .logo { font-family: var(--serif); font-size: 1.6rem; font-weight: 800; color: var(--orange); text-shadow: 0 1px 6px rgba(0,0,0,.3); }
        .nav { display: flex; align-items: center; gap: 1.4rem; }
        .nav a.link { color: #fff; font-weight: 700; font-size: .92rem; padding: .35rem 0; border-bottom: 2px solid transparent; text-shadow: 0 1px 4px rgba(0,0,0,.35); }
        .header.scrolled .nav a.link, .header.open .nav a.link { color: #e6f3ed; text-shadow: none; }
        .nav a.link:hover { color: var(--orange); }
        .nav a.link.active { border-bottom-color: var(--orange); color: var(--orange); }
        .btn-login { border: 2px solid #fff; color: #fff; padding: .4rem 1.1rem; border-radius: 99px; font-weight: 700; font-size: .88rem; }
        .btn-login:hover { background: #fff; color: var(--green); }
        .header.scrolled .btn-login, .header.open .btn-login { border-color: var(--orange); color: var(--orange); }
        .header.scrolled .btn-login:hover, .header.open .btn-login:hover { background: var(--orange); color: #fff; }
        .burger { display: none; background: none; border: 2px solid #fff; color: #fff; padding: .3rem .8rem; border-radius: 99px; font-weight: 700; cursor: pointer; }

        /* ===== Hero ===== */
        .hero { position: relative; min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; color: #fff;
                background: linear-gradient(135deg, var(--green-d), var(--green)); overflow: hidden; }
        .slide { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: 0; transition: opacity 1s; }
        .slide.active { opacity: 1; }
        .slide video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .hero::after { content: ""; position: absolute; inset: 0; background: linear-gradient(rgba(6,36,27,.45), rgba(6,36,27,.7)); }
        .hero-inner { position: relative; z-index: 2; max-width: 52rem; padding: 6rem 1.25rem 5rem; }
        .eyebrow { display: inline-block; font-size: .78rem; font-weight: 900; letter-spacing: .22em; text-transform: uppercase; color: var(--orange); }
                .hero-title { margin: 0; font-family: var(--serif); font-size: clamp(2.2rem, 6vw, 4.2rem); line-height: 1.12; font-weight: 800; text-shadow: 0 3px 18px rgba(0,0,0,.45); }
        .hero-sub { margin: 1rem auto 2rem; max-width: 36rem; font-size: clamp(1rem, 2vw, 1.25rem); font-weight: 300; color: #eaf5ef; }
        .hero-actions { display: flex; gap: .9rem; justify-content: center; flex-wrap: wrap; }
        .btn { display: inline-block; padding: .85rem 1.9rem; font-weight: 900; font-size: .9rem; text-transform: uppercase; letter-spacing: .06em; border-radius: .6rem; transition: background .2s, color .2s, transform .15s; }
        .btn:hover { transform: translateY(-2px); }
        .btn-outline { border: 2px solid #fff; color: #fff; background: rgba(0,0,0,.2); }
        .btn-outline:hover { background: #fff; color: var(--green); }
        .btn-fill { background: var(--orange); color: #fff; }
        .btn-fill:hover { background: #d96d10; }

        .arrow { position: absolute; top: 50%; transform: translateY(-50%); z-index: 3; width: 3rem; height: 3rem; border-radius: 50%; border: 2px solid rgba(255,255,255,.7); background: rgba(0,0,0,.2); color: #fff; font-size: 1.3rem; cursor: pointer; }
        .arrow:hover { background: rgba(255,255,255,.25); }
        .arrow.prev { left: 1.5rem; }
        .arrow.next { right: 1.5rem; }
        .dots { position: absolute; bottom: 2.2rem; left: 50%; transform: translateX(-50%); z-index: 3; display: flex; gap: .5rem; }
        .dots button { width: .65rem; height: .65rem; padding: 0; border-radius: 50%; border: 0; background: rgba(255,255,255,.45); cursor: pointer; transition: width .25s, background .25s; }
        .dots button.on { width: 1.8rem; border-radius: 99px; background: var(--orange); }


        /* ===== Section umum ===== */
        .section { padding: 5.5rem 0; }
        .sec-head { text-align: center; margin-bottom: 3rem; }
        .sec-head h2 { margin: .4rem 0 0; font-family: var(--serif); font-size: clamp(1.9rem, 4vw, 2.8rem); font-weight: 700; line-height: 1.2; }
        .sec-head h2 b { color: var(--orange); font-weight: 800; }
        .rule { width: 3.5rem; height: 4px; border-radius: 4px; background: var(--orange); margin: 1rem auto 0; }
        .sec-head.left { text-align: left; margin-bottom: 1.5rem; }
        .sec-head.left .rule { margin-left: 0; }
        .empty { text-align: center; opacity: .7; }

        #about { position: relative; overflow: hidden; color: var(--green-d); background-color: var(--cream);
                 background-image: radial-gradient(rgba(15,95,70,.12) 1.3px, transparent 1.4px); background-size: 22px 22px; }
        #about::before, #about::after { content: ""; position: absolute; border-radius: 50%; pointer-events: none; }
        #about::before { width: 24rem; height: 24rem; top: -9rem; right: -8rem; background: radial-gradient(circle, rgba(245,130,32,.18), rgba(245,130,32,0) 70%); }
        #about::after { width: 22rem; height: 22rem; bottom: -9rem; left: -8rem; background: radial-gradient(circle, rgba(15,95,70,.14), rgba(15,95,70,0) 70%); }
        #about .container { position: relative; z-index: 1; }
        #menu { background: #fff; color: var(--green); }
        #kontak { background: linear-gradient(135deg, var(--green-d), var(--green)); color: #fff; }
        #lokasi { background: var(--cream); color: var(--green); }
        #galeri { background: #fff; color: var(--green); }
        #ulasan { background: var(--beige); color: var(--green); }

        .card { background: #fff; color: var(--ink); border-radius: 1rem; padding: 1.7rem; box-shadow: 0 10px 30px rgba(15,95,70,.12); }
        .card h3 { margin: 0 0 1rem; font-size: 1rem; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; color: var(--green); }

        /* ===== Tentang ===== */
        .about-grid { display: grid; grid-template-columns: 1.1fr .9fr; gap: 3rem; align-items: center; }
        .about-text { white-space: pre-line; font-size: 1.05rem; color: #2f3e3a; }
        .about-facts { display: flex; flex-wrap: wrap; gap: .7rem; margin-top: 1.6rem; }
        .fact { display: inline-flex; align-items: center; gap: .5rem; padding: .5rem 1rem; border-radius: 99px; background: #fff; font-size: .88rem; font-weight: 700; box-shadow: 0 2px 8px rgba(15,95,70,.1); }
        .fact svg { color: var(--orange); }
        .about-img { position: relative; }
        .about-img img { width: 100%; aspect-ratio: 4 / 5; object-fit: cover; border-radius: 1.2rem; box-shadow: 0 18px 40px rgba(15,95,70,.25); display: block; }
        .about-img::before { content: ""; position: absolute; inset: 1.2rem -1.2rem -1.2rem 1.2rem; border: 3px solid var(--orange); border-radius: 1.2rem; z-index: -1; }
        .about-solo { max-width: 46rem; margin: 0 auto; text-align: center; }

        /* ===== Menu & Galeri ===== */
        /* Menu */
        .chips { display: flex; flex-wrap: wrap; gap: .5rem; justify-content: center; margin-bottom: 2rem; }
        .chip { padding: .4rem 1.2rem; border: 2px solid var(--green); border-radius: 99px; background: transparent; color: var(--green); font: inherit; font-weight: 900; font-size: .85rem; cursor: pointer; transition: background .2s, color .2s; }
        .chip:hover, .chip.active { background: var(--green); color: #fff; }
        .dishes { display: grid; gap: 1.1rem; grid-template-columns: repeat(auto-fill, minmax(14.5rem, 1fr)); }
        .dish { position: relative; isolation: isolate; aspect-ratio: 3 / 4; border-radius: 1.1rem; overflow: hidden;
                background: linear-gradient(160deg, var(--green-l), var(--green-d)); box-shadow: 0 8px 24px rgba(15,95,70,.18); transition: transform .35s, box-shadow .35s; }
        .dish[hidden] { display: none; }
        .dish[data-zoom] { cursor: zoom-in; }
        .dish:hover { transform: translateY(-6px); box-shadow: 0 18px 40px rgba(15,95,70,.32); }
        .dish img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .6s; }
        .dish:hover img { transform: scale(1.08); }
        .dish::after { content: ""; position: absolute; inset: 0; z-index: 1; pointer-events: none;
                       background: linear-gradient(to top, rgba(6,36,27,.92) 0%, rgba(6,36,27,.55) 38%, rgba(6,36,27,0) 66%); }
        .dish-initial { position: absolute; inset: 0; display: grid; place-items: center; padding-bottom: 3rem; font-family: var(--serif); font-size: 6rem; font-weight: 800; color: rgba(245,130,32,.9); }
        .dish-info { position: absolute; left: 0; right: 0; bottom: 0; z-index: 2; display: flex; flex-direction: column; gap: .25rem; padding: 1rem 1.1rem 1.1rem; color: #fff; }
        .dish-cat { font-size: .66rem; font-weight: 900; letter-spacing: .14em; text-transform: uppercase; color: var(--orange); }
        .dish-info h3 { margin: 0; font-size: 1.15rem; font-weight: 900; line-height: 1.25; text-shadow: 0 1px 6px rgba(0,0,0,.5); }
        .dish-price { align-self: flex-start; margin-top: .35rem; padding: .2rem .85rem; border-radius: 99px; background: var(--orange); color: #fff; font-weight: 900; font-size: .9rem; }
        .dish.sold img { filter: grayscale(1); opacity: .6; }
        .dish.sold .dish-price { background: #6b7280; }
        .badge-sold { position: absolute; top: .8rem; left: .8rem; z-index: 3; padding: .2rem .8rem; border-radius: 99px; background: #b91c1c; color: #fff; font-style: normal; font-weight: 900; font-size: .72rem; letter-spacing: .06em; text-transform: uppercase; }
        .menu-cta { display: flex; flex-wrap: wrap; gap: .8rem; justify-content: center; align-items: center; margin-top: 2.2rem; }
        .menu-cta[hidden] { display: none; }
        @media (max-width: 600px) {
            .dishes { grid-template-columns: repeat(2, 1fr); gap: .8rem; }
            .dish-info { padding: .7rem .8rem .8rem; }
            .dish-info h3 { font-size: .95rem; }
            .dish-price { font-size: .8rem; padding: .15rem .65rem; }
            .dish-initial { font-size: 4rem; }
        }

        /* Jendela buku menu */
        .bk-modal { position: fixed; inset: 0; z-index: 90; display: none; padding: 1.2rem; background: rgba(6,36,27,.8); overflow-y: auto; }
        .bk-modal.show { display: flex; }
        .bk-modal .book-wrap { margin: auto; width: 100%; }
        .bk-top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: .8rem; color: #fff; }
        .bk-top h3 { margin: 0; font-family: var(--serif); font-size: 1.4rem; }
        .bk-close { width: 2.4rem; height: 2.4rem; border: 0; border-radius: 50%; background: rgba(255,255,255,.18); color: #fff; font-size: 1.6rem; line-height: 1; cursor: pointer; }
        .bk-close:hover { background: var(--orange); }
        .bk-modal .btab { background: rgba(255,255,255,.16); }
        .bk-modal .btab:hover { background: rgba(255,255,255,.3); }
        .bk-modal .btab.active { background: #fbf6ea; }

        /* ===== Menu bergaya buku ===== */
        .book-wrap { max-width: 62rem; margin: 0 auto; }
        .book-tabs { position: relative; z-index: 1; display: flex; flex-wrap: wrap; gap: .3rem; padding: 0 1.4rem; }
        .btab { padding: .55rem 1.2rem .6rem; border: 0; border-radius: .7rem .7rem 0 0; background: var(--green); color: #d6ebe2; font: inherit; font-weight: 900; font-size: .85rem; cursor: pointer; transition: background .2s, color .2s; }
        .btab:hover { background: var(--green-l); color: #fff; }
        .btab.active { background: #fbf6ea; color: var(--green); padding-bottom: .8rem; margin-bottom: -.25rem; box-shadow: 0 -4px 10px rgba(15,95,70,.1); }

        .book { position: relative; padding: 2.4rem 2.6rem 1.6rem; background-color: #fbf6ea; border-radius: .6rem 1rem 1rem 1rem;
                background-image: linear-gradient(90deg, rgba(15,95,70,.05), transparent 6%, transparent 94%, rgba(15,95,70,.05));
                box-shadow: 0 24px 50px rgba(15,95,70,.22), inset 0 0 0 1px rgba(15,95,70,.12); }
        .book::before { content: ""; position: absolute; inset: .8rem; border: 2px solid rgba(245,130,32,.5); border-radius: .5rem; pointer-events: none; }
        .book-title { display: flex; align-items: center; justify-content: center; gap: .9rem; font-family: var(--serif); font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 800; color: var(--green); text-align: center; }
        .book-title i { font-style: normal; font-size: .9rem; color: var(--orange); }
        .book-sub { text-align: center; font-size: .72rem; font-weight: 900; letter-spacing: .3em; text-transform: uppercase; color: var(--muted); margin: .2rem 0 1.6rem; }

        .book-pages { column-count: 2; column-gap: 4.5rem; column-rule: 2px dashed rgba(15,95,70,.2); }
        .mrow { break-inside: avoid; display: flex; align-items: center; gap: .9rem; padding: .7rem 0; border-bottom: 1px solid rgba(15,95,70,.08); }
        .mrow[hidden] { display: none; }
        .mthumb { width: 3.2rem; height: 3.2rem; flex-shrink: 0; border-radius: 50%; overflow: hidden; display: grid; place-items: center; background: var(--beige); border: 2px solid rgba(245,130,32,.45);
                  font-family: var(--serif); font-weight: 800; font-size: 1.3rem; color: var(--orange); box-shadow: 0 3px 8px rgba(15,95,70,.15); }
        .mthumb[data-zoom] { cursor: zoom-in; }
        .mthumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .mbody { flex: 1; min-width: 0; }
        .mline { display: flex; align-items: flex-end; gap: .5rem; }
        .mname { font-family: var(--serif); font-weight: 700; font-size: 1.08rem; line-height: 1.3; color: var(--ink); }
        .mdots { flex: 1; min-width: .8rem; margin-bottom: .4rem; border-bottom: 2px dotted rgba(15,95,70,.35); }
        .mprice { font-family: var(--serif); font-weight: 800; color: var(--orange); white-space: nowrap; }
        .mcat { display: block; font-size: .66rem; font-weight: 900; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }
        .book.by-cat .mcat { display: none; }
        .mrow.sold .mname { text-decoration: line-through; opacity: .5; }
        .mrow.sold .mprice { font-family: 'Lato', sans-serif; font-size: .72rem; letter-spacing: .08em; text-transform: uppercase; color: #b91c1c; }
        .mrow.sold .mthumb img { filter: grayscale(1); opacity: .6; }
        .book-empty { text-align: center; color: var(--muted); padding: 1.5rem 0; }

        .book-pager { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-top: 1.4rem; font-family: var(--serif); font-weight: 700; color: var(--green); }
        .book-pager[hidden] { display: none; }
        .bk-btn { width: 2.3rem; height: 2.3rem; border-radius: 50%; border: 2px solid var(--green); background: transparent; color: var(--green); font-size: 1rem; cursor: pointer; transition: background .2s, color .2s; }
        .bk-btn:hover:not(:disabled) { background: var(--green); color: #fff; }
        .bk-btn:disabled { opacity: .3; cursor: default; }
        @media (max-width: 760px) {
            .book { padding: 1.8rem 1.2rem 1.2rem; }
            .book::before { inset: .5rem; }
            .book-pages { column-count: 1; }
            .btab { padding: .45rem .9rem .5rem; font-size: .78rem; }
        }

        .masonry { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fill, minmax(15rem, 1fr)); }
        .masonry figure { margin: 0; position: relative; overflow: hidden; border-radius: .9rem; cursor: zoom-in; background: #e6dfd0; aspect-ratio: 4 / 3; box-shadow: 0 4px 14px rgba(15,95,70,.12); }
        .masonry img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .5s; }
        .masonry figure:hover img { transform: scale(1.05); }
        .masonry figcaption { position: absolute; left: 0; right: 0; bottom: 0; padding: 2rem 1rem .8rem; color: #fff; font-weight: 700; font-size: .92rem; background: linear-gradient(transparent, rgba(0,0,0,.75)); opacity: 0; transition: opacity .3s; }
        .masonry figure:hover figcaption { opacity: 1; }
        @media (hover: none) { .masonry figcaption { opacity: 1; } }

        /* ===== Form & Kontak ===== */
        .two-col { display: grid; grid-template-columns: 1.15fr .85fr; gap: 1.6rem; align-items: start; }
        .rsv-form { display: grid; gap: .9rem; grid-template-columns: 1fr 1fr; }
        .rsv-form .full { grid-column: 1 / -1; }
        .rsv-form label { display: grid; gap: .25rem; font-size: .8rem; font-weight: 900; color: var(--green); text-transform: uppercase; letter-spacing: .04em; }
        .rsv-form input, .rsv-form textarea { font: inherit; font-weight: 400; text-transform: none; letter-spacing: 0; padding: .65rem .8rem; border: 1px solid #d9cfbd; border-radius: .6rem; background: #fbf9f4; color: var(--ink); width: 100%; }
        .rsv-form input:focus, .rsv-form textarea:focus { outline: 2px solid var(--orange); border-color: var(--orange); background: #fff; }
        .rsv-form button { cursor: pointer; border: 0; font: inherit; font-weight: 900; }
        .btn-wa { display: inline-flex; align-items: center; justify-content: center; gap: .6rem; padding: .85rem 1.6rem; background: #25a35a; color: #fff; border-radius: .6rem; font-weight: 900; font-size: .9rem; text-transform: uppercase; letter-spacing: .04em; transition: background .2s, transform .15s; }
        .btn-wa:hover { background: #1c8a4a; transform: translateY(-2px); }
        .btn-maps { display: inline-block; padding: .6rem 1.3rem; border: 2px solid var(--green); border-radius: .6rem; font-weight: 900; font-size: .85rem; color: var(--green); }
        .btn-maps:hover { background: var(--green); color: #fff; }
        .note { font-size: .82rem; color: var(--muted); margin: .9rem 0 0; }

        .info-row { display: flex; gap: .9rem; margin-bottom: 1.1rem; }
        .info-ico { width: 2.4rem; height: 2.4rem; border-radius: .7rem; display: grid; place-items: center; flex-shrink: 0; background: #e9f3ee; color: var(--green); }
        .info-row b { display: block; font-size: .78rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }
        .info-row span { white-space: pre-line; }
        .btn-row { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: 1.3rem; }

        /* ===== Lokasi ===== */
        .loc-grid { display: grid; grid-template-columns: .8fr 1.2fr; gap: 1.6rem; align-items: stretch; }
        .map-wrap { overflow: hidden; border-radius: 1rem; box-shadow: 0 10px 30px rgba(15,95,70,.18); background: #ddd; min-height: 24rem; }
        .map-wrap iframe { display: block; width: 100%; height: 100%; min-height: 24rem; border: 0; }

        /* ===== Ulasan ===== */
        .rating-sum { display: flex; align-items: center; justify-content: center; gap: 1rem; margin: -1.2rem 0 2.2rem; }
        .rating-num { font-family: var(--serif); font-size: 3rem; font-weight: 800; line-height: 1; color: var(--green); }
        .rating-meta { text-align: left; font-weight: 700; }
        .rating-meta .review-stars { font-size: 1.2rem; }
        .stars { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: .2rem; }
        .rsv-form .stars input { position: absolute; opacity: 0; width: 0; height: 0; padding: 0; border: 0; }
        .rsv-form .stars label { display: block; font-size: 2rem; line-height: 1; color: #d9cfbd; cursor: pointer; font-weight: 400; }
        .stars input:checked ~ label, .stars label:hover, .stars label:hover ~ label { color: var(--orange); }
        .hp { position: absolute; left: -9999px; height: 0; overflow: hidden; }
        .review-list { display: grid; gap: 1rem; align-content: start; }
        .review { background: #fff; border-radius: 1rem; padding: 1.2rem 1.4rem; box-shadow: 0 6px 18px rgba(15,95,70,.1); position: relative; }
        .review::before { content: "\201C"; position: absolute; right: 1.1rem; top: -.1rem; font-family: var(--serif); font-size: 4rem; color: rgba(245,130,32,.25); line-height: 1; }
        .review-head { display: flex; align-items: center; gap: .8rem; }
        .avatar { width: 2.6rem; height: 2.6rem; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0; background: var(--green); color: #fff; font-weight: 900; }
        .review-name { font-weight: 900; color: var(--ink); line-height: 1.2; }
        .review-stars { color: var(--orange); letter-spacing: .1em; }
        .review p { margin: .7rem 0 .3rem; white-space: pre-line; color: #374151; }
        .review small { color: var(--muted); }
        .alert-ok { background: #e3f4ea; color: #0b4a37; padding: .7rem 1rem; border-radius: .6rem; margin-bottom: 1rem; font-weight: 700; }
        .alert-err { background: #fdeaea; color: #a12626; padding: .7rem 1rem; border-radius: .6rem; margin-bottom: 1rem; font-size: .9rem; }

        /* ===== Footer ===== */
        .footer { background: var(--green-d); color: #cfe3da; padding: 3.5rem 0 0; }
        .foot-grid { display: grid; grid-template-columns: 1.3fr 1fr 1fr; gap: 2.5rem; padding-bottom: 2.5rem; }
        .foot-brand { font-family: var(--serif); font-size: 1.7rem; font-weight: 800; color: var(--orange); }
        .footer h4 { margin: 0 0 .9rem; font-size: .8rem; font-weight: 900; letter-spacing: .12em; text-transform: uppercase; color: #fff; }
        .footer p { margin: .3rem 0; white-space: pre-line; font-size: .92rem; }
        .footer ul { list-style: none; margin: 0; padding: 0; }
        .footer li { margin: .4rem 0; font-size: .92rem; }
        .footer a:hover { color: var(--orange); }
        .copy { border-top: 1px solid rgba(255,255,255,.15); text-align: center; padding: 1.1rem 1rem; font-size: .85rem; }

        /* Tombol WhatsApp melayang */
        .wa-float { position: fixed; right: 1.2rem; bottom: 1.2rem; z-index: 40; width: 3.4rem; height: 3.4rem; border-radius: 50%; display: grid; place-items: center; background: #25a35a; color: #fff; box-shadow: 0 8px 22px rgba(0,0,0,.3); transition: transform .2s; }
        .wa-float:hover { transform: scale(1.08); }

        .more-wrap { text-align: center; margin-top: 2.2rem; }
        /* Form ulasan memanjang sama dengan daftar ulasan */
        #ulasan .two-col { align-items: stretch; }
        #ulasan .two-col > .card { display: flex; flex-direction: column; }
        #ulasan .two-col > .card form { flex: 1; grid-template-rows: auto auto 1fr auto; }
        .rsv-form .grow { grid-template-rows: auto 1fr; }
        .rsv-form .grow textarea { height: 100%; min-height: 6rem; resize: none; }

        .btn-more { padding: .8rem 2rem; border: 2px solid var(--green); background: transparent; color: var(--green); border-radius: .6rem; font: inherit; font-weight: 900; font-size: .9rem; cursor: pointer; transition: background .2s, color .2s; }
        .btn-more:hover { background: var(--green); color: #fff; }

        /* Jendela semua ulasan */
        .rv-modal { position: fixed; inset: 0; z-index: 90; display: none; align-items: center; justify-content: center; padding: 1rem; background: rgba(0,0,0,.6); }
        .rv-modal.show { display: flex; }
        .rv-box { width: 100%; max-width: 38rem; max-height: 86vh; display: flex; flex-direction: column; background: var(--cream); border-radius: 1.1rem; box-shadow: 0 24px 60px rgba(0,0,0,.4); overflow: hidden; }
        .rv-box-head { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem 1.3rem; background: #fff; border-bottom: 3px solid var(--orange); }
        .rv-box-head h3 { margin: 0; font-family: var(--serif); font-size: 1.3rem; color: var(--green); }
        .rv-box-head small { color: var(--muted); font-weight: 700; }
        .rv-close { width: 2.3rem; height: 2.3rem; border: 0; border-radius: 50%; background: #f0ebe0; color: var(--green); font-size: 1.5rem; line-height: 1; cursor: pointer; }
        .rv-close:hover { background: var(--orange); color: #fff; }
        .rv-box-body { overflow-y: auto; padding: 1.1rem 1.3rem 1.4rem; display: grid; gap: 1rem; align-content: start; }

        /* Pembesar gambar */
        .lightbox { position: fixed; inset: 0; z-index: 100; display: none; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(0,0,0,.9); cursor: zoom-out; }
        .lightbox.show { display: flex; }
        .lightbox img { max-width: 100%; max-height: 100%; object-fit: contain; border-radius: .5rem; }

        /* ===== Responsif ===== */
        @media (max-width: 900px) {
            .about-grid, .two-col, .loc-grid { grid-template-columns: 1fr; }
            .about-grid { gap: 2.5rem; }
            .about-img { max-width: 24rem; margin: 0 auto; }
            .foot-grid { grid-template-columns: 1fr; gap: 1.8rem; }
            .map-wrap, .map-wrap iframe { min-height: 20rem; }
        }
        @media (max-width: 860px) {
            .burger { display: block; }
            .nav { display: none; position: absolute; top: 4.5rem; left: 0; right: 0; flex-direction: column; align-items: stretch;
                   gap: .3rem; padding: 1rem 1.25rem 1.25rem; background: var(--green); }
            .header.open .nav { display: flex; }
            .nav a.link { color: #e6f3ed; padding: .5rem 0; }
            .btn-login { text-align: center; border-color: var(--orange); color: var(--orange); }
            .arrow { width: 2.4rem; height: 2.4rem; }
            .arrow.prev { left: .6rem; }
            .arrow.next { right: .6rem; }
            .section { padding: 4rem 0; }
        }
        @media (max-width: 600px) {
            .rsv-form { grid-template-columns: 1fr; }
            }
    </style>
</head>
<body>

@php
    $waNumber    = \App\Models\SiteContent::whatsappNumber($c);
    $mapEmbed    = \App\Models\SiteContent::mapEmbedUrl($c);
    $reviews     = \App\Models\Review::approved()->latest()->take(50)->get();
    $previewReviews = $reviews->take(3);
    $reviewCount = \App\Models\Review::approved()->count();
    $reviewAvg   = $reviewCount ? round(\App\Models\Review::approved()->avg('rating'), 1) : 0;
    $aboutImg    = optional($galleries->first())->image_url ?? optional($slides->first())->image_url;
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
            <a href="#kontak" class="link">Reservasi</a>
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
    {{-- Latar dan sampul hanya dipasang kalau gambarnya ada (Slide Hero boleh hanya video) --}}
    @foreach ($slides as $slide)
        <div class="slide {{ $loop->first ? 'active' : '' }}"
             @if ($slide->image_url) style="background-image:url('{{ $slide->image_url }}')" @endif>
            @if ($slide->video_url)
                <video muted playsinline preload="metadata" @if ($slide->image_url) poster="{{ $slide->image_url }}" @endif>
                    <source src="{{ $slide->video_url }}" type="{{ str_ends_with($slide->video, '.webm') ? 'video/webm' : 'video/mp4' }}">
                </video>
            @endif
        </div>
    @endforeach

    @if ($slides->count() > 1)
        <button type="button" id="prev" class="arrow prev" aria-label="Sebelumnya">&#10094;</button>
        <button type="button" id="next" class="arrow next" aria-label="Berikutnya">&#10095;</button>
        <div id="dots" class="dots"></div>
    @endif

    <div class="hero-inner">
        <h1 class="hero-title">{{ $c['hero_title'] }}</h1>
        @if ($c['hero_subtitle'])
            <p class="hero-sub">{{ $c['hero_subtitle'] }}</p>
        @endif

        <div class="hero-actions">
            <a href="#menu" class="btn btn-outline">Lihat Menu</a>
            <a href="#kontak" class="btn btn-fill">Reservasi</a>
        </div>
    </div>
</section>

{{-- Tentang --}}
<section id="about" class="section">
    <div class="container">
        @if ($aboutImg)
            <div class="about-grid">
                <div class="reveal">
                    <div class="sec-head left">
                        <span class="eyebrow">Tentang Kami</span>
                        <h2>{{ $c['about_title'] }}</h2>
                        <div class="rule"></div>
                    </div>
                    <div class="about-text">{{ $c['about_text'] }}</div>
                    @if ($c['open_hours'] || $c['address'])
                        <div class="about-facts">
                            @if ($c['open_hours'])
                                <span class="fact">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                    {{ $c['open_hours'] }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="about-img reveal">
                    <img src="{{ $aboutImg }}" alt="{{ $c['site_name'] }}" loading="lazy">
                </div>
            </div>
        @else
            <div class="about-solo reveal">
                <div class="sec-head">
                    <span class="eyebrow">Tentang Kami</span>
                    <h2>{{ $c['about_title'] }}</h2>
                    <div class="rule"></div>
                </div>
                <div class="about-text">{{ $c['about_text'] }}</div>
            </div>
        @endif
    </div>
</section>

{{-- Menu --}}
{{-- Menu dibaca dari data Menu kasir (cukup diinput sekali di Admin → Menu) --}}
<section id="menu" class="section">
    <div class="container">
        <div class="sec-head reveal">
            <span class="eyebrow">Hidangan Kami</span>
            <h2>Menu <b>{{ $c['site_name'] }}</b></h2>
            <div class="rule"></div>
        </div>

        @if ($menuItems->isNotEmpty())
            <div class="chips reveal">
                <button type="button" class="chip active" data-cat="all">Semua</button>
                @foreach ($menuCategories as $cat)
                    <button type="button" class="chip" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
                @endforeach
            </div>

            <div class="dishes reveal">
                @foreach ($menuItems as $item)
                    <article class="dish {{ $item->is_available ? '' : 'sold' }}" data-cat="{{ $item->category_id }}"
                             @if ($item->image_url) data-zoom="{{ $item->image_url }}" @endif
                             @if ($loop->index >= 8) hidden @endif>
                        @if ($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" loading="lazy">
                        @else
                            <span class="dish-initial">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($item->name, 0, 1)) }}</span>
                        @endif

                        @unless ($item->is_available)
                            <em class="badge-sold">Habis</em>
                        @endunless

                        <div class="dish-info">
                            <span class="dish-cat">{{ $item->category->name }}</span>
                            <h3>{{ $item->name }}</h3>
                            <span class="dish-price">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($menuItems->count() > 4)
                <div class="menu-cta" id="menu-more">
                    <button type="button" id="all-menu-btn" class="btn-more">Lihat semua menu ({{ $menuItems->count() }})</button>
                </div>
            @endif
                @else
            <p class="empty">Menu akan segera hadir.</p>
        @endif
    </div>
</section>

{{-- Kontak & Reservasi --}}
<section id="kontak" class="section">
    <div class="container">
        <div class="sec-head reveal">
            <span class="eyebrow">Pesan Meja</span>
            <h2 style="color:#fff">Kontak <b>&amp; Reservasi</b></h2>
            <div class="rule"></div>
        </div>

        <div class="two-col">
            {{-- Form reservasi --}}
            <div class="card reveal">
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
                        <button type="submit" class="btn-wa full">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20z"/></svg>
                            Kirim Reservasi via WhatsApp
                        </button>
                    </form>
                    <p class="note">Anda akan diarahkan ke WhatsApp. Reservasi dikonfirmasi setelah kami membalas pesan Anda.</p>
                @else
                    <p class="empty">Reservasi via WhatsApp akan segera tersedia.</p>
                @endif
            </div>

            {{-- Info kontak --}}
            <div class="card reveal">
                <h3>Informasi</h3>

                @if ($c['address'])
                    <div class="info-row">
                        <div class="info-ico">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                        </div>
                        <div><b>Alamat</b><span>{{ $c['address'] }}</span></div>
                    </div>
                @endif

                @if ($c['open_hours'])
                    <div class="info-row">
                        <div class="info-ico">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        </div>
                        <div><b>Jam Buka</b><span>{{ $c['open_hours'] }}</span></div>
                    </div>
                @endif

                <div class="btn-row">
                    @if ($waUrl)
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="btn-wa">Chat WhatsApp</a>
                    @endif
                    <a href="#lokasi" class="btn-maps">Lihat Peta</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Lokasi --}}
<section id="lokasi" class="section">
    <div class="container">
        <div class="sec-head reveal">
            <span class="eyebrow">Temukan Kami</span>
            <h2>Lokasi <b>{{ $c['site_name'] }}</b></h2>
            <div class="rule"></div>
        </div>

        @if ($mapEmbed)
            <div class="loc-grid">
                <div class="card reveal">
                    <h3>Alamat</h3>
                    @if ($c['address'])
                        <div class="info-row">
                            <div class="info-ico">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                            </div>
                            <div><span>{{ $c['address'] }}</span></div>
                        </div>
                    @endif
                    @if ($c['open_hours'])
                        <div class="info-row">
                            <div class="info-ico">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            </div>
                            <div><span>{{ $c['open_hours'] }}</span></div>
                        </div>
                    @endif
                    @if ($c['maps_url'])
                        <div class="btn-row">
                            <a href="{{ $c['maps_url'] }}" target="_blank" rel="noopener" class="btn-maps">Buka di Google Maps</a>
                        </div>
                    @endif
                </div>

                <div class="map-wrap reveal">
                    <iframe src="{{ $mapEmbed }}" loading="lazy" allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Lokasi {{ $c['site_name'] }}"></iframe>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- Galeri --}}
<section id="galeri" class="section">
    <div class="container">
        <div class="sec-head reveal">
            <span class="eyebrow">Momen Kami</span>
            <h2>Galeri <b>{{ $c['site_name'] }}</b></h2>
            <div class="rule"></div>
        </div>

        @if ($galleries->isNotEmpty())
            <div class="masonry">
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
        <div class="sec-head reveal">
            <span class="eyebrow">Kata Mereka</span>
            <h2>Ulasan <b>Pelanggan</b></h2>
            <div class="rule"></div>
        </div>

        @if ($reviewCount)
            <div class="rating-sum">
                <div class="rating-num">{{ $reviewAvg }}</div>
                <div class="rating-meta">
                    <div class="review-stars">@for ($i = 1; $i <= 5; $i++){{ $i <= round($reviewAvg) ? '★' : '☆' }}@endfor</div>
                    <div>{{ $reviewCount }} ulasan</div>
                </div>
            </div>
        @endif

        <div class="two-col">
            <div class="card reveal">
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
                        <div style="font-size:.8rem;font-weight:900;margin-bottom:.25rem;color:var(--green);text-transform:uppercase;letter-spacing:.04em">Penilaian</div>
                        <div class="stars">
                            @for ($i = 5; $i >= 1; $i--)
                                <input type="radio" id="st{{ $i }}" name="rating" value="{{ $i }}" required @checked(old('rating') == $i)>
                                <label for="st{{ $i }}" title="{{ $i }} bintang">&#9733;</label>
                            @endfor
                        </div>
                    </div>

                    <label class="full grow">Ulasan
                        <textarea name="comment" rows="4" required minlength="5" maxlength="500" placeholder="Ceritakan pengalaman Anda">{{ old('comment') }}</textarea>
                    </label>

                    <button type="submit" class="btn btn-fill full">Kirim Ulasan</button>
                </form>
            </div>

            <div class="review-list">
                @forelse ($previewReviews as $r)
                    <div class="review reveal">
                        <div class="review-head">
                            <div class="avatar">{{ strtoupper(mb_substr($r->name, 0, 1)) }}</div>
                            <div>
                                <div class="review-name">{{ $r->name }}</div>
                                <div class="review-stars">@for ($i = 1; $i <= 5; $i++){{ $i <= $r->rating ? '★' : '☆' }}@endfor</div>
                            </div>
                        </div>
                        <p>{{ $r->comment }}</p>
                        <small>{{ $r->created_at->format('d/m/Y') }}</small>
                    </div>
                @empty
                    <p class="empty">Belum ada ulasan. Jadilah yang pertama!</p>
                @endforelse
            </div>
        </div>

        @if ($reviewCount > $previewReviews->count())
            <div class="more-wrap">
                <button type="button" id="all-reviews-btn" class="btn-more">Lihat semua ulasan ({{ $reviewCount }})</button>
            </div>
        @endif
    </div>
</section>

{{-- Footer --}}
<footer class="footer">
    <div class="container foot-grid">
        <div>
            <div class="foot-brand">{{ $c['site_name'] }}</div>
            @if ($c['hero_subtitle'])
                <p>{{ $c['hero_subtitle'] }}</p>
            @endif
        </div>
        <div>
            <h4>Kunjungi Kami</h4>
            @if ($c['address'])<p>{{ $c['address'] }}</p>@endif
            @if ($c['open_hours'])<p>{{ $c['open_hours'] }}</p>@endif
        </div>
        <div>
            <h4>Jelajahi</h4>
            <ul>
                <li><a href="#about">Tentang</a></li>
                <li><a href="#menu">Menu</a></li>
                <li><a href="#kontak">Reservasi</a></li>
                <li><a href="#galeri">Galeri</a></li>
                <li><a href="#ulasan">Ulasan</a></li>
            </ul>
        </div>
    </div>
    <div class="copy">&copy; {{ date('Y') }} {{ $c['site_name'] }}. Semua hak dilindungi.</div>
</footer>

@if ($waUrl)
    <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="wa-float" aria-label="Chat WhatsApp">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20z"/></svg>
    </a>
@endif

@if ($reviewCount > $previewReviews->count())
    <div id="reviews-modal" class="rv-modal" role="dialog" aria-modal="true" aria-label="Semua ulasan">
        <div class="rv-box">
            <div class="rv-box-head">
                <div>
                    <h3>Semua Ulasan</h3>
                    <small>{{ $reviewAvg }} / 5 &middot; {{ $reviewCount }} ulasan</small>
                </div>
                <button type="button" class="rv-close" data-close aria-label="Tutup">&times;</button>
            </div>
            <div class="rv-box-body">
                @foreach ($reviews as $r)
                    <div class="review">
                        <div class="review-head">
                            <div class="avatar">{{ strtoupper(mb_substr($r->name, 0, 1)) }}</div>
                            <div>
                                <div class="review-name">{{ $r->name }}</div>
                                <div class="review-stars">@for ($i = 1; $i <= 5; $i++){{ $i <= $r->rating ? '★' : '☆' }}@endfor</div>
                            </div>
                        </div>
                        <p>{{ $r->comment }}</p>
                        <small>{{ $r->created_at->format('d/m/Y') }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@if ($menuItems->count() > 4)
    <div id="menu-modal" class="bk-modal" role="dialog" aria-modal="true" aria-label="Menu selengkapnya">
        <div class="book-wrap">
            <div class="bk-top">
                <h3>Menu Selengkapnya</h3>
                <button type="button" class="bk-close" data-close aria-label="Tutup">&times;</button>
            </div>

            <div class="book-tabs">
                <button type="button" class="btab active" data-cat="all" data-title="Semua Menu">Semua</button>
                @foreach ($menuCategories as $cat)
                    <button type="button" class="btab" data-cat="{{ $cat->id }}" data-title="{{ $cat->name }}">{{ $cat->name }}</button>
                @endforeach
            </div>

            <div class="book">
                <div class="book-title"><i>&#10022;</i><span id="book-title">Semua Menu</span><i>&#10022;</i></div>
                <div class="book-sub">{{ $c['site_name'] }}</div>

                <div class="book-pages">
                    @foreach ($menuItems as $item)
                        <div class="mrow {{ $item->is_available ? '' : 'sold' }}" data-cat="{{ $item->category_id }}" @if ($loop->index >= 10) hidden @endif>
                            <div class="mthumb" @if ($item->image_url) data-zoom="{{ $item->image_url }}" @endif>
                                @if ($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" loading="lazy">
                                @else
                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($item->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="mbody">
                                <div class="mline">
                                    <span class="mname">{{ $item->name }}</span>
                                    <span class="mdots"></span>
                                    <span class="mprice">{{ $item->is_available ? 'Rp ' . number_format($item->price, 0, ',', '.') : 'Habis' }}</span>
                                </div>
                                <small class="mcat">{{ $item->category->name }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="book-empty" id="book-empty" hidden>Belum ada menu di kategori ini.</p>

                <div class="book-pager" id="book-pager" @if ($menuItems->count() <= 10) hidden @endif>
                    <button type="button" id="bk-prev" class="bk-btn" aria-label="Halaman sebelumnya">&#10094;</button>
                    <span id="bk-info">Halaman 1</span>
                    <button type="button" id="bk-next" class="bk-btn" aria-label="Halaman berikutnya">&#10095;</button>
                </div>
            </div>
        </div>
    </div>
@endif

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

    const dotsBox = document.getElementById('dots');
    if (dotsBox) {
        slides.forEach((_, i) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.setAttribute('aria-label', 'Slide ' + (i + 1));
            b.addEventListener('click', () => show(i));
            dotsBox.appendChild(b);
        });
    }
    const markDot = () => dotsBox && [...dotsBox.children].forEach((d, i) => d.classList.toggle('on', i === current));

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

        markDot();
        startCurrent();
    }

    document.getElementById('prev')?.addEventListener('click', () => show(current - 1));
    document.getElementById('next')?.addEventListener('click', () => show(current + 1));

    markDot();
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

    // Animasi muncul saat di-scroll
    const revealer = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) { e.target.classList.add('in'); revealer.unobserve(e.target); }
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach((el) => revealer.observe(el));

    // Menu: filter kategori + batasi tampilan (sisanya lewat tombol "Lihat semua menu")
    const dishes = [...document.querySelectorAll('.dish')];
    const chips = [...document.querySelectorAll('.chips .chip')];
    const menuMore = document.getElementById('menu-more');
    let menuCat = 'all';
    const menuLimit = () => (window.innerWidth < 600 ? 6 : 8);

    function applyMenu() {
        const limit = menuLimit();
        let shown = 0;
        dishes.forEach((d) => {
            const match = menuCat === 'all' || d.dataset.cat === menuCat;
            const show = match && shown < limit;
            if (show) shown++;
            d.hidden = !show;
        });
        if (menuMore) menuMore.hidden = dishes.length <= limit;
    }

    chips.forEach((chip) => chip.addEventListener('click', () => {
        menuCat = chip.dataset.cat;
        chips.forEach((c) => c.classList.toggle('active', c === chip));
        applyMenu();
    }));
    window.addEventListener('resize', applyMenu);
    if (dishes.length) applyMenu();

    // Menu bergaya buku: tab kategori + halaman
    const bookRows = [...document.querySelectorAll('.mrow')];
    if (bookRows.length) {
        const tabs = [...document.querySelectorAll('.btab')];
        const book = document.querySelector('.book');
        const titleEl = document.getElementById('book-title');
        const infoEl = document.getElementById('bk-info');
        const prevBtn = document.getElementById('bk-prev');
        const nextBtn = document.getElementById('bk-next');
        const pager = document.getElementById('book-pager');
        const emptyEl = document.getElementById('book-empty');
        let cat = 'all';
        let page = 1;
        const perPage = () => (window.innerWidth < 760 ? 6 : 10);

        function renderBook() {
            const per = perPage();
            const matched = bookRows.filter((r) => cat === 'all' || r.dataset.cat === cat);
            const pages = Math.max(1, Math.ceil(matched.length / per));
            page = Math.min(Math.max(page, 1), pages);

            bookRows.forEach((r) => { r.hidden = true; });
            matched.slice((page - 1) * per, page * per).forEach((r) => { r.hidden = false; });

            book.classList.toggle('by-cat', cat !== 'all');
            emptyEl.hidden = matched.length > 0;
            pager.hidden = pages <= 1;
            infoEl.textContent = 'Halaman ' + page + ' dari ' + pages;
            prevBtn.disabled = page <= 1;
            nextBtn.disabled = page >= pages;
        }

        tabs.forEach((tab) => tab.addEventListener('click', () => {
            cat = tab.dataset.cat;
            page = 1;
            titleEl.textContent = tab.dataset.title;
            tabs.forEach((t) => t.classList.toggle('active', t === tab));
            renderBook();
        }));
        prevBtn.addEventListener('click', () => { page--; renderBook(); });
        nextBtn.addEventListener('click', () => { page++; renderBook(); });

        let lastW = window.innerWidth;
        window.addEventListener('resize', () => {
            if (window.innerWidth !== lastW) { lastW = window.innerWidth; renderBook(); }
        });
        renderBook();
    }

    // Buku menu (jendela "Lihat semua menu")
    const bkModal = document.getElementById('menu-modal');
    if (bkModal) {
        const openBk = () => { bkModal.classList.add('show'); document.body.style.overflow = 'hidden'; };
        const closeBk = () => { bkModal.classList.remove('show'); document.body.style.overflow = ''; };
        document.getElementById('all-menu-btn')?.addEventListener('click', openBk);
        bkModal.addEventListener('click', (e) => { if (e.target === bkModal || e.target.closest('[data-close]')) closeBk(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeBk(); });
    }

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

    // Jendela semua ulasan
    const rvModal = document.getElementById('reviews-modal');
    if (rvModal) {
        const openRv = () => { rvModal.classList.add('show'); document.body.style.overflow = 'hidden'; };
        const closeRv = () => { rvModal.classList.remove('show'); document.body.style.overflow = ''; };
        document.getElementById('all-reviews-btn')?.addEventListener('click', openRv);
        rvModal.addEventListener('click', (e) => { if (e.target === rvModal || e.target.closest('[data-close]')) closeRv(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeRv(); });
    }

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
</html>