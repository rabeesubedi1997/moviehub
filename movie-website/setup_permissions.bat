@echo off
echo Creating directory structure...

REM Create main directories
mkdir "app\Controllers" 2>nul
mkdir "app\Models" 2>nul
mkdir "app\Views\admin" 2>nul
mkdir "app\Views\layouts" 2>nul
mkdir "app\Views\users" 2>nul
mkdir "config" 2>nul
mkdir "public\assets\images\news" 2>nul
mkdir "public\assets\images\movies" 2>nul
mkdir "public\assets\css" 2>nul
mkdir "public\assets\js" 2>nul

REM Set permissions
icacls "public\assets\images" /grant Everyone:(OI)(CI)F /T

echo Directory structure created successfully!
pause