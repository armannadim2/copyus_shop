<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminHeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::ordered()->get();

        return view('admin.hero-slides.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.hero-slides.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image'      => 'required|image|max:4096',
            'eyebrow'    => 'nullable|string|max:100',
            'title'      => 'nullable|string|max:200',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $path = $request->file('image')->store('hero_slides', 'public');

        HeroSlide::create([
            'image'      => $path,
            'eyebrow'    => $data['eyebrow'] ?? null,
            'title'      => $data['title'] ?? null,
            'is_active'  => $request->boolean('is_active', true),
            'sort_order' => $data['sort_order'] ?? (HeroSlide::max('sort_order') + 1),
        ]);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Slide creat correctament.');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.hero-slides.edit', compact('heroSlide'));
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $data = $request->validate([
            'image'      => 'nullable|image|max:4096',
            'eyebrow'    => 'nullable|string|max:100',
            'title'      => 'nullable|string|max:200',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($heroSlide->image);
            $data['image'] = $request->file('image')->store('hero_slides', 'public');
        }

        $heroSlide->update([
            'image'      => $data['image'] ?? $heroSlide->image,
            'eyebrow'    => $data['eyebrow'] ?? null,
            'title'      => $data['title'] ?? null,
            'is_active'  => $request->boolean('is_active', true),
            'sort_order' => $data['sort_order'] ?? $heroSlide->sort_order,
        ]);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Slide actualitzat correctament.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        Storage::disk('public')->delete($heroSlide->image);
        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Slide eliminat.');
    }

    public function toggle(HeroSlide $heroSlide)
    {
        $heroSlide->update(['is_active' => ! $heroSlide->is_active]);

        return back()->with('success', 'Estat actualitzat.');
    }

    public function moveUp(HeroSlide $heroSlide)
    {
        $prev = HeroSlide::where('sort_order', '<', $heroSlide->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($prev) {
            [$heroSlide->sort_order, $prev->sort_order] = [$prev->sort_order, $heroSlide->sort_order];
            $heroSlide->save();
            $prev->save();
        }

        return back();
    }

    public function moveDown(HeroSlide $heroSlide)
    {
        $next = HeroSlide::where('sort_order', '>', $heroSlide->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            [$heroSlide->sort_order, $next->sort_order] = [$next->sort_order, $heroSlide->sort_order];
            $heroSlide->save();
            $next->save();
        }

        return back();
    }
}
