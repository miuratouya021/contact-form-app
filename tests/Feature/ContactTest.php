<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせ画面を表示できる(): void
    {
        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function 確認画面を表示できる(): void
    {
        // Arrange
        $category = Category::create([
            'content' => 'テスト用カテゴリー',
        ]);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'building' => '',
            'category_id' => $category->id,
            'detail' => 'テスト用のお問い合わせです',
        ];

        // Act
        $response = $this->post('/contacts/confirm', $data);

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function お問い合わせを登録できる(): void
    {
        // Arrange
        $category = Category::create([
            'content' => 'テスト用カテゴリー',
        ]);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'building' => '',
            'category_id' => $category->id,
            'detail' => 'テスト用のお問い合わせです',
        ];

        // Act
        $response = $this->post('/contacts', $data);

        // Assert
        $response->assertRedirect('/thanks');
        $this->assertDatabaseHas('contacts', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function 必須項目が空だとエラーになる(): void
    {
        // Act
        $response = $this->post('/contacts/confirm', []);

        // Assert
        $response->assertSessionHasErrors('first_name');
    }
}