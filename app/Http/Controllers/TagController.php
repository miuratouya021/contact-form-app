<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:tags',
        ]);
        Tag::create([
            'name' => $request->name,
        ]);
        return redirect('/admin');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {

        $request->validate([
            'name' => 'required|string|max:50|unique:tags,name,' . $tag->id,
        ]);
        $tag->update([
            'name' => $request->name,
        ]);
        return redirect('/admin');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect('/admin');
    }
}