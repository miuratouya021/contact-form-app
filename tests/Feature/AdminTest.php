<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use App\Models\Category;

class AdminTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /** @test */
    public function 管理画面にアクセスするとログインページにリダイレクトされる(): void
    {
        // Act
        $response = $this->get(route('admin'));
        // Assert
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 問い合わせ詳細画面にアクセスするとログインページにリダイレクトされる(): void
    {
        // Act
        $response = $this->get('/admin/contacts/1');

        // Assert
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 問い合わせを削除しようとするとログインページにリダイレクトされる(): void
    {
        // Act
        $response = $this->delete('/admin/contacts/1');

        // Assert
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ログイン済みユーザーは管理画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->get(route('admin'));

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function ログイン済みユーザーは問い合わせ詳細画面を表示できる(): void
    {
        // Arrange
        $user = User::factory()->create();

        Category::create([
            'content' => 'テスト用カテゴリー',
        ]);

        $contact = Contact::factory()->create();

        // Act
        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function ログイン済みユーザーは問い合わせを削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();

        Category::create([
            'content' => 'テスト用カテゴリー',
        ]);

        $contact = Contact::factory()->create();

        // Act
        $response = $this->actingAs($user)->delete("/admin/contacts/{$contact->id}");

        // Assert
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
        $response->assertRedirect(route('admin'));
    }

    /** @test */
    public function 不正な性別で検索するとバリデーションエラーになる(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->get('/admin?gender=9');

        // Assert
        $response->assertSessionHasErrors('gender');
    }

    /** @test */
    public function 存在しないカテゴリーで検索するとバリデーションエラーになる(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->get('/admin?category_id=99999');

        // Assert
        $response->assertSessionHasErrors('category_id');
    }

    /** @test */
    public function 不正な日付で検索するとバリデーションエラーになる(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->get('/admin?date=abc');

        // Assert
        $response->assertSessionHasErrors('date');
    }
}