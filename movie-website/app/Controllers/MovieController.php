<?php
// filepath: /c:/xampp/htdocs/MovieHub/movie-website/app/Controllers/MovieController.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Movie.php';

class MovieController
{
    private $movieModel;

    public function __construct($pdo)
    {
        session_start();
        $this->movieModel = new Movie($pdo);
    }

    public function index()
    {
        $movies = $this->movieModel->getAllMovies();
        require __DIR__ . '/../Views/movies/index.php';
    }

    public function show($id)
    {
        $movie = $this->movieModel->getMovieById($id);
        require '../Views/movies/details.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/admin/add_movie.php';
    }

    public function store($data)
    {
        if ($this->movieModel->addMovie($data['title'], $data['description'], $data['release_date'], $data['genre'], $data['director'])) {
            $_SESSION['success'] = "Movie added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add movie!";
        }
        header('Location: /MovieHub/movie-website/public/movies');
        exit();
    }

    public function edit($id)
    {
        $movie = $this->movieModel->getMovieById($id);
        require '../Views/admin/edit_movie.php';
    }

    public function update($id, $data)
    {
        $this->movieModel->updateMovie($id, $data['title'], $data['description'], $data['release_date'], $data['genre'], $data['director']);
        header('Location: /movies');
    }

    public function delete($id)
    {
        $this->movieModel->deleteMovie($id);
        header('Location: /movies');
    }
}
