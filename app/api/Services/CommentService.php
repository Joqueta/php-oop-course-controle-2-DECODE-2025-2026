<?php

namespace Api\Services;

use Api\Entities\Comment;
use Api\Repositories\CommentRepository;
use Api\Repositories\PostRepository;
use Api\Repositories\UserRepository;

class CommentService
{
    private CommentRepository $commentRepository;
    private PostRepository $postRepository;
    private UserRepository $userRepository;

    public function __construct(
        CommentRepository $commentRepository = null,
        PostRepository $postRepository = null,
        UserRepository $userRepository = null
    ) {
        $this->commentRepository = $commentRepository ?? new CommentRepository();
        $this->postRepository = $postRepository ?? new PostRepository();
        $this->userRepository = $userRepository ?? new UserRepository();
    }

    public function getCommentsByPostId(int $postId): array
    {
        $post = $this->postRepository->findById($postId);
        if (!$post) {
            throw new \Exception('Post not found');
        }

        return $this->commentRepository->findByPostId($postId);
    }

    public function createComment(int $postId, array $data): Comment
    {
        $this->validateCommentData($data);

        $post = $this->postRepository->findById($postId);
        if (!$post) {
            throw new \Exception('Post not found');
        }

        $user = $this->userRepository->findById($data['user_id']);
        if (!$user) {
            throw new \Exception('User not found');
        }

        $comment = new Comment($data['content'], $data['user_id'], $postId);

        return $this->commentRepository->create($comment);
    }

    public function updateComment(int $id, array $data): Comment
    {
        $comment = $this->commentRepository->findById($id);
        if (!$comment) {
            throw new \Exception('Comment not found');
        }

        if (isset($data['content'])) {
            if (strlen($data['content']) < 1) {
                throw new \InvalidArgumentException('Content cannot be empty');
            }
            $comment->setContent($data['content']);
        }

        $this->commentRepository->update($comment);

        return $comment;
    }

    public function deleteComment(int $id): void
    {
        $comment = $this->commentRepository->findById($id);
        if (!$comment) {
            throw new \Exception('Comment not found');
        }

        $this->commentRepository->delete($id);
    }

    private function validateCommentData(array $data): void
    {
        if (empty($data['content']) || empty($data['user_id'])) {
            throw new \InvalidArgumentException('Content and user_id are required');
        }

        if (strlen($data['content']) < 1) {
            throw new \InvalidArgumentException('Content cannot be emil empty');
        }
    }
}
