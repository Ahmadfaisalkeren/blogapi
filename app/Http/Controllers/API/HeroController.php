<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\HeroService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hero\StoreHeroRequest;
use App\Http\Requests\Hero\UpdateHeroRequest;

class HeroController extends Controller
{
    protected $heroService;

    public function __construct(HeroService $heroService)
    {
        $this->heroService = $heroService;
    }

    public function index()
    {
        $heroes = $this->heroService->getHeroes();

        return response()->json([
            'status' => 200,
            'message' => "Heroes Data Fetched Successfully",
            'heroes' => $heroes,
        ], 200);
    }

    public function store(StoreHeroRequest $request)
    {
        $hero = $this->heroService->storeHero($request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Hero Stored Successfully',
            'hero' => $hero,
        ], 200);
    }

    public function edit(string $heroId)
    {
        $hero = $this->heroService->getHeroById($heroId);

        return response()->json([
            'status' => 200,
            'message' => 'Hero Fetched Successfully',
            'hero' => $hero,
        ], 200);
    }

    public function update(string $heroId, UpdateHeroRequest $request)
    {
        $hero = $this->heroService->updateHero($heroId, $request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Hero Updated Successfully',
            'hero' => $hero,
        ], 200);
    }

    public function destroy(string $heroId)
    {
        $hero = $this->heroService->deleteHero($heroId);

        return response()->json([
            'status' => 200,
            'message' => 'Hero Deleted Successfully',
            'hero' => $hero,
        ], 200);
    }
}
