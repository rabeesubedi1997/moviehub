<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MovieHub/movie-website/app/Views/users/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $release_date = $_POST['release_date'];
    $genre = $_POST['genre'];
    $director = $_POST['director'];

    try {
        // Update movie in database
        $stmt = $pdo->prepare("
            UPDATE movies 
            SET title = ?, 
                description = ?, 
                release_date = ?, 
                genre = ?, 
                director = ? 
            WHERE id = ?
        ");

        $stmt->execute([
            $title,
            $description,
            $release_date,
            $genre,
            $director,
            $id
        ]);

        $_SESSION['success'] = "Movie updated successfully!";
        header('Location: dashboard.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating movie: " . $e->getMessage();
        header('Location: edit_movie.php?id=' . $id);
        exit();
    }
} else {
    // If not POST request, redirect to dashboard
    header('Location: dashboard.php');
    exit();
}
