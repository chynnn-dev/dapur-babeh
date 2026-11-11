<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    private function rules(Request $request, bool $creating): array
    {
        // Gambar wajib saat membuat, kecuali Slide Hero yang berisi video
        $imageRequired = $creating
            && ! ($request->input('type') === 'hero' && $request->hasFile('video'));

        return [
            'type'         => ['required', Rule::in(array_keys(Post::TYPES))],
            'title'        => ['nullable', 'string', 'max:100'],
            'caption'      => ['nullable', 'string', 'max:500'],
            'image'        => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'video'        => ['nullable', 'file', 'mimes:mp4,webm', 'max:35840'],
            'remove_video' => ['nullable', 'boolean'],
            'sort_order'   => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function index(Request $request)
    {
        $type = array_key_exists($request->type, Post::TYPES) ? $request->type : null;

        $posts = Post::query()
            ->when($type, fn ($q) => $q->ofType($type))
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('admin.posts.index', ['posts' => $posts, 'type' => $type]);
    }

    public function create(Request $request)
    {
        $type = array_key_exists($request->type, Post::TYPES) ? $request->type : 'gallery';

        return view('admin.posts.create', ['post' => new Post(['type' => $type, 'is_published' => true])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules($request, true), $this->messages());
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order']   = $data['sort_order'] ?? 0;

        unset($data['image'], $data['video'], $data['remove_video']);

        $data['image'] = $request->hasFile('image')
            ? $request->file('image')->store('posts', 'public')
            : null;

        // Video hanya berlaku untuk Slide Hero
        if ($data['type'] === 'hero' && $request->hasFile('video')) {
            $data['video'] = $request->file('video')->store('posts/videos', 'public');
        }

        $post = Post::create($data);

        return redirect()->route('admin.posts.index', ['type' => $post->type])
            ->with('success', 'Postingan berhasil ditambahkan.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate($this->rules($request, false), $this->messages());
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order']   = $data['sort_order'] ?? 0;

        // Postingan harus tetap punya isi: gambar, atau (khusus Slide Hero) video
        $hasImage = $post->image || $request->hasFile('image');
        $hasVideo = $data['type'] === 'hero'
            && ($request->hasFile('video') || ($post->video && ! $request->boolean('remove_video')));

        if (! $hasImage && ! $hasVideo) {
            throw ValidationException::withMessages([
                'image' => 'Gambar wajib diisi (Slide Hero boleh hanya berisi video).',
            ]);
        }

        unset($data['image'], $data['video'], $data['remove_video']);

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        // Hapus video lama jika: jenis bukan hero, diminta hapus, atau diganti video baru
        if ($post->video && ($data['type'] !== 'hero' || $request->boolean('remove_video') || $request->hasFile('video'))) {
            Storage::disk('public')->delete($post->video);
            $data['video'] = null;
        }

        if ($data['type'] === 'hero' && $request->hasFile('video')) {
            $data['video'] = $request->file('video')->store('posts/videos', 'public');
        }

        $post->update($data);

        return redirect()->route('admin.posts.index', ['type' => $post->type])
            ->with('success', 'Postingan berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        if ($post->video) {
            Storage::disk('public')->delete($post->video);
        }

        $post->delete();

        return back()->with('success', 'Postingan berhasil dihapus.');
    }

    private function messages(): array
    {
        return [
            'image.required' => 'Gambar wajib diisi (Slide Hero boleh hanya berisi video).',
            'image.max'      => 'Ukuran gambar maksimal 4 MB.',
            'video.mimes'    => 'Video harus berformat MP4 atau WebM.',
            'video.max'      => 'Ukuran video maksimal 35 MB.',
        ];
    }
}