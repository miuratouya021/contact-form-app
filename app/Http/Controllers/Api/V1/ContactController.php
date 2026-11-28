<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Resources\ContactResource;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Requests\IndexContactRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        $keyword = $request->input('keyword');

        $query = Contact::with('category', 'tags');

        if ($request->filled('keyword')) {
            $query->where(function ($query) use ($keyword) {
                $query->where('first_name', 'like', '%' . $keyword . '%')
                    ->orWhere('last_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->has('gender') && $request->gender != 0) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $contacts = $query->paginate(15);

        return ContactResource::collection($contacts);
    }

    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    public function store(StoreContactRequest $request)
    {

        $store = $request->validated();

        $contact = Contact::create($store);

        $contact->tags()->attach($store['tag_ids'] ?? []);

        return new ContactResource($contact);
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {

        $update = $request->validated();

        $contact->update($update);

        $contact->tags()->sync($update['tag_ids'] ?? []);

        return new ContactResource($contact);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json(null, 204);
    }
}