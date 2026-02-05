<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageElement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::paginate(10);
        return Inertia::render('Admin/Pages/Index', compact('pages'));
    }

    public function create()
    {
        return Inertia::render('Admin/Pages/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'is_published' => 'boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
        ]);

        // Auto-generate slug from title if not present, just for basic fallback
        $validated['slug'] = Str::slug($validated['title']);
        $validated['content'] = null; // Empty content for legacy column

        $page = Page::create($validated);

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Página creada. Ahora agregue secciones.');
    }

    public function edit(Page $page)
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page->load('elements'),
        ]);
    }

    public function update(Request $request, Page $page)
    {
        // Check if we are updating basic info or elements
        if ($request->has('elements')) {
            // Validate structure of elements
            $data = $request->validate([
                'elements' => 'array',
                'elements.*.type' => 'required|string',
                'elements.*.content' => 'nullable', // json or string
                'elements.*.order' => 'integer',
                'elements.*.id' => 'nullable|integer',
                'elements.*.settings' => 'nullable|array',
            ]);

            // Sync elements
            // Strategy: 
            // 1. Get IDs from request
            // 2. Delete elements not in request
            // 3. Update or Create remaining

            $incomingIds = collect($data['elements'] ?? [])->pluck('id')->filter()->toArray();

            // Delete missing
            $page->elements()->whereNotIn('id', $incomingIds)->delete();

            foreach ($request->elements as $elementData) {
                if (isset($elementData['id'])) {
                    $element = $page->elements()->find($elementData['id']);
                    if ($element) {
                        $element->update([
                            'type' => $elementData['type'],
                            'content' => $elementData['content'],
                            'order' => $elementData['order'],
                            'settings' => $elementData['settings'] ?? null,
                        ]);
                    }
                } else {
                    $page->elements()->create([
                        'type' => $elementData['type'],
                        'content' => $elementData['content'],
                        'order' => $elementData['order'],
                        'settings' => $elementData['settings'] ?? null,
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Elementos guardados correctamente.');

        } else {
            // Basic Info Update
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'is_published' => 'boolean',
                'seo_title' => 'nullable|string|max:255',
                'seo_description' => 'nullable|string|max:255',
            ]);

            $page->update($validated);

            return redirect()->back()->with('success', 'Información actualizada.');
        }
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Página eliminada.');
    }
}
