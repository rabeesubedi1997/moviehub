<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MovieHub/movie-website/app/Views/users/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $director = $_POST['director'];
        $release_date = $_POST['release_date'];
        $genre = $_POST['genre'];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;

        $updateData = [
            'title' => $title,
            'description' => $description,
            'director' => $director,
            'release_date' => $release_date,
            'genre' => $genre,
            'is_featured' => $is_featured
        ];

        // Handle image upload if provided
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/../../../../public/assets/images/movies/';
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $updateData['image'] = $fileName;
            }
        }

        // Build and execute update query
        $sql = "UPDATE movies SET ";
        $params = [];
        foreach ($updateData as $key => $value) {
            $sql .= "$key = ?, ";
            $params[] = $value;
        }
        $sql = rtrim($sql, ", ") . " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = "Movie updated successfully";
        header('Location: /MovieHub/movie-website/app/Views/admin/dashboard.php');
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating movie: " . $e->getMessage();
        header("Location: /MovieHub/movie-website/app/Views/admin/edit_movie.php?id=" . $id);
        exit();
    }
} else {
    // If not POST request, redirect to dashboard
    header('Location: /MovieHub/movie-website/app/Views/admin/dashboard.php');
    exit();
}
