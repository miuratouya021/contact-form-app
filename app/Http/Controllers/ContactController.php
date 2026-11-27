<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\ExportContactRequest;

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

    public function export(ExportContactRequest $request)
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

        $contacts = $query->latest()->get();

        return response()->streamDownload(function () use ($contacts) {

            $file = fopen('php://output', 'w');

            // BOMの追加
            fwrite($file, "\xEF\xBB\xBF");

            // 性別の変換
            $genderLabels = [
                1 => '男性',
                2 => '女性',
                3 => 'その他',
            ];

            // CSVのヘッダー
            fputcsv($file, [
                'ID',
                '氏名',
                '性別',
                'メール',
                '電話',
                '住所',
                '建物',
                'カテゴリ',
                '内容',
                '作成日時',
            ], ',', '"', '');

            // CSVのデータ
            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->first_name . ' ' . $contact->last_name,
                    $genderLabels[$contact->gender],
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building,
                    $contact->category->content,
                    $contact->detail,
                    $contact->created_at,
                ], ',', '"', '');
            }

            fclose($file);

        }, 'contacts.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
