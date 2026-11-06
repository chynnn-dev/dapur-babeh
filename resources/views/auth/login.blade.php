<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Dapur Babeh</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --green: #0f5f46; --green-d: #0b4a37; --green-l: #14785a; --orange: #f58220; --cream: #f4f1ea; --ink: #1f2937; }

        .lg-page {
            min-height: 100vh; margin: 0; padding: 1.5rem 1rem;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Lato', system-ui, sans-serif; color: var(--ink);
            background-color: var(--cream);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='260' height='150'%3E%3Ctext x='130' y='80' text-anchor='middle' font-family='Georgia,serif' font-weight='900' font-size='26' fill='rgb(15,95,70)' fill-opacity='0.10' transform='rotate(-20 130 75)'%3EDAPUR BABEH%3C/text%3E%3C/svg%3E");
        }

        .lg-card {
            width: 100%; max-width: 46rem; display: grid; grid-template-columns: 1fr 1fr;
            background: #fff; border-radius: 1.25rem; overflow: hidden;
            box-shadow: 0 20px 50px rgba(15,95,70,.25);
        }

        /* Panel kiri (dekoratif) */
        .lg-side {
            position: relative; overflow: hidden; color: #fff; padding: 2rem 1.75rem;
            display: flex; flex-direction: column; justify-content: center; min-height: 20rem;
            background: linear-gradient(160deg, var(--green-l), var(--green) 50%, var(--green-d));
        }
        .lg-side::before, .lg-side::after { content: ""; position: absolute; border-radius: 50%; pointer-events: none; }
        .lg-side::before { width: 9rem; height: 9rem; right: -3.5rem; top: -3.5rem; background: rgba(245,130,32,.25); }
        .lg-side::after { width: 7rem; height: 7rem; left: -3rem; bottom: -3rem; background: rgba(255,255,255,.08); }
        .lg-side > * { position: relative; z-index: 1; }

        .lg-brand { text-align: center; }
        .lg-bowl { width: 3.25rem; height: 3.25rem; margin: 0 auto .6rem; display: block; }
        .lg-name { margin: 0; font-size: 1.6rem; font-weight: 900; letter-spacing: .03em; color: var(--orange); text-shadow: 0 2px 8px rgba(0,0,0,.25); }
        .lg-tag { margin: .25rem 0 0; font-size: .9rem; color: #d6ebe2; }

        /* Panel kanan (form) */
        .lg-form { padding: 2rem 2rem 1.5rem; display: flex; flex-direction: column; justify-content: center; }
        .lg-form h2 { margin: 0; font-size: 1.5rem; font-weight: 900; color: var(--green); }
        .lg-note {
            display: flex; gap: .45rem; align-items: center; margin: .35rem 0 1.1rem;
            font-size: .8rem; color: #9a5a00; background: #fff4e5; border-radius: .4rem; padding: .4rem .6rem;
        }
        .lg-note svg { flex-shrink: 0; }

        .lg-field { margin-bottom: .8rem; }
        .lg-field label { display: block; font-size: .78rem; font-weight: 700; color: var(--green); margin-bottom: .2rem; text-transform: uppercase; letter-spacing: .05em; }
        .lg-field input[type="text"], .lg-field input[type="password"] {
            width: 100%; box-sizing: border-box; padding: .55rem .75rem; font: inherit;
            border: 1px solid #d9cfbd; border-radius: .5rem; background: #fbf9f4; color: var(--ink);
        }
        .lg-field input:focus { outline: 2px solid var(--orange); border-color: var(--orange); background: #fff; }
        .lg-err { margin: .25rem 0 0; font-size: .8rem; color: #b91c1c; }

        .lg-row { display: flex; align-items: center; justify-content: space-between; margin: .2rem 0 1rem; font-size: .82rem; }
        .lg-row label { display: flex; align-items: center; gap: .4rem; color: #4b5563; }
        .lg-row a { color: #6b7280; text-decoration: none; }
        .lg-row a:hover { color: var(--green); }

        .lg-btn {
            width: 100%; padding: .65rem; border: 0; border-radius: .5rem; cursor: pointer;
            font: inherit; font-weight: 900; letter-spacing: .05em; text-transform: uppercase;
            background: var(--green); color: #fff; transition: background .2s;
        }
        .lg-btn:hover { background: var(--orange); }

        @media (max-width: 720px) {
            .lg-card { grid-template-columns: 1fr; max-width: 26rem; }
            .lg-side { min-height: 0; padding: 1.25rem; flex-direction: row; align-items: center; gap: 1rem; }
            .lg-brand { display: flex; align-items: center; gap: .75rem; text-align: left; }
            .lg-bowl { width: 2.75rem; height: 2.75rem; margin: 0; }
            .lg-name { font-size: 1.4rem; }
            .lg-tag { font-size: .8rem; }
            .lg-form { padding: 1.5rem 1.25rem 1.25rem; }
        }
    </style>
</head>
<body class="lg-page">
    <div class="lg-card">

        {{-- Panel kiri --}}
        <aside class="lg-side">
            <div class="lg-brand">
                <svg class="lg-bowl" viewBox="0 0 120 120" aria-hidden="true">
                    <g fill="none" stroke="#f4f1ea" stroke-width="4" stroke-linecap="round" opacity=".85">
                        <path d="M42 36c-6-6 6-10 0-18"/>
                        <path d="M60 36c-6-6 6-10 0-18"/>
                        <path d="M78 36c-6-6 6-10 0-18"/>
                    </g>
                    <path d="M14 56h92a46 40 0 0 1-92 0z" fill="#f58220"/>
                    <path d="M14 56h92" stroke="#f4f1ea" stroke-width="5" stroke-linecap="round"/>
                    <path d="M30 78q30 14 60 0" fill="none" stroke="#fff" stroke-opacity=".45" stroke-width="3" stroke-linecap="round"/>
                    <rect x="42" y="100" width="36" height="6" rx="3" fill="#f4f1ea" opacity=".85"/>
                </svg>
                <div>
                    <h1 class="lg-name">Dapur Babeh</h1>
                    <p class="lg-tag">Masakan hangat, dimasak sepenuh hati</p>
                </div>
            </div>

        </aside>

        {{-- Panel kanan --}}
        <section class="lg-form">
            <h2>Masuk</h2>
            <div class="lg-note" role="note">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2 1 21h22L12 2zm1 15h-2v-2h2v2zm0-4h-2V9h2v4z"/></svg>
                <span><b>Khusus karyawan.</b> Pelanggan, silakan kembali ke beranda.</span>
            </div>

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf

                <div class="lg-field">
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}"
                           autocomplete="username" autofocus required>
                    @error('username')
                        <p class="lg-err">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg-field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password"
                           autocomplete="current-password" required>
                    @error('password')
                        <p class="lg-err">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg-row">
                    <label><input type="checkbox" name="remember"> Ingat saya</label>
                    <a href="{{ route('home') }}">&larr; Beranda</a>
                </div>

                <button type="submit" class="lg-btn">Masuk</button>
            </form>
        </section>
    </div>
</body>
</html>