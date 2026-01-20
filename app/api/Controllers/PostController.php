<?php

namespace Api\Controllers;

use Api\Services\PostService;
use Api\Repositories\CommentRepository;

class PostController extends ApiController
{
    private PostService $postService;
    private CommentRepository $commentRepository;

    public function __construct(?PostService $postService = null, ?CommentRepository $commentRepository = null)
    {
        $this->postService = $postService ?? new PostService();
        $this->commentRepository = $commentRepository ?? new CommentRepository();
    }

    public function index(): void
    {
        $page = (int) $this->getParam('page', 1);
        $limit = (int) $this->getParam('limit', 10);

        $result = $this->postService->getAllPosts($page, $limit);

        $postsArray = array_map(fn($post) => $post->toArray(), $result['posts']);

        $this->sendSuccess([
            'posts' => $postsArray,
            'pagination' => $result['pagination']
        ]);
    }

    public function show(int $id): void
    {
        try {
            $post = $this->postService->getPostById($id);

            if (!$post) {
                $this->sendError('Post not found', 404);
                return;
            }

            $comments = $this->commentRepository->findByPostId($id);
            $commentsArray = array_map(fn($comment) => $comment->toArray(), $comments);

            $this->sendSuccess([
                'post' => $post->toArray(),
                'comments' => $commentsArray
            ]);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function create(): void
    {
        try {
            $data = $this->getJsonInput();
            $post = $this->postService->createPost($data);

            $this->sendSuccess($post->toArray(), 'Post created successfully', 201);
        } catch (\InvalidArgumentException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 404);
        }
    }

    public function update(int $id): void
    {
        try {
            $data = $this->getJsonInput();
            $post = $this->postService->updatePost($id, $data);

            $this->sendSuccess($post->toArray(), 'Post updated successfully');
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 404);
        }
    }

    public function delete(int $id): void
    {
        try {
            $this->postService->deletePost($id);

            $this->sendSuccess([], 'Post deleted successfully');
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 404);
        }
    }
}
