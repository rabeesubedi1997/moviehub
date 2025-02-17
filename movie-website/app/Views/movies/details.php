<?php
// This file displays the details of a selected movie.

require_once '../../Models/Movie.php';
require '../layouts/header.php';

if (isset($_GET['id'])) {
    $movieId = $_GET['id'];
    $movieModel = new Movie();
    $movie = $movieModel->findById($movieId);

    if ($movie) {
?>
        <h1><?= htmlspecialchars($movie->title) ?></h1>
        <p><?= htmlspecialchars($movie->description) ?></p>
        <p><strong>Release Date:</strong> <?= htmlspecialchars($movie->release_date) ?></p>
        <p><strong>Genre:</strong> <?= htmlspecialchars($movie->genre) ?></p>
        <p><strong>Director:</strong> <?= htmlspecialchars($movie->director) ?></p>
        <a href="index.php">Back to Movies</a>
<?php
    } else {
        echo "<p>Movie not found.</p>";
    }
} else {
    echo "<p>No movie selected.</p>";
}

require '../layouts/footer.php';
?>