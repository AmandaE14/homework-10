<?php

namespace app\models;

use app\models\Model;

class Post extends Model {
    protected $table = 'posts';

    public function getAllPostsByTitle($title) {
        $query = "SELECT * FROM {$this->table} WHERE title LIKE :title";
        return $this->fetchAllWithParams($query, ['title' => '%' . $title . '%']);
    }

    public function getAllPosts() {
        $query = "SELECT * FROM {$this->table}";
        return $this->fetchAll($query);
    }

    public function getPostById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->fetchOneWithParams($query, ['id' => $id]);
    }

    public function createPost($data) {
        $query = "INSERT INTO {$this->table} (title, content) VALUES (:title, :content)";
        return $this->executeWithParams($query, $data);
    }    

    public function savePost($title, $content) {
        $query = "INSERT INTO {$this->table} (title, content) VALUES (:title, :content)";
        return $this->executeWithParams($query, ['title' => $title, 'content' => $content]);
    }

    public function updatePost($data) {
        $query = "UPDATE {$this->table} SET title = :title, content = :content WHERE id = :id";
        return $this->executeWithParams($query, $data);
    }

    public function deletePost($data) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->executeWithParams($query, $data);
    }
}