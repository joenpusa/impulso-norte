<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;

class MediaFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = MediaFile::latest()->paginate(20);
        return view('admin.media.index', compact('files'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.media.create');
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

        return redirect()->route('admin.media.index')->with('success', 'File uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MediaFile $mediaFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MediaFile $mediaFile)
    {
        return view('admin.media.edit', compact('mediaFile'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MediaFile $mediaFile)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $mediaFile->update($request->only('title'));

        return redirect()->route('admin.media.index')->with('success', 'File updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MediaFile $mediaFile)
    {
        $mediaFile->delete(); // Model event handles file deletion
        return redirect()->route('admin.media.index')->with('success', 'File deleted successfully.');
    }
}
