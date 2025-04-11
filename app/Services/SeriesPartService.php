<?php

namespace App\Services;

use App\Models\Series;
use App\Models\SeriesPart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SeriesPartService
{
    protected $contentBlocksService;

    public function __construct(ContentBlocksService $contentBlocksService)
    {
        $this->contentBlocksService = $contentBlocksService;
    }

    public function getSeriesPartBySlug(string $seriesSlug)
    {
        $series = Series::where('slug', $seriesSlug)->firstOrFail();
        return SeriesPart::with('series')
            ->where('series_id', $series->id)
            ->orderBy('created_at', 'ASC')
            ->get();
    }

    public function storeSeriesPart(array $seriesPartData)
    {
        DB::beginTransaction();

        try {
            $seriesPartId = $seriesPartData['id'] ?? null;
            $seriesPart = $seriesPartId ? SeriesPart::findOrFail($seriesPartId) : new SeriesPart();

            $seriesPart->fill([
                'series_id' => $seriesPartData['series_id'] ?? $seriesPart->series_id,
                'part_number' => $seriesPartData['part_number'] ?? $seriesPart->part_number,
                'title' => $seriesPartData['title'] ?? $seriesPart->title,
            ]);

            $seriesPart->save();

            if (isset($seriesPartData['content_blocks'])) {
                $this->contentBlocksService->updateContentBlocks($seriesPart, $seriesPartData['content_blocks']);
            }

            DB::commit();

            return $seriesPart->load(
                'contentBlocks.paragraphs',
                'contentBlocks.headers',
                'contentBlocks.codes',
                'contentBlocks.images',
                'contentBlocks.listItems'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getSeriesPartById($id)
    {
        return SeriesPart::with([
            'contentBlocks' => function ($query) {
                $query->where('parent_type', SeriesPart::class)
                    ->orderBy('order', 'asc');
            },
            'contentBlocks.paragraphs',
            'contentBlocks.headers',
            'contentBlocks.codes',
            'contentBlocks.images',
            'contentBlocks.listItems'
        ])->where('series_id', $id)->get();
    }

    public function getSeriesPartBySeriesPartId($seriesPartId)
    {
        return SeriesPart::with([
            'contentBlocks' => function ($query) {
                $query->where('parent_type', SeriesPart::class)
                    ->orderBy('order', 'asc');
            },
            'contentBlocks.paragraphs',
            'contentBlocks.headers',
            'contentBlocks.codes',
            'contentBlocks.images',
            'contentBlocks.listItems'
        ])->where('id', $seriesPartId)->get();
    }

    public function storeTemporaryImage($image)
    {
        return $this->contentBlocksService->storeTemporaryImage($image);
    }

    public function deleteImageBlock($type, $id)
    {
        return $this->contentBlocksService->deleteImageBlock($type, $id);
    }

    public function deleteSeriesPart($id)
    {
        DB::beginTransaction();

        try {
            $seriesPart = SeriesPart::find($id);
            if (!$seriesPart) {
                throw new \Exception('Series part not found');
            }

            $this->contentBlocksService->updateContentBlocks($seriesPart, []);
            $seriesPart->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Series part deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting series part: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting series part: ' . $e->getMessage()], 500);
        }
    }
}
