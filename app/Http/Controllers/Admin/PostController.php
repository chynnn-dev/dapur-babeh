<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    private function rules(bool $imageRequired): array
    {
        return [
            'type'         => ['required', Rule::in(array_keys(Post::TYPES))],
            'title'        => ['nullable', 'string', 'max:100'],
            'caption'      => ['nullable', 'string', 'max:500'],
            'image'        => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
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
        $data = $request->validate($this->rules(true));
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order']   = $data['sort_order'] ?? 0;
        $data['image']        = $request->file('image')->store('posts', 'public');

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
        $data = $request->validate($this->rules(false));
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order']   = $data['sort_order'] ?? 0;

        unset($data['image']);
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($post->image);
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()->route('admin.posts.index', ['type' => $post->type])
            ->with('success', 'Postingan berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        Storage::disk('public')->delete($post->image);
        $post->delete();

        return back()->with('success', 'Postingan berhasil dihapus.');
    }
}