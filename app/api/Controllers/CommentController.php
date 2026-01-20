<?php

namespace Api\Controllers;

use Api\Services\CommentService;

class CommentController extends ApiController
{
    private CommentService $commentService;

    public function __construct(?CommentService $commentService = null)
    {
        $this->commentService = $commentService ?? new CommentService();
    }

    public function index(int $postId): void
    {
        try {
            $comments = $this->commentService->getCommentsByPostId($postId);
            $commentsArray = array_map(fn($comment) => $comment->toArray(), $comments);

            $this->sendSuccess(['comments' => $commentsArray]);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 404);
        }
    }

    public function create(int $postId): void
    {
        try {
            $data = $this->getJsonInput();
            $comment = $this->commentService->createComment($postId, $data);

            $this->sendSuccess($comment->toArray(), 'Comment created successfully', 201);
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
            $comment = $this->commentService->updateComment($id, $data);

            $this->sendSuccess($comment->toArray(), 'Comment updated successfully');
        } catch (\InvalidArgumentException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 404);
        }
    }

    public function delete(int $id): void
    {
        try {
            $this->commentService->deleteComment($id);

            $this->sendSuccess([], 'Comment deleted successfully');
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 404);
        }
    }
}
