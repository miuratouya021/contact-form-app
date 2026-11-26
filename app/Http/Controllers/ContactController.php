<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;

class ContactController extends Controller
{

    // お問い合わせフォームの入力ページ
    public function index()
    {
        $categories = Category::all();

        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    // お問い合わせフォームの確認ページ
    public function confirm(StoreContactRequest $request)
    {

        $validated = $request->validated();

        $category = Category::findOrFail($validated['category_id']);

        $tags = Tag::whereIn('id', $validated['tag_ids'] ?? [])->get();

        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    // サンクスページ
    public function thanks()
    {
        return view('contact.thanks');
    }

    public function store(StoreContactRequest $request)
    {

        $store = $request->validated();

        if ($request->input('action') === 'edit') {
            return redirect('/')
                ->withInput($store);

        }

        $contact = Contact::create($store);

        $contact->tags()->attach($store['tag_ids'] ?? []);

        return redirect('/thanks');
    }
}
