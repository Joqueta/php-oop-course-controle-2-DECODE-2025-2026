<?php

namespace Api\Config;

use Api\Controllers\AuthController;
use Api\Controllers\PostController;
use Api\Controllers\CommentController;
use Api\Responses\ApiResponse;

class Router
{
    private string $method;
    private string $path;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function route(): void
    {
        try {
            if ($this->path === '/api/auth/register' && $this->method === 'POST') {
                (new AuthController())->register();
                return;
            }

            if ($this->path === '/api/auth/login' && $this->method === 'POST') {
                (new AuthController())->login();
                return;
            }

            if ($this->path === '/api/posts' && $this->method === 'GET') {
                (new PostController())->index();
                return;
            }

            if (preg_match('#^/api/posts/(\d+)$#', $this->path, $matches) && $this->method === 'GET') {
                (new PostController())->show((int) $matches[1]);
                return;
            }

            if ($this->path === '/api/posts' && $this->method === 'POST') {
                (new PostController())->create();
                return;
            }

            if (preg_match('#^/api/posts/(\d+)$#', $this->path, $matches) && $this->method === 'PATCH') {
                (new PostController())->update((int) $matches[1]);
                return;
            }

            if (preg_match('#^/api/posts/(\d+)$#', $this->path, $matches) && $this->method === 'DELETE') {
                (new PostController())->delete((int) $matches[1]);
                return;
            }

            if (preg_match('#^/api/posts/(\d+)/comments$#', $this->path, $matches) && $this->method === 'GET') {
                (new CommentController())->index((int) $matches[1]);
                return;
            }

            if (preg_match('#^/api/posts/(\d+)/comments$#', $this->path, $matches) && $this->method === 'POST') {
                (new CommentController())->create((int) $matches[1]);
                return;
            }

            if (preg_match('#^/api/comments/(\d+)$#', $this->path, $matches) && $this->method === 'PATCH') {
                (new CommentController())->update((int) $matches[1]);
                return;
            }

            if (preg_match('#^/api/comments/(\d+)$#', $this->path, $matches) && $this->method === 'DELETE') {
                (new CommentController())->delete((int) $matches[1]);
                return;
            }

            ApiResponse::error('Route not found', 404)->send();
        } catch (\Exception $e) {
            ApiResponse::error('Internal server error: ' . $e->getMessage(), 500)->send();
        }
    }
}
