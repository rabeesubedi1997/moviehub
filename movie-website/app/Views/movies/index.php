<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require __DIR__ . '/../app/Controllers/MovieController.php';

// Fetch latest movies
try {
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC LIMIT 6");
    $latestMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latestMovies = [];
}

$controller = new MovieController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['url']) && $_GET['url'] === 'store') {
        $controller->store($_POST);
    }
} else {
    if (isset($_GET['url']) && $_GET['url'] === 'show') {
        $controller->show($_GET['id']);
    } elseif (isset($_GET['url']) && $_GET['url'] === 'create') {
        $controller->create();
    } elseif (isset($_GET['url']) && $_GET['url'] === 'edit') {
        $controller->edit($_GET['id']);
    } else {
        $controller->index();
    }
}
