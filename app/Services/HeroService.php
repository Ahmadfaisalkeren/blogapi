<?php

namespace App\Services;

use App\Models\Hero;
use Illuminate\Support\Facades\Storage;

/**
 * Class HeroService.
 */
class HeroService
{
    public function getHeroes()
    {
        $heroes = Hero::orderBy('created_at', 'desc')->get();

        return $heroes;
    }

    public function storeHero(array $heroData)
    {
        if (isset($heroData['image'])) {
            $heroData['image'] = $this->storeImage($heroData['image']);
        }

        $hero = Hero::create($heroData);

        return $hero;
    }

    private function storeImage($image)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('images/heroes', $imageName, 'public');

        return $imagePath;
    }

    public function getHeroById(string $heroId)
    {
        $hero = Hero::findOrFail($heroId);

        return $hero;
    }

    public function updateHero(string $heroId, array $heroData)
    {
        $hero = Hero::findOrFail($heroId);

        $hero->title = $heroData['title'] ?? $hero->title;

        if (isset($heroData['image'])) {
            $this->updateImage($hero, $heroData['image']);
        }

        $hero->save();

        return $hero;
    }

    private function updateImage(Hero $hero, $image)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('public/images/heroes', $imageName);

        if ($hero->image) {
            Storage::delete('public/' . $hero->image);
        }

        $hero->image = str_replace('public/', '', $imagePath);
    }

    public function deleteHero(string $heroId)
    {
        $hero = Hero::findOrFail($heroId);

        $this->deleteImage($hero->image);

        $hero->delete();

        return $hero;
    }

    private function deleteImage($imagePath)
    {
        if ($imagePath) {
            Storage::delete('public/' . $imagePath);
        }
    }
}
