<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Setting;

class TableController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::orderByRaw('CAST(table_number AS UNSIGNED), table_number')->get();

return view('admin.tables.index', [
    'tables'    => $tables,
    'featureOn' => Setting::isActive(Setting::TABLE_MANAGEMENT),
]);    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'table_number' => ['required', 'string', 'max:20', 'unique:tables,table_number'],
        ]);

        RestaurantTable::create($data);

        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function edit(RestaurantTable $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $data = $request->validate([
            'table_number' => ['required', 'string', 'max:20', Rule::unique('tables', 'table_number')->ignore($table->id)],
            'status'       => ['required', Rule::in(['available', 'occupied'])],
        ]);

        $table->update($data);

        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroy(RestaurantTable $table)
    {
        if ($table->status === 'occupied') {
            return back()->with('error', 'Meja sedang terisi, tidak bisa dihapus.');
        }

        $table->delete();

        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil dihapus.');
    }
}