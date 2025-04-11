<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\SeriesPartService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesPart\StoreSeriesPartRequest;
use App\Http\Requests\SeriesPart\UpdateSeriesPartRequest;
use Illuminate\Support\Facades\Log;

class SeriesPartController extends Controller
{
    protected $seriesPartService;

    public function __construct(SeriesPartService $seriesPartService)
    {
        $this->seriesPartService = $seriesPartService;
    }

    public function index(string $seriesSlug)
    {
        $seriesPart = $this->seriesPartService->getSeriesPartBySlug($seriesSlug);

        return response()->json([
            'status' => 200,
            'message' => "Series Part Data Fetched Successfully",
            'seriesPart' => $seriesPart,
        ], 200);
    }

    public function store(StoreSeriesPartRequest $request, $id = null)
    {
        try {
            $seriesPartData = $request->all();
            $seriesPartData['id'] = $id;

            $seriesPart = $this->seriesPartService->storeSeriespart($seriesPartData);

            $message = $id ? 'Series Part Updated Successfully' : 'Series Part Created Successfully';

            return response()->json([
                'status' => 200,
                'message' => $message,
                'seriesPart' => $seriesPart,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'Failed to process series part',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $seriesPart = $this->seriesPartService->getSeriesPartById($id);

        return response()->json([
            'status' => 200,
            'message' => 'Series Part Fetched Successfully',
            'seriesPart' => $seriesPart,
        ], 200);
    }

    public function show($seriesPartId)
    {
        $seriesPart = $this->seriesPartService->getSeriesPartBySeriesPartId($seriesPartId);

        return response()->json([
            'status' => 200,
            'message' => 'Series Part Fetched Successfully',
            'seriesPart' => $seriesPart,
        ], 200);
    }

    public function destroy($id)
    {
        $this->seriesPartService->deleteSeriesPart($id);

        return response()->json([
            'status' => 200,
            'message' => 'Series Part Deleted Successfully',
        ], 200);
    }
}
