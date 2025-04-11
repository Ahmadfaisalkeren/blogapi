<?php

namespace App\Services;

use App\Models\Editor\Code;
use App\Models\Editor\Image;
use App\Models\Editor\Header;
use App\Models\Editor\ListItem;
use App\Models\Editor\Paragraph;
use App\Models\Editor\ContentBlocks;
use Illuminate\Support\Facades\Storage;

class ContentBlocksService
{
    public function updateContentBlocks($parent, array $contentBlocks)
    {
        $existingBlockIds = ContentBlocks::where('parent_id', $parent->id)
            ->where('parent_type', get_class($parent))
            ->pluck('id')
            ->toArray();

        $incomingBlockIds = array_column($contentBlocks, 'id');
        $blocksToDelete = array_diff($existingBlockIds, $incomingBlockIds);

        if (!empty($blocksToDelete)) {
            foreach ($blocksToDelete as $blockId) {
                $contentBlock = ContentBlocks::find($blockId);
                if ($contentBlock) {
                    $this->deleteOrphanedData($contentBlock);
                    $contentBlock->delete();
                }
            }
        }

        foreach ($contentBlocks as $blockIndex => $blockData) {
            $contentBlock = ContentBlocks::updateOrCreate(
                [
                    'parent_id' => $parent->id,
                    'parent_type' => get_class($parent),
                    'id' => $blockData['id'] ?? null,
                ],
                [
                    'type' => $blockData['type'],
                    'order' => $blockData['order'] ?? ($blockIndex + 1),
                ]
            );

            $this->handleBlockData($blockData, $contentBlock);
        }
    }

    protected function handleBlockData(array $blockData, ContentBlocks $contentBlock)
    {
        switch ($blockData['type']) {
            case 'paragraph':
                Paragraph::updateOrCreate(
                    ['content_block_id' => $contentBlock->id],
                    ['paragraph' => $blockData['data']['text'] ?? null]
                );
                break;

            case 'header':
                Header::updateOrCreate(
                    ['content_block_id' => $contentBlock->id],
                    [
                        'level' => $blockData['data']['level'] ?? null,
                        'header' => $blockData['data']['text'] ?? null,
                    ]
                );
                break;

            case 'code':
                Code::updateOrCreate(
                    ['content_block_id' => $contentBlock->id],
                    ['code' => $blockData['data']['code'] ?? null]
                );
                break;

            case 'list':
                $this->handleListBlock($blockData, $contentBlock);
                break;

            case 'image':
                $this->handleImageBlock($blockData, $contentBlock);
                break;
        }
    }

    protected function handleListBlock(array $blockData, ContentBlocks $contentBlock)
    {
        ListItem::where('content_block_id', $contentBlock->id)->delete();

        foreach ($blockData['data']['items'] as $index => $item) {
            ListItem::create([
                'content_block_id' => $contentBlock->id,
                'list' => $item['content'],
                'style' => $blockData['data']['style'] ?? 'unordered',
                'meta' => json_encode($item['meta'] ?? []),
                'order' => $index + 1
            ]);
        }
    }

    protected function handleImageBlock(array $blockData, ContentBlocks $contentBlock)
    {
        $imageUrl = $blockData['data']['file']['url'] ?? null;
        if ($imageUrl) {
            $relativeImageUrl = str_replace(asset('storage') . '/', '', $imageUrl);
            $temporaryImage = Image::where('image_url', $relativeImageUrl)->first();
            if ($temporaryImage) {
                $temporaryImage->update([
                    'type' => 'permanent',
                    'content_block_id' => $contentBlock->id,
                    'parent_type' => get_class($contentBlock->parent),
                ]);
            }
        }
    }

    protected function deleteOrphanedData(ContentBlocks $contentBlock)
    {
        switch ($contentBlock->type) {
            case 'paragraph':
                Paragraph::where('content_block_id', $contentBlock->id)->delete();
                break;
            case 'header':
                Header::where('content_block_id', $contentBlock->id)->delete();
                break;
            case 'code':
                Code::where('content_block_id', $contentBlock->id)->delete();
                break;
            case 'list':
                ListItem::where('content_block_id', $contentBlock->id)->delete();
                break;
            case 'image':
                $image = Image::where('content_block_id', $contentBlock->id)->first();
                if ($image) {
                    if (Storage::disk('public')->exists($image->image_url)) {
                        Storage::disk('public')->delete($image->image_url);
                    }
                    $image->delete();
                }
                break;
        }
    }

    public function storeTemporaryImage($image)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('posts/images', $imageName, 'public');

        $image = Image::create([
            'image_url' => $imagePath,
            'type' => 'temporary'
        ]);

        return $image;
    }

    public function deleteImageBlock($type, $id)
    {
        if (!in_array($type, ['temporary', 'permanent'])) {
            throw new \InvalidArgumentException('Invalid image type provided');
        }

        $image = Image::find($id);
        if (!$image || $image->type !== $type) {
            return response()->json(['success' => false, 'message' => 'Image not found or type mismatch'], 404);
        }

        if (Storage::disk('public')->exists($image->image_url)) {
            Storage::disk('public')->delete($image->image_url);
        }

        $contentBlockId = null;
        if ($type === 'permanent' && $image->content_block_id) {
            $contentBlockId = $image->content_block_id;
        }

        $image->delete();

        if ($contentBlockId) {
            $contentBlock = ContentBlocks::find($contentBlockId);
            if ($contentBlock) {
                $contentBlock->update(['type' => 'paragraph']);

                Paragraph::updateOrCreate(
                    ['content_block_id' => $contentBlockId],
                    ['paragraph' => '']
                );
            }
        }

        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    }
}
