<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tag;

class TagTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /** @test */
    public function ログイン済みユーザーはタグを追加できる(): void
    {
        // Arrange
        $user = User::factory()->create();

        $data = [
            'name' => 'テスト用タグ',
        ];

        // Act
        $response = $this->actingAs($user)->post('/admin/tags', $data);

        // Assert
        $response->assertRedirect(route('admin'));
        $this->assertDatabaseHas('tags', [
            'name' => 'テスト用タグ',
        ]);
    }

    /** @test */
    public function ログイン済みユーザーはタグを更新できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        $tag = Tag::create([
            'name' => 'テスト用タグ',
        ]);
        $data = [
            'name' => '更新後のタグ',
        ];

        // Act
        $response = $this->actingAs($user)
            ->put("/admin/tags/{$tag->id}", $data);

        // Assert
        $response->assertRedirect(route('admin'));
        $this->assertDatabaseHas('tags', [
            'name' => '更新後のタグ',
        ]);
    }

    /** @test */
    public function ログイン済みユーザーはタグを削除できる(): void
    {
        // Arrange
        $user = User::factory()->create();

        $tag = Tag::create([
            'name' => 'テスト用タグ',
        ]);

        // Act
        $response = $this->actingAs($user)->delete("/admin/tags/{$tag->id}");

        // Assert
        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
        $response->assertRedirect(route('admin'));
    }

    /** @test */
    public function タグ名が空だとエラーになる(): void
    {
        // Arrange
        $user = User::factory()->create();

        $data = [
            'name' => '',
        ];

        // Act
        $response = $this->actingAs($user)->post('/admin/tags', $data);

        // Assert
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function タグ名が重複するとエラーになる(): void
    {
        // Arrange
        $user = User::factory()->create();

        Tag::create([
            'name' => 'テスト用タグ',
        ]);

        $data = [
            'name' => 'テスト用タグ',
        ];

        // Act
        $response = $this->actingAs($user)->post('/admin/tags', $data);

        // Assert
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function タグ名が51文字だとエラーになる(): void
    {
        // Arrange
        $user = User::factory()->create();

        $data = [
            'name' => '123456789012345678901234567890123456789012345678901',
        ];

        // Act
        $response = $this->actingAs($user)->post('/admin/tags', $data);

        // Assert
        $response->assertSessionHasErrors('name');
    }
}
