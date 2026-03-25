# Deploying OJT Time Tracker to Render

This guide will help you deploy your Laravel application to **Render.com** for free (or paid) hosting.

## Prerequisites
1.  A **GitHub** account.
2.  A **Render.com** account.
3.  Your **Supabase** database credentials.

---

## Step 1: Push Code to GitHub

Since you are currently working locally, you need to push your code to a GitHub repository.

1.  **Initialize Git** (if not done):
    ```powershell
    git init
    git add .
    git commit -m "Initial commit"
    ```
2.  **Create a Repository on GitHub**:
    *   Go to GitHub > New Repository.
    *   Name it `ojt-time-tracker`.
    *   **Do NOT** initialize with README/gitignore (you already have them).
3.  **Push your code**:
    ```powershell
    git remote add origin https://github.com/YOUR_USERNAME/ojt-time-tracker.git
    git branch -M main
    git push -u origin main
    ```

---

## Step 2: Configure Render

1.  Log in to your [Render Dashboard](https://dashboard.render.com/).
2.  Click **New +** and select **Web Service**.
3.  Connect your GitHub account and select the `ojt-time-tracker` repository.

### Service Settings
*   **Name**: `ojt-time-tracker` (or any unique name)
*   **Region**: Closest to you (e.g., Singapore/Oregon)
*   **Branch**: `main`
*   **Root Directory**: Leave blank (default is `.`)
*   **Runtime**: **PHP**
*   **Build Command**:
    ```bash
    composer install --no-dev --optimize-autoloader
    ```
    *(Note: We skip `npm install` because you are using CDN for Tailwind/JS)*
*   **Start Command**:
    ```bash
    php artisan config:cache && php artisan route:cache && php artisan view:cache && heroku-php-apache2 public/
    ```
    *(This commands optimizes the app and starts the Apache server serving the public folder)*

---

## Step 3: Environment Variables

Click on **Advanced** or **Environment Variables** and add the following keys. Copy values from your local `.env` file, but use the **Production** values.

| Key | Value |
| :--- | :--- |
| `APP_NAME` | `OJT Tracker` |
| `APP_ENV` | `production` |
| `APP_KEY` | *(Run `php artisan key:generate --show` locally and copy the value)* |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://your-app-name.onrender.com` (You will get this after creation) |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | `aws-1-ap-southeast-2.pooler.supabase.com` |
| `DB_PORT` | `6543` (Use Transaction Mode port for production stability) |
| `DB_DATABASE` | `postgres` |
| `DB_USERNAME` | `postgres.uxczcgqpucwzkyxqyfkw` |
| `DB_PASSWORD` | *(Your Supabase Password)* |
| `SESSION_DRIVER` | `cookie` or `file` (Cookie is easiest for simple apps) |

> **Important**: For Supabase on production, ensure you use the **Connection Pooling** port (usually **6543**) and not the Session port (5432) if possible, to avoid connection limits on the free tier.

---

## Step 4: Deploy

1.  Click **Create Web Service**.
2.  Render will start building your app.
3.  Watch the logs. It will install PHP dependencies and start the server.
4.  Once it says "Live", click the URL at the top to visit your site.

## Troubleshooting

*   **500 Server Error**: Check the **Logs** tab in Render. It usually means a missing environment variable (like `APP_KEY` or DB credentials).
*   **Database Errors**: Ensure your Supabase database allows connections from anywhere (`0.0.0.0/0`) or just ensure the credentials are correct.
*   **Migration Issues**: Since you imported SQL manually, you don't need to run migrations. If you want to run them in the future, add `&& php artisan migrate --force` to your Start Command, but be careful of conflicts.
