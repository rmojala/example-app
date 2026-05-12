# Example App

This an example Laravel app. To keep things simple, the database is SQLite and the user interface is based on Laravel's Vue starter kit.

## Cloning and running

```bash
git clone https://github.com/rmojala/example-app.git
cd example-app
composer install
npm ci && npm run build
cp .env.example .env
php artisan key:generate && php artisan migrate --seed
composer run dev
```

Then visit [http://localhost:8000](http://localhost:8000). The seeder script created an admin user (`admin@example.com`) and two regular users (`user1@example.com`, `user2@example.com`). The password is `password` for all users. The script also created some example data to play with.

I noticed that the Vite dev server tends to crash quite often. If the app becomes unresponsive, check that the server is still up.

## Features

- Users can create/view/edit/delete notes.
- Users can grant/revoke other users read access to individual notes.
- Admins can manage users by enabling/disabling their ability to create notes.

## Comments

- The database tables use auto-incrementing ids because this is the default in Laravel. In real apps I prefer UUIDv7 ids by default.
- I didn't write documentation or type specifications. In real apps I document components and API boundaries.
- I wrote just a few example tests. In real apps I often like to do test-driven development.
