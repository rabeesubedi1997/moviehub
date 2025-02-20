<?php

class NewsController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function index()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM news ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching news: " . $e->getMessage());
        }
    }

    public function store($data, $file = null)
    {
        try {
            $image_filename = null;
            if ($file && isset($file['name']) && !empty($file['name'])) {
                $image_filename = $this->handleImageUpload($file);
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO news (title, content, image, status, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");

            return $stmt->execute([
                $data['title'],
                $data['content'],
                $image_filename,
                $data['status']
            ]);
        } catch (Exception $e) {
            throw new Exception("Error creating news: " . $e->getMessage());
        }
    }

    public function update($id, $data, $file = null)
    {
        try {
            $params = [
                $data['title'],
                $data['content'],
                $data['status']
            ];

            $image_sql = "";
            if ($file && isset($file['name']) && !empty($file['name'])) {
                $image_filename = $this->handleImageUpload($file);
                $image_sql = ", image = ?";
                $params[] = $image_filename;
            }

            $params[] = $id;

            $stmt = $this->pdo->prepare("
                UPDATE news 
                SET title = ?, content = ?, status = ? {$image_sql}
                WHERE id = ?
            ");

            return $stmt->execute($params);
        } catch (Exception $e) {
            throw new Exception("Error updating news: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM news WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error deleting news: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM news WHERE id = ?");
            $stmt->execute([$id]);
            $news = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$news) {
                throw new Exception("News article not found");
            }

            return $news;
        } catch (PDOException $e) {
            throw new Exception("Error fetching news article: " . $e->getMessage());
        }
    }

    private function ensureUploadDirectory()
    {
        $target_dir = dirname(dirname(dirname(__FILE__))) . "/public/assets/images/news/";

        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                throw new Exception("Failed to create upload directory");
            }
            chmod($target_dir, 0777);
        }

        if (!is_writable($target_dir)) {
            throw new Exception("Upload directory is not writable");
        }

        return $target_dir;
    }

    private function handleImageUpload($file)
    {
        $target_dir = $this->ensureUploadDirectory();

        // Check if file is actually uploaded
        if (!isset($file) || !isset($file['tmp_name']) || empty($file['tmp_name'])) {
            throw new Exception("No file uploaded");
        }

        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;

        // Validate image
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File is not an image.");
        }

        // Check file size (5MB max)
        if ($file["size"] > 5000000) {
            throw new Exception("File is too large. Maximum size is 5MB.");
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Upload file
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Failed to upload image.");
        }

        return $new_filename;
    }

    private function logError($message)
    {
        $log_file = dirname(dirname(dirname(__FILE__))) . "/logs/upload_errors.log";
        $timestamp = date('Y-m-d H:i:s');
        error_log("[$timestamp] $message\n", 3, $log_file);
    }
}
