# Laravel Project Setup Guide

## Prerequisites

To run this project, you need Docker
- [Download and install Docker](https://www.docker.com/) for your operating system.
- Ensure Docker is running.

---

## Installation Steps

### 1. Clone the Repository
Clone this repository to your local machine:
```bash
    git clone git@gitlab.com:rb-code-challenges/sloboda-alex.git
    cd sloboda-alex
```

### 2. Install Dependencies
Install dependencies with Laravel Sail PHP development server:

```bash
    docker run --rm \
        -v $(pwd):/opt \
        -w /opt \
        laravelsail/php81-composer:latest \
        composer install
```

### 3. Configure Environment
- Create a `.env` file by copying the example configuration:
```bash
   cp .env.example .env
```
- Generate the application key:
```bash
   docker run --rm \
       -v $(pwd):/opt \
       -w /opt \
       laravelsail/php81-composer:latest \
       php artisan key:generate
```

### 4. Start the Application
- Install Laravel Sail as a development dependency:
```bash
   docker run --rm \
       -v $(pwd):/opt \
       -w /opt \
       laravelsail/php81-composer:latest \
       composer require laravel/sail --dev
```
- (OPTIONAL) You can add sail alias to use `sail` instead of `./vendor/bin/sail`:
```bash
   alias sail="./vendor/bin/sail"
```
- Start the Docker containers:
```bash
    sail up -d
```

### 5. Access the Application
Once the containers are running, open your browser and navigate to:
```
    http://localhost
```

---

## Common Commands

### Run Artisan Commands
```bash
  sail artisan <command>
```

### Run Tests
```bash
  sail test
```

### Stop the Environment
```bash
  sail down
```
