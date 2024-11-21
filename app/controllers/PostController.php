<?php

/**CRUD operations for posts are managed by this PostController class. Nothing I tried appeared to work at first. The techniques for accepting JSON input, verifying data, and sending suitable HTTP replies frequently failed.

I had to use internet resources for guidance after finding it difficult to debug and comprehend what was wrong. After doing some research, I was able to find helpful instructions on how to use PHP's file_get_contents, json_decode, and HTTP request processing.

Resources used:
PHP Manual for file_get_contents and json_decode: https://www.php.net/manual/en/function.file-get-contents.php
Handling JSON APIs in PHP: https://www.tutorialrepublic.com/php-tutorial/php-json-parsing.php

Resolving the problems and making sure the controller functions as planned required the use of these resources. I couldn't have successfully implemented functionality like createPost and updatePost without them.
*/
namespace app\controllers;

use app\models\Post;

class PostController {
    public function validatePost($inputData) {
        $errors = [];
        $title = $inputData['title'] ?? null;
        $content = $inputData['content'] ?? null;

        if ($title) {
            $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5, 'UTF-8', true);
            if (strlen($title) < 5) {
                $errors['titleShort'] = 'Title must be at least 5 characters long';
            }
        } else {
            $errors['requiredTitle'] = 'Title is required';
        }

        if ($content) {
            $content = htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8', true);
            if (strlen($content) < 10) {
                $errors['contentShort'] = 'Content must be at least 10 characters long';
            }
        } else {
            $errors['requiredContent'] = 'Content is required';
        }

        if (count($errors)) {
            http_response_code(400);
            echo json_encode($errors);
            exit();
        }

        return ['title' => $title, 'content' => $content];
    }

    public function getAllPosts() {
        $postModel = new Post();
        header("Content-Type: application/json");
        $posts = $postModel->getAllPosts();
        echo json_encode($posts);
        exit();
    }

    public function getPostByID($id) {
        if (!$id) {
            http_response_code(404);
            echo json_encode(['error' => 'Post not found']);
            exit();
        }

        $postModel = new Post();
        $post = $postModel->getPostById($id);

        if ($post) {
            header("Content-Type: application/json");
            echo json_encode($post);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Post not found']);
        }
        exit();
    }

    public function createPost() {
        try {
            $jsonData = file_get_contents('php://input');
            if (!$jsonData) {
                throw new \Exception('No input data provided');
            }

            $inputData = json_decode($jsonData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON input: ' . json_last_error_msg());
            }

            $postData = $this->validatePost($inputData);

            $postModel = new Post();
            $result = $postModel->createPost($postData);

            if ($result) {
                http_response_code(201);
                echo json_encode(['message' => 'Post created successfully']);
            } else {
                throw new \Exception('Failed to insert data into the database');
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }

    public function updatePost($id) {
        try {
            if (!$id) {
                throw new \Exception('Post ID is required');
            }

            parse_str(file_get_contents('php://input'), $_PUT);
            $inputData = [
                'id' => $id,
                'title' => $_PUT['title'] ?? null,
                'content' => $_PUT['content'] ?? null,
            ];

            $postData = $this->validatePost($inputData);

            $postModel = new Post();
            $result = $postModel->updatePost($postData);

            if ($result) {
                http_response_code(200);
                echo json_encode(['message' => 'Post updated successfully']);
            } else {
                throw new \Exception('Failed to update post');
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }

    public function deletePost($id) {
        try {
            if (!$id) {
                throw new \Exception('Post ID is required');
            }

            $postModel = new Post();
            $result = $postModel->deletePost(['id' => $id]);

            if ($result) {
                http_response_code(200);
                echo json_encode(['message' => 'Post deleted successfully']);
            } else {
                throw new \Exception('Failed to delete post');
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }

    public function postsView() {
        include '../public/assets/views/posts/posts-view.html';
        exit();
    }

    public function postsAddView() {
        include '../public/assets/views/posts/posts-add.html';
        exit();
    }

    public function postsUpdateView() {
        include '../public/assets/views/posts/posts-update.html';
        exit();
    }

    public function postsDeleteView() {
        include '../public/assets/views/posts/posts-delete.html';
        exit();
    }
}