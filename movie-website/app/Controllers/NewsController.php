<?php

class NewsController
{
    private $pdo;
    private $uploadPath;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->uploadPath = dirname(dirname(dirname(__FILE__))) . '/public/assets/images/news/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    private function createSlug($title) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
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
            $title = $data['title'];
            $content = $data['content'];
            $status = $data['status'] ?? 'draft';
            $author_id = $_SESSION['user_id'];
            $slug = $this->createSlug($title);

            // Handle image upload
            $image = null;
            if ($file && $file['size'] > 0) {
                $targetDir = $this->uploadPath;
                $fileName = time() . '_' . basename($file['name']);
                $targetPath = $targetDir . $fileName;

                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $image = $fileName;
                }
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO news (title, slug, content, image, status, author_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");

            return $stmt->execute([
                $title,
                $slug,
                $content,
                $image,
                $status,
                $author_id
            ]);
        } catch (Exception $e) {
            error_log("Error in NewsController::store: " . $e->getMessage());
            throw $e;
        }
    }

    public function create($data, $file = null) {
        try {
            $image = null;
            if ($file && $file['size'] > 0) {
                $image = $this->uploadImage($file);
            }

            $slug = $this->createSlug($data['title']);
            
            $stmt = $this->pdo->prepare("
                INSERT INTO news (title, content, image, status, author_id, slug)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            return $stmt->execute([
                $data['title'],
                $data['content'],
                $image,
                $data['status'] ?? 'draft',
                $_SESSION['user_id'],
                $slug
            ]);
        } catch (PDOException $e) {
            throw new Exception("Failed to create news article: " . $e->getMessage());
        }
    }

    public function update($id, $data, $file = null) {
        try {
            $this->pdo->beginTransaction();

            $updateData = [
                'title' => $data['title'],
                'content' => $data['content'],
                'status' => $data['status'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle image upload if new image is provided
            if ($file && $file['size'] > 0) {
                $newImage = $this->uploadImage($file);
                if ($newImage) {
                    $updateData['image'] = $newImage;
                    
                    // Delete old image
                    $oldImage = $this->getById($id)['image'];
                    if ($oldImage) {
                        $imagePath = __DIR__ . '/../../../public/assets/images/news/' . $oldImage;
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                }
            }

            // Build update query
            $setClauses = [];
            $params = [];
            foreach ($updateData as $key => $value) {
                $setClauses[] = "$key = ?";
                $params[] = $value;
            }
            $params[] = $id;

            $sql = "UPDATE news SET " . implode(', ', $setClauses) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);

            $this->pdo->commit();
            return $result;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error updating news: " . $e->getMessage());
            throw new Exception("Failed to update news article: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            // Get the article first to get the image filename
            $article = $this->getById($id);
            if ($article && $article['image']) {
                // Delete the image file
                $imagePath = __DIR__ . '/../../../public/assets/images/news/' . $article['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $stmt = $this->pdo->prepare("DELETE FROM news WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Failed to delete news article: " . $e->getMessage());
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT n.*, u.username as author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            throw new Exception("Failed to fetch news article");
        }
    }

    public function getAllPaginated($page = 1, $limit = 9) {
        try {
            $offset = ($page - 1) * $limit;
            
            // Get total count
            $countStmt = $this->pdo->query("SELECT COUNT(*) FROM news WHERE status = 'public'");
            $total = $countStmt->fetchColumn();
            
            // Get news items
            $stmt = $this->pdo->prepare("
                SELECT 
                    n.id,
                    n.title,
                    n.content,
                    n.image,
                    n.created_at,
                    COALESCE(u.username, 'Anonymous') as author_name
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.status = 'public' 
                ORDER BY n.created_at DESC 
                LIMIT ? OFFSET ?
            ");
            
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'news' => $news,
                'total' => $total,
                'pages' => ceil($total / $limit),
                'current_page' => $page
            ];
        } catch (PDOException $e) {
            error_log("Error in getAllPaginated: " . $e->getMessage());
            return [
                'news' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => $page
            ];
        }
    }

    public function getAllPaginatedAdmin($page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;
            
            // Get total count
            $countStmt = $this->pdo->query("SELECT COUNT(*) FROM news");
            $total = $countStmt->fetchColumn();
            
            // Get news items with author information
            $stmt = $this->pdo->prepare("
                SELECT n.*, u.username as author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                ORDER BY n.created_at DESC 
                LIMIT ? OFFSET ?
            ");
            
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return [
                'news' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $total,
                'pages' => ceil($total / $limit),
                'current_page' => $page
            ];
        } catch (PDOException $e) {
            error_log("Error in getAllPaginatedAdmin: " . $e->getMessage());
            throw new Exception("Failed to fetch news articles");
        }
    }

    public function getTotalPublicNews() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM news WHERE status = 'published'");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error in getTotalPublicNews: " . $e->getMessage());
            return 0;
        }
    }

    private function handleImageUpload($file)
    {
        try {
            error_log("Starting image upload...");
            error_log("Upload path: " . $this->uploadPath);

            if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
                error_log("No file uploaded");
                return null;
            }

            // Log file details
            error_log("File details: " . print_r($file, true));

            // Check directory permissions
            if (!is_dir($this->uploadPath)) {
                error_log("Upload directory does not exist: " . $this->uploadPath);
                mkdir($this->uploadPath, 0777, true);
            }

            if (!is_writable($this->uploadPath)) {
                error_log("Upload directory is not writable: " . $this->uploadPath);
                throw new Exception('Upload directory is not writable');
            }

            // Validate image
            $check = getimagesize($file['tmp_name']);
            if ($check === false) {
                throw new Exception('File is not an image.');
            }

            // Generate unique filename
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newFilename = uniqid() . '.' . $extension;
            $targetFile = $this->uploadPath . $newFilename;

            // Check file size (5MB)
            if ($file['size'] > 5000000) {
                throw new Exception('File is too large. Maximum size is 5MB.');
            }

            // Allow certain file formats
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed.');
            }

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
                throw new Exception('Failed to move uploaded file.');
            }

            error_log("Image uploaded successfully: " . $newFilename);
            return $newFilename;
        } catch (Exception $e) {
            error_log("Image upload error: " . $e->getMessage());
            throw new Exception('Image upload failed: ' . $e->getMessage());
        }
    }

    public function getLatestNews($limit = 3) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT n.*, u.username as author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.status = 'public' 
                ORDER BY n.created_at DESC 
                LIMIT ?
            ");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getLatestNews: " . $e->getMessage());
            return [];
        }
    }

    public function getFeaturedNews($limit = 3) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT n.*, u.username as author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.status = 'public' 
                AND n.image IS NOT NULL 
                ORDER BY n.created_at DESC, n.views DESC 
                LIMIT ?
            ");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getFeaturedNews: " . $e->getMessage());
            return [];
        }
    }

    public function getBySlug($slug) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT n.*, u.username as author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.slug = ? AND n.status = 'published'
            ");
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getBySlug: " . $e->getMessage());
            return null;
        }
    }

    public function incrementViews($id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE news 
                SET views = views + 1 
                WHERE id = ?
            ");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in incrementViews: " . $e->getMessage());
            return false;
        }
    }

    public function getRelatedNews($currentNewsId, $limit = 3) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT n.*, u.username as author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.status = 'public' 
                AND n.id != ? 
                ORDER BY n.created_at DESC 
                LIMIT ?
            ");
            $stmt->bindValue(1, $currentNewsId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getRelatedNews: " . $e->getMessage());
            return [];
        }
    }

    public function searchNews($query, $page = 1, $limit = 9) {
        try {
            $offset = ($page - 1) * $limit;
            $searchTerm = "%{$query}%";
            
            $stmt = $this->pdo->prepare("
                SELECT n.*, u.username as author_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.status = 'public' 
                AND (n.title LIKE ? OR n.content LIKE ?)
                ORDER BY n.created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$searchTerm, $searchTerm, $limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in searchNews: " . $e->getMessage());
            return [];
        }
    }

    protected function uploadImage($file) {
        try {
            if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
                return null;
            }

            // Validate image
            $check = getimagesize($file['tmp_name']);
            if ($check === false) {
                throw new Exception('File is not an image.');
            }

            // Check file size (5MB)
            if ($file['size'] > 5000000) {
                throw new Exception('File is too large. Maximum size is 5MB.');
            }

            // Generate unique filename
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed.');
            }

            $newFilename = uniqid() . '.' . $extension;
            $uploadPath = __DIR__ . '/../../../public/assets/images/news/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if (!move_uploaded_file($file['tmp_name'], $uploadPath . $newFilename)) {
                throw new Exception('Failed to move uploaded file.');
            }

            return $newFilename;
        } catch (Exception $e) {
            error_log("Error uploading image: " . $e->getMessage());
            throw $e;
        }
    }
}
