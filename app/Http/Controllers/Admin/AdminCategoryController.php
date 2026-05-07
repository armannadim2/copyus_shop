<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        $allowed = ['name_ca', 'sort_order', 'products_count', 'is_active', 'created_at'];
        $sort    = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'products_count';
        $dir     = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $query = Category::with('parent')->withCount('products');

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

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($sort === 'name_ca') {
            $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ca')) $dir");
        } else {
            $query->orderBy($sort, $dir);
        }

        $categories = $query->paginate(20)->withQueryString();
        $parents    = Category::parents()->get();

        return view('admin.categories.index', compact('categories', 'parents', 'sort', 'dir'));
    }

    public function create()
    {
        $parents = Category::parents()->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.ca' => 'required|string|max:255',
            'name.es' => 'nullable|string|max:255',
            'name.en' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.ca' => 'nullable|string',
            'description.es' => 'nullable|string',
            'description.en' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Categoria creada correctament.');
    }

    public function edit(Category $category)
    {
        $parents = Category::where('id', '!=', $category->id)->parents()->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.ca' => 'required|string|max:255',
            'name.es' => 'nullable|string|max:255',
            'name.en' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.ca' => 'nullable|string',
            'description.es' => 'nullable|string',
            'description.en' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Categoria actualitzada correctament.');
    }

    public function toggle(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        return back()->with('success', 'Estat de la categoria actualitzat.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0 || $category->children()->count() > 0) {
            return back()->with('error', 'No es pot eliminar una categoria que té productes o subcategories associades.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Categoria eliminada correctament.');
    }
}
