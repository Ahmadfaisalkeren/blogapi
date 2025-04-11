<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\PostsService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\StorePostRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Http\Requests\Image\StoreTemporaryImageRequest;

class PostsController extends Controller
{
    protected $postsService;

    public function __construct(PostsService $postsService)
    {
        $this->postsService = $postsService;
    }

    public function index()
    {
        $posts = $this->postsService->getPosts();

        return response()->json([
            'status' => 200,
            'message' => "Posts Data Fetched Successfully",
            'posts' => $posts,
        ], 200);
    }

    public function publishedPosts()
    {
        $posts = $this->postsService->publishedPosts();

        return response()->json([
            'status' => 200,
            'message' => "Published Posts Fetched Successfully",
            'posts' => $posts,
        ], 200);
    }

    public function store(StorePostRequest $request, $id = null)
    {
        try {
            $postData = $request->all();
            $postData['id'] = $id;

            $post = $this->postsService->storePost($postData);

            $message = $id ? 'Post updated successfully' : 'Post created successfully';

            return response()->json([
                'status' => 200,
                'message' => $message,
                'post' => $post,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'Failed to process post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeTemporaryImage(StoreTemporaryImageRequest $request)
    {
        $image = $this->postsService->storeTemporaryImage($request->file('image_url'));

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => asset('storage/' . $image->image_url),
                'id' => $image->id,
            ],
        ]);
    }

    public function deleteImage($type, $id)
    {
        try {
            $deleted = $this->postsService->deleteImageBlock($type, $id);

            if (!$deleted) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Image not found or type mismatch',
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Image deleted successfully',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while deleting the image',
            ], 500);
        }
    }

    public function show($slug)
    {
        $post = $this->postsService->getPostBySlug($slug);

        return response()->json([
            'status' => 200,
            'message' => 'Post Fetched Successfully',
            'post' => $post,
        ], 200);
    }

    public function showById($id)
    {
        $post = $this->postsService->getPostById($id);

        return response()->json([
            'status' => 200,
            'message' => 'Post Fetched Successfully',
            'post' => $post,
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->postsService->deletePost($id);

        if (!$deleted) {
            return response()->json([
                'status' => 404,
                'message' => 'Post not found',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Post deleted successfully',
        ]);
    }
}
