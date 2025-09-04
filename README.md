# Laravel Filament Image Gallery

A Laravel-based image gallery application using Filament for the admin panel. Features include image search, pagination, favorites, download, and responsive grid layout.

---

## Table of Contents

- [Features](#features)  
- [Requirements](#requirements)  
- [Installation](#installation)  
- [Configuration](#configuration)  
- [Running the Project](#running-the-project)  
- [Usage](#usage)  
- [Folder Structure](#folder-structure)  
- [License](#license)  

---

## Features

- Responsive image grid with equal-sized cards  
- Search bar to filter images  
- Pagination with Previous/Next buttons  
- Favorite and download buttons  
- Modal to preview full-size images  
- Filament admin panel integration  

---

## Requirements

- PHP >= 8.1  
- Composer  
- Node.js >= 18  
- NPM / Yarn  
- MySQL or any other supported database  

---

## Installation

1. **Clone the repository**  

```bash
git clone https://github.com/your-username/laravel-image-gallery.git
cd IMAGE-GALLERY-APP
composer install
npm install

php artisan key:generate
php artisan migrate
composer run dev
http://127.0.0.1:8000
