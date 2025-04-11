<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use App\Services\ContentBlocksService;

class PostsService
{
    protected $contentBlocksService;

    public function __construct(ContentBlocksService $contentBlocksService)
    {
        $this->contentBlocksService = $contentBlocksService;
    }

    public function getPosts()
    {
        return Post::with('contentBlocks')->orderBy('created_at', 'desc')->get();
    }

    public function publishedPosts()
    {
        return Post::with('contentBlocks')->where('status', 'publish')->orderBy('created_at', 'desc')->get();
    }

    public function storePost(array $postData)
    {
        DB::beginTransaction();

        try {
            $postId = $postData['id'] ?? null;
            $post = $postId ? Post::findOrFail($postId) : new Post();

            $post->fill([
                'title' => $postData['title'] ?? $post->title,
                'slug' => $postData['slug'] ?? $post->slug,
                'author' => $postData['author'] ?? $post->author,
                'post_date' => $postData['post_date'] ?? $post->post_date,
                'status' => $postData['status'] ?? $post->status,
            ]);
            $post->save();

            if (isset($postData['content_blocks'])) {
                $this->contentBlocksService->updateContentBlocks($post, $postData['content_blocks']);
            }

            DB::commit();
            return $post->load('contentBlocks');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getPostById($id)
    {
        $post = Post::with([
            'contentBlocks' => function ($query) {
                $query->where('parent_type', Post::class)
                    ->orderBy('order', 'asc');
            },
            'contentBlocks.paragraphs',
            'contentBlocks.headers',
            'contentBlocks.listItems',
            'contentBlocks.codes',
            'contentBlocks.images'
        ])->find($id);

        return $post;
    }

    public function storeTemporaryImage($image)
    {
        return $this->contentBlocksService->storeTemporaryImage($image);
    }

    public function deleteImageBlock($type, $id)
    {
        return $this->contentBlocksService->deleteImageBlock($type, $id);
    }

    public function deletePost($postId)
    {
        DB::beginTransaction();

        try {
            $post = Post::find($postId);
            if (!$post) throw new \Exception('Post not found.');

            $this->contentBlocksService->updateContentBlocks($post, []);

            $post->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Post deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
