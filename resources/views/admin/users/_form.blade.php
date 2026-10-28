@php $isSelf = $user->exists && $user->is(auth()->user()); @endphp

<form method="POST" action="{{ $action }}" class="max-w-lg space-y-4 rounded-xl bg-white p-6 shadow">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <label for="name" class="block text-sm font-medium">Nama</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="username" class="block text-sm font-medium">Username</label>
        <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required
               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
        <p class="mt-1 text-xs text-gray-500">Huruf, angka, strip (-), dan underscore (_), tanpa spasi.</p>
        @error('username') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="role" class="block text-sm font-medium">Role</label>
        @if ($isSelf)
            <input type="hidden" name="role" value="{{ $user->role }}">
        @endif
        <select id="role" @unless ($isSelf) name="role" @endunless @disabled($isSelf)
                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 disabled:bg-gray-100">
            @foreach (\App\Models\User::ROLES as $key => $label)
                <option value="{{ $key }}" @selected(old('role', $user->role) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @if ($isSelf)
            <p class="mt-1 text-xs text-gray-500">Role akun Anda sendiri tidak bisa diubah.</p>
        @endif
        @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium">
            {{ $user->exists ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password' }}
        </label>
        <input id="password" type="password" name="password" autocomplete="new-password"
               @if (! $user->exists) required @endif
               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium">Konfirmasi Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
    </div>

    <div class="flex gap-2 pt-2">
        <button class="rounded-lg bg-orange-600 px-5 py-2 text-sm font-semibold text-white hover:bg-orange-700">Simpan</button>
        <a href="{{ route('admin.users.index') }}" class="rounded-lg border px-5 py-2 text-sm hover:bg-gray-50">Batal</a>
    </div>
</form>