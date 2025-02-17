<?php

class Movie
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllMovies()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM movies ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMovieById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM movies WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addMovie($title, $description, $release_date, $genre, $director)
    {
        $sql = "INSERT INTO movies (title, description, release_date, genre, director) 
                VALUES (:title, :description, :release_date, :genre, :director)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':release_date' => $release_date,
            ':genre' => $genre,
            ':director' => $director
        ]);
    }

    public function updateMovie($id, $title, $description, $release_date, $genre, $director)
    {
        $stmt = $this->pdo->prepare("UPDATE movies SET title = :title, description = :description, release_date = :release_date, genre = :genre, director = :director WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':release_date', $release_date);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':director', $director);
        return $stmt->execute();
    }

    public function deleteMovie($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM movies WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
