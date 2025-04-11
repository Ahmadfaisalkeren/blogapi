<?php

namespace App\Services;

use App\Models\Series;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class SeriesService.
 */
class SeriesService
{
    public function getSeries()
    {
        $series = Series::all();

        return $series;
    }

    public function publishedSeries()
    {
        $publishedSeries = Series::where('status', '=', 'publish')->orderBy('created_at', 'DESC')->get();

        return $publishedSeries;
    }

    public function storeSeries(array $seriesData)
    {
        if (isset($seriesData['image'])) {
            $seriesData['image'] = $this->storeImage($seriesData['image']);
        }

        $series = Series::create($seriesData);

        return $series;
    }

    private function storeImage($image)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('images/series', $imageName, 'public');

        return $imagePath;
    }

    public function getSeriesBySlug($slug)
    {
        $series = Series::where('slug', $slug)->firstOrFail();

        return $series;
    }

    public function getSeriesById(string $seriesId)
    {
        $series = Series::findOrFail($seriesId);

        return $series;
    }

    public function updateSeries(string $seriesId, array $seriesData)
    {
        $series = Series::findOrFail($seriesId);

        $series->title = $seriesData['title'] ?? $series->title;
        $series->slug = $seriesData['slug'] ?? $series->slug;
        $series->author = $seriesData['author'] ?? $series->author;
        $series->series_date = $seriesData['series_date'] ?? $series->series_date;
        $series->status = $seriesData['status'] ?? $series->status;

        if (isset($seriesData['image'])) {
            $this->updateImage($series, $seriesData['image']);
        }

        $series->save();

        return $series;
    }

    private function updateImage(Series $series, $image)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('public/images/series', $imageName);

        if ($series->image) {
            Storage::delete('public/' . $series->image);
        }

        $series->image = str_replace('public/', '', $imagePath);
    }

    public function deleteSeries($id)
    {
        $series = Series::findOrFail($id);

        DB::beginTransaction();

        try {
            foreach ($series->seriesParts as $seriesPart) {
                app(SeriesPartService::class)->deleteSeriesPart($seriesPart->id);
            }

            $series->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }
}
