<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = Contact::factory()
            ->count(20)
            ->create();
        foreach ($contacts as $contact) {
            $tagCount = fake()->randomElement([1, 2, 3]);
            $tags = DB::table('tags')
                ->inRandomOrder()
                ->limit($tagCount)
                ->get();

            foreach ($tags as $tag) {
                $contact->tags()->attach($tag->id);
            }
        }
    }
}
