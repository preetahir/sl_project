@echo off
title LibrisCMS - Responsive Book Content Management System
color 0b

echo ===============================================================================
echo                LibrisCMS - Responsive Book Management System
echo                HTML5 ^| CSS3 ^| ES6 JS ^| PHP 8.1 ^| Laravel ^| XML
echo ===============================================================================
echo.

set PHP_EXE=
if exist "%~dp0..\php-portable\php.exe" (
    set "PHP_EXE=%~dp0..\php-portable\php.exe"
) else if exist "C:\Users\%USERNAME%\Downloads\php-portable\php.exe" (
    set "PHP_EXE=C:\Users\%USERNAME%\Downloads\php-portable\php.exe"
) else (
    where php >nul 2>nul
    if %ERRORLEVEL% equ 0 (
        set PHP_EXE=php
    )
)

if "%PHP_EXE%"=="" (
    echo [ERROR] PHP executable was not found.
    echo Please make sure php-portable exists in your Downloads folder.
    pause
    exit /b 1
)

echo [INFO] Using PHP Runtime: %PHP_EXE%
echo [INFO] Working Directory: %~dp0
echo [INFO] Starting Built-in Web Server at http://127.0.0.1:8000
echo.

cd /d "%~dp0"

:: Launch default browser after 1 second in background
start "" cmd /c "timeout /t 2 >nul & start http://127.0.0.1:8000"

:: Start PHP Server with public/index.php front-controller
"%PHP_EXE%" -S 127.0.0.1:8000 -t "%~dp0public" "%~dp0public\index.php"

pause

