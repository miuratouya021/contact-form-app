<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // タグ候補を作成
        DB::table('tags')->insert([
            ['name' => '質問', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '要望', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '不具合報告', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ご意見', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'その他', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
