# FitnessIdeal Tracker

A daily habit and fitness task tracker built with Laravel — track tasks, build streaks, 
and visualize progress over time.

## Features

- **Task management** — create, edit, categorize, and prioritize daily tasks, with soft-delete 
  (trash/restore/force-delete/bulk-delete)
- **Daily tracking** — mark tasks done or skipped each day, with notes and actual duration logged
- **Streaks** — automatic current & longest streak tracking per task
- **Analytics dashboard** — visualize progress and consistency with Chart.js
- **Authentication** — registration, login, email verification, password reset (Laravel Breeze-style)

## Tech Stack

- **Laravel 12** (PHP 8.2)
- **Blade** + **Alpine.js** + **Tailwind CSS** — frontend
- **Chart.js** — analytics visualizations
- **Vite** — asset bundling
- **Docker** — containerized deployment (Dockerfile, Nginx, Supervisor), deployed on Render

## Setup

```bash
git clone <repo-url>
cd fitnessideal-tracker
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev       # in one terminal
php artisan serve # in another
```

## Screenshots

[Add 2-3 screenshots: dashboard, task list, analytics/streaks view]
