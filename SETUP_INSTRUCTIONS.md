# OJT Time Tracker System - Setup Instructions

## Prerequisites
- PHP 8.2+
- Composer
- Supabase Account (or local PostgreSQL/SQLite)

## 1. Environment Setup

Copy the example environment file:
```bash
cp .env.example .env
```

Configure your `.env` file with your Supabase credentials. You can use the provided `.env.example.supabase` as a reference.

```env
DB_CONNECTION=pgsql
DB_HOST=aws-0-us-west-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.your-project-ref
DB_PASSWORD=your-password
```

Ensure you generate an app key:
```bash
php artisan key:generate
```

## 2. Database Migration

Run the migrations to create the `users` and `time_logs` tables in your Supabase database:

```bash
php artisan migrate
```

## 3. Running the Application

Serve the application locally:

```bash
php artisan serve
```

Access the app at `http://localhost:8000`.

## 4. Usage

1.  **Register**: Go to `/register` and create a new account.
2.  **Dashboard**: Once logged in, you will be redirected to the dashboard.
3.  **Start Timer**: Click "Start Timer" to begin tracking time.
4.  **Stop Timer**: Click "Stop Timer" to end the session.
5.  **Logs**: View your history in the "Logs" tab.

## Features Implemented
- **User Authentication**: Login, Register, Logout.
- **Time Tracking**: Start/Stop timer with real-time display.
- **Dashboard**: Today's summary and recent logs.
- **History**: Full paginated list of past logs.
- **Supabase Integration**: Configured via `.env`.
