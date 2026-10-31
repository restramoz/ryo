<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.taxonomy', ['items' => Tag::orderBy('name')->get(), 'type' => 'tags']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.tags.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:tags,name'], 'slug' => ['nullable', 'string', 'max:255', 'unique:tags,slug']]);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        Tag::create($data);

        return back()->with('success', 'Tag created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag): RedirectResponse
    {
        return redirect()->route('admin.tags.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag): RedirectResponse
    {
        return redirect()->route('admin.tags.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'slug' => ['nullable', 'string', 'max:255']]);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $tag->update($data);

        return back()->with('success', 'Tag updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return back()->with('success', 'Tag deleted.');
    }
}
