<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class AdminBrandController extends Controller
{
    public function index(Request $request)
    {
        $allowed = ['name_ca', 'sort_order', 'products_count', 'is_active', 'created_at'];
        $sort    = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'sort_order';
        $dir     = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $query = Brand::withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($sort === 'name_ca') {
            $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ca')) $dir");
        } else {
            $query->orderBy($sort, $dir);
        }

        $brands = $query->paginate(20)->withQueryString();

        return view('admin.brands.index', compact('brands', 'sort', 'dir'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|array',
            'name.ca'        => 'required|string|max:255',
            'name.es'        => 'nullable|string|max:255',
            'name.en'        => 'nullable|string|max:255',
            'description'    => 'nullable|array',
            'description.ca' => 'nullable|string',
            'description.es' => 'nullable|string',
            'description.en' => 'nullable|string',
            'slug'           => 'required|string|max:255|unique:brands,slug',
            'sort_order'     => 'required|integer|min:0',
            'is_active'      => 'boolean',
            'image'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('brands', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        Brand::create($validated);

        return redirect()->route('admin.brands.index')->with('success', 'Marca creada correctament.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name'           => 'required|array',
            'name.ca'        => 'required|string|max:255',
            'name.es'        => 'nullable|string|max:255',
            'name.en'        => 'nullable|string|max:255',
            'description'    => 'nullable|array',
            'description.ca' => 'nullable|string',
            'description.es' => 'nullable|string',
            'description.en' => 'nullable|string',
            'slug'           => 'required|string|max:255|unique:brands,slug,' . $brand->id,
            'sort_order'     => 'required|integer|min:0',
            'is_active'      => 'boolean',
            'image'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('brands', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $brand->update($validated);

        return redirect()->route('admin.brands.index')->with('success', 'Marca actualitzada correctament.');
    }

    public function toggle(Brand $brand)
    {
        $brand->update(['is_active' => !$brand->is_active]);
        return back()->with('success', 'Estat de la marca actualitzat.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return back()->with('error', 'No es pot eliminar una marca que té productes associats.');
        }

        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Marca eliminada correctament.');
    }
}
