# (Inertia Issue - Version 1.0) Debugging inertia-axios overlap

A demo application for the purpose of demonstrating the error.

## Installation

Clone the repo locally:

```sh
git clone https://github.com/kiwiscode/inertia-axios-overlap-error.git
cd inertia-1.0
```

Install PHP dependencies:

```sh
composer install
```

Install NPM dependencies:

```sh
npm ci
```

Build assets:

```sh
npm run dev
```

Setup configuration:

```sh
cp .env.example .env
```

Generate application key:

```sh
php artisan key:generate
```
Generate ziggy:

```sh
php artisan ziggy:generate
```

Create an SQLite database. You can also use another database (MySQL, Postgres), simply update your configuration accordingly.

```sh
touch database/database.sqlite
```

Run database migrations:

```sh
php artisan migrate
```

Run database seeder:

```sh
php artisan db:seed
```

Run the dev server (the output will give the address):

```sh
php artisan serve
```
