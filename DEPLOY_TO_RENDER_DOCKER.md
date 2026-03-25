# Deploying OJT Time Tracker to Render (Docker Method)

This guide explains how to deploy your Laravel application to **Render** using **Docker**.

## Prerequisites
1.  **GitHub Account**: Your code must be pushed to a GitHub repository.
2.  **Render Account**: Account on [render.com](https://render.com).
3.  **Supabase Database**: Your existing PostgreSQL database.

---

## Step 1: Push Code to GitHub

Ensure your project (including the new `Dockerfile`) is pushed to GitHub.

```powershell
git add .
git commit -m "Add Dockerfile for deployment"
git push origin main
```

---

## Step 2: Create a Web Service on Render

1.  Log in to the [Render Dashboard](https://dashboard.render.com/).
2.  Click **New +** and select **Web Service**.
3.  Connect your GitHub account and select your `ojt-time-tracker` repository.

### Service Settings

*   **Name**: `ojt-time-tracker`
*   **Region**: Singapore (or closest to you)
*   **Branch**: `main`
*   **Runtime**: **Docker** (Important: Select Docker, not PHP)
*   **Instance Type**: Free

Render will automatically detect the `Dockerfile` in your repository root.

---

## Step 3: Environment Variables

Click on **Advanced** or **Environment Variables** and add the following. 
**Copy these values from your local `.env` file.**

| Key | Value |
| :--- | :--- |
| `APP_NAME` | `OJT Time Tracker` |
| `APP_ENV` | `production` |
| `APP_KEY` | *(Run `php artisan key:generate --show` locally to get this)* |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://your-app-name.onrender.com` (Auto-generated) |
| `LOG_CHANNEL` | `stderr` (Important for Docker logs) |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | `aws-1-ap-southeast-2.pooler.supabase.com` |
| `DB_PORT` | `6543` (Use Transaction Mode port 6543 for production) |
| `DB_DATABASE` | `postgres` |
| `DB_USERNAME` | `postgres.uxczcgqpucwzkyxqyfkw` |
| `DB_PASSWORD` | `m4wZUuHcWIYolzQh` |

> **Note on Port**: Standard Supabase port is `5432`. For serverless/cloud deployments like Render, Supabase recommends port `6543` (Transaction Pooler) to manage connections better.

---

## Step 4: Deploy

1.  Click **Create Web Service**.
2.  Render will start building your Docker image. This might take a few minutes the first time.
3.  Once the build finishes, it will deploy the container.
4.  When the status says **Live**, click the URL to access your app.

## Troubleshooting

*   **Build Failed**: Check the logs. If it fails on `composer install`, ensure `composer.json` and `composer.lock` are in the repo.
*   **500 Error**: Check the logs. Usually a missing environment variable or permission issue. The `Dockerfile` sets permissions for `storage` automatically.
*   **Database Connection Error**: Verify `DB_HOST`, `DB_PORT` (try 5432 if 6543 fails), `DB_USERNAME`, and `DB_PASSWORD` are correct in Render's Environment Variables.
