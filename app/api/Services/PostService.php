<?php

namespace Api\Services;

use Api\Entities\Post;
use Api\Repositories\PostRepository;
use Api\Repositories\UserRepository;

class PostService
{
    private PostRepository $postRepository;
    private UserRepository $userRepository;

    public function __construct(
        PostRepository $postRepository = null,
        UserRepository $userRepository = null
    ) {
        $this->postRepository = $postRepository ?? new PostRepository();
        $this->userRepository = $userRepository ?? new UserRepository();
    }

    public function getAllPosts(int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        $posts = $this->postRepository->findAll($limit, $offset);
        $total = $this->postRepository->count();

        return [
            'posts' => $posts,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
            ]
        ];
    }

    public function getPostById(int $id): ?Post
    {
        return $this->postRepository->findById($id);
    }

    public function createPost(array $data): Post
    {
        $this->validatePostData($data);

        $user = $this->userRepository->findById($data['user_id']);
        if (!$user) {
            throw new \Exception('User not found');
        }

        $post = new Post($data['title'], $data['content'], $data['user_id']);

        return $this->postRepository->create($post);
    }

    public function updatePost(int $id, array $data): Post
    {
        $post = $this->postRepository->findById($id);
        if (!$post) {
            throw new \Exception('Post not found');
        }

        if (isset($data['title'])) {
            $post->setTitle($data['title']);
        }

        if (isset($data['content'])) {
            $post->setContent($data['content']);
        }

        $this->postRepository->update($post);

        return $post;
    }

    public function deletePost(int $id): void
    {
        $post = $this->postRepository->findById($id);
        if (!$post) {
            throw new \Exception('Post not found');
        }

        $this->postRepository->delete($id);
    }

    private function validatePostData(array $data): void
    {
        if (empty($data['title']) || empty($data['content']) || empty($data['user_id'])) {
            throw new \InvalidArgumentException('Title, content and user_id are required');
        }

        if (strlen($data['title']) < 3) {
            throw new \InvalidArgumentException('Title must be at least 3 characters');
        }

        if (strlen($data['content']) < 10) {
            throw new \InvalidArgumentException('Content must be at least 10 characters');
        }
    }
}
