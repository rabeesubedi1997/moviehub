# Movie Website

## Overview
This project is a movie website built using PHP following the MVC (Model-View-Controller) architecture. It allows users to browse movies, view details, and manage their accounts. Admins can add, edit, and delete movies, as well as view statistics on the admin dashboard.

## Project Structure
```
movie-website
├── app
│   ├── Controllers
│   │   ├── AdminController.php
│   │   ├── MovieController.php
│   │   └── UserController.php
│   ├── Models
│   │   ├── Movie.php
│   │   └── User.php
│   ├── Views
│   │   ├── admin
│   │   │   ├── add_movie.php
│   │   │   ├── edit_movie.php
│   │   │   ├── delete_movie.php
│   │   │   └── dashboard.php
│   │   ├── movies
│   │   │   ├── index.php
│   │   │   ├── details.php
│   │   │   └── slider.php
│   │   └── users
│   │       ├── login.php
│   │       └── register.php
├── config
│   └── database.php
├── public
│   ├── css
│   │   └── styles.css
│   ├── js
│   │   └── scripts.js
│   └── index.php
├── .htaccess
├── composer.json
└── README.md
```

## Features
- **User Management**: Users can register, log in, and manage their profiles.
- **Movie Management**: Admins can add, edit, and delete movies.
- **Movie Browsing**: Users can browse a list of movies and view detailed information.
- **Responsive Design**: The website is designed to be responsive and user-friendly.

## Installation
1. Clone the repository:
   ```
   git clone https://github.com/yourusername/movie-website.git
   ```
2. Navigate to the project directory:
   ```
   cd movie-website
   ```
3. Install dependencies using Composer:
   ```
   composer install
   ```
4. Configure the database settings in `config/database.php`.
5. Set up the web server to point to the `public` directory.

## Usage
- Access the website through your web browser.
- Use the admin panel to manage movies and view statistics.
- Users can register and log in to access additional features.

## Contributing
Contributions are welcome! Please open an issue or submit a pull request for any enhancements or bug fixes.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.