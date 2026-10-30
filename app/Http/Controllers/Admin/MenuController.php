<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    private function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:100'],
            'price'       => ['required', 'integer', 'min:0', 'max:10000000'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ];
    }

    public function index(Request $request)
    {
        $menus = Menu::with('category')
            ->when($request->category, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->q, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.menus.index', [
            'menus'      => $menus,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.menus.create', [
            'menu'       => new Menu(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['is_available'] = $request->boolean('is_available');

        unset($data['image']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        Menu::create($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        return view('admin.menus.edit', [
            'menu'       => $menu,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate($this->rules());
        $data['is_available'] = $request->boolean('is_available');

        unset($data['image']);
        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        try {
            $menu->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Menu sudah pernah dipakai di transaksi dan tidak bisa dihapus. Tandai saja sebagai "Habis".');
        }

        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus.');
    }

    public function toggleAvailability(Menu $menu)
    {
        $menu->update(['is_available' => ! $menu->is_available]);

        return back()->with('success', "{$menu->name} ditandai " . ($menu->is_available ? 'tersedia' : 'habis') . '.');
    }
}