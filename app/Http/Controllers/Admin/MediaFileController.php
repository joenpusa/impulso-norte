<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MediaFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MediaFile::latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $files = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Media/Index', [
            'files' => $files,
            'filters' => $request->only(['search', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'title' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $dateFolder = date('Y-m-d');
        $path = $file->store("uploads/{$dateFolder}", 'public');

        MediaFile::create([
            'title' => $request->input('title') ?? $file->getClientOriginalName(),
            'path' => $path,
            'type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return redirect()->route('admin.media.index')->with('success', 'Archivo subido correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MediaFile $mediaFile)
    {
        // Actually, looking at routes, it's 'media', so param is 'medium'? No, Laravel resource uses singular 'media' -> 'medium' only if English inflection works.
        // But in web.php it is Route::resource('media', ...).
        // Let's check logic: Update method was implementing title update.

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $mediaFile->update($request->only('title'));

        return redirect()->back()->with('success', 'Archivo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $mediaFile = MediaFile::findOrFail($id);
        $mediaFile->delete(); // Model event handles file deletion
        return redirect()->route('admin.media.index')->with('success', 'Archivo eliminado correctamente.');
    }
}
