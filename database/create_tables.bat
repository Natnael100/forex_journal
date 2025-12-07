@echo off
echo Creating Phase 6 and 7 database tables...
echo.

REM Check if sqlite3 is available
where sqlite3 >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo SQLite3 command line tool not found!
    echo.
    echo Please install SQLite3:
    echo 1. Download from: https://www.sqlite.org/download.html
    echo 2. Or use DB Browser for SQLite GUI: https://sqlitebrowser.org/dl/
    echo.
    pause
    exit /b 1
)

echo Running SQL script...
cd /d "%~dp0"
sqlite3 database.sqlite < create_phase6_7_tables.sql

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✓ Tables created successfully!
    echo.
    echo Created tables:
    echo - analyst_assignments
    echo - feedback  
    echo - notifications
    echo.
) else (
    echo.
    echo × Error creating tables!
    echo.
)

pause
