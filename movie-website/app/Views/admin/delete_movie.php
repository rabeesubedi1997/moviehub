<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Movie</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body>
    <div class="container">
        <h1>Delete Movie</h1>
        <p>Are you sure you want to delete the movie titled "<strong><?php echo htmlspecialchars($movie->title); ?></strong>"?</p>
        <form action="/admin/delete_movie/<?php echo $movie->id; ?>" method="POST">
            <button type="submit">Yes, Delete</button>
            <a href="/admin/dashboard">Cancel</a>
        </form>
    </div>
</body>

</html>