<?php
// filepath: /c:/xampp/htdocs/MovieHub/movie-website/app/Controllers/MovieController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Movie.php';

class MovieController
{
    private $pdo;

    public function __construct($pdo)
    {
        //session_start();
        $this->pdo = $pdo;
    }

    public function index()
    {
        $movies = $this->getAllMovies();
        require __DIR__ . '/../Views/movies/index.php';
    }

    public function show($id)
    {
        $movie = $this->getMovieById($id);
        require '../Views/movies/details.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/admin/add_movie.php';
    }

    public function store($data, $file)
    {
        try {
            $result = $this->createMovie($data, $file);
            if ($result) {
                $_SESSION['success'] = "Movie added successfully!";
                return true;
            } else {
                throw new Exception("Failed to add movie!");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            return false;
        }
    }

    public function edit($id)
    {
        try {
            $movie = $this->getMovieById($id);
            if (!$movie) {
                throw new Exception("Movie not found");
            }
            return $movie;
        } catch (Exception $e) {
            throw new Exception("Error fetching movie: " . $e->getMessage());
        }
    }

    public function update($id, $data, $file = null)
    {
        try {
            $updateData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'release_date' => $data['release_date'],
                'genre' => $data['genre'],
                'director' => $data['director'],
                'is_featured' => isset($data['is_featured']) ? 1 : 0,
                'in_slider' => isset($data['in_slider']) ? 1 : 0
            ];

            // Handle new image upload if provided
            if ($file && $file['size'] > 0) {
                $image_filename = $this->handleImageUpload($file);
                $updateData['image'] = $image_filename;

                // Delete old image
                $oldMovie = $this->getMovieById($id);
                if ($oldMovie && $oldMovie['image']) {
                    $oldImagePath = PUBLIC_PATH . '/assets/images/movies/' . $oldMovie['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }

            $columns = array_keys($updateData);
            $placeholders = array_map(function ($col) {
                return "$col = ?";
            }, $columns);
            $sql = "UPDATE movies SET " . implode(', ', $placeholders) . " WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([...array_values($updateData), $id]);

            return true;
        } catch (Exception $e) {
            throw new Exception("Error updating movie: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            // First get the movie to delete its image
            $stmt = $this->pdo->prepare("SELECT image FROM movies WHERE id = ?");
            $stmt->execute([$id]);
            $movie = $stmt->fetch();

            if ($movie && $movie['image']) {
                $imagePath = PUBLIC_PATH . '/assets/images/movies/' . $movie['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Delete the movie record
            $stmt = $this->pdo->prepare("DELETE FROM movies WHERE id = ?");
            $stmt->execute([$id]);

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error deleting movie: " . $e->getMessage());
        }
    }

    public function getAllMovies()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM movies ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching movies: " . $e->getMessage());
        }
    }

    public function getMovieById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM movies WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching movie: " . $e->getMessage());
        }
    }

    private function handleImageUpload($file)
    {
        $target_dir = __DIR__ . "/../../public/assets/images/movies/";
        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;

        // Check if image file is actual image
        if (!getimagesize($file["tmp_name"])) {
            throw new Exception("File is not an image.");
        }

        // Check file size (5MB max)
        if ($file["size"] > 5000000) {
            throw new Exception("File is too large.");
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Failed to upload image.");
        }

        return $new_filename;
    }

    public function createMovie($data, $file)
    {
        try {
            $image_filename = $this->handleImageUpload($file);

            $stmt = $this->pdo->prepare("
                INSERT INTO movies (
                    title, description, release_date, genre, 
                    director, image, is_featured, in_slider, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            return $stmt->execute([
                $data['title'],
                $data['description'],
                $data['release_date'],
                $data['genre'],
                $data['director'],
                $image_filename,
                isset($data['is_featured']) ? 1 : 0,
                isset($data['in_slider']) ? 1 : 0
            ]);
        } catch (Exception $e) {
            throw new Exception("Error creating movie: " . $e->getMessage());
        }
    }

    public function updateMovie($id, $data)
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE movies 
                SET title = ?, description = ?, release_date = ?, 
                    genre = ?, director = ?, image = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['title'],
                $data['description'],
                $data['release_date'],
                $data['genre'],
                $data['director'],
                $data['image'],
                $id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error updating movie: " . $e->getMessage());
        }
    }

    public function deleteMovie($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM movies WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error deleting movie: " . $e->getMessage());
        }
    }
}
