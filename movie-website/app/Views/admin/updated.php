<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../Controllers/NewsController.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access";
    header('Location: /MovieHub/movie-website/public/login');
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method";
    header('Location: /MovieHub/movie-website/public/admin/manage-news');
    exit();
}

// Validate required fields
if (empty($_POST['id']) || empty($_POST['title']) || empty($_POST['content']) || empty($_POST['status'])) {
    $_SESSION['error'] = "All fields are required";
    header('Location: /MovieHub/movie-website/public/admin/edit-news?id=' . $_POST['id']);
    exit();
}

try {
    $newsController = new NewsController($pdo);
    
    // Update the news article
    $result = $newsController->update(
        $_POST['id'],
        [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'status' => $_POST['status']
        ],
        $_FILES['image'] ?? null
    );

    if ($result) {
        $_SESSION['success'] = "News article updated successfully";
        header('Location: /MovieHub/movie-website/public/admin/manage-news');
    } else {
        $_SESSION['error'] = "Failed to update news article";
        header('Location: /MovieHub/movie-website/public/admin/edit-news?id=' . $_POST['id']);
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: /MovieHub/movie-website/public/admin/edit-news?id=' . $_POST['id']);
}
exit();