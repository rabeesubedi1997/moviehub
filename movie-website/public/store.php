<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../app/Models/Movie.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $release_date = $_POST['release_date'];
    $genre = $_POST['genre'];
    $director = $_POST['director'];

    $movieModel = new Movie($pdo);
    $movieModel->addMovie($title, $description, $release_date, $genre, $director);

    header('Location: /movies');
    exit();
} else {
    header('Location: /movies/create');
    exit();
}
