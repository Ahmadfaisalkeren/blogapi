<?php

namespace Tests\Unit;

use App\Models\Post;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function test_post_has_a_title()
    {
        $post = Post::factory()->create(['title' => 'Test Post']);
        $this->assertEquals('Test Post', $post->title);
    }

    public function test_post_belongs_to_series()
    {
        $post = Post::factory()->create();
        $this->assertNotNull($post->series);
    }
}
