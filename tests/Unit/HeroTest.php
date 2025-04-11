<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Hero;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test; // Import the Test attribute

class HeroTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_hero()
    {
        $hero = Hero::create([
            'id' => Str::uuid(),
            'title' => 'Superman',
            'image' => 'superman.jpg',
        ]);

        $this->assertDatabaseHas('heroes', [
            'id' => $hero->id,
            'title' => 'Superman',
            'image' => 'superman.jpg',
        ]);
    }

    #[Test]
    public function it_has_fillable_attributes()
    {
        $hero = new Hero();
        $this->assertEquals(['title', 'image'], $hero->getFillable());
    }
}
