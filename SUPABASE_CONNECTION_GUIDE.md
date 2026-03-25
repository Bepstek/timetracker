# Supabase Connection Guide for Laravel

For a Laravel application (especially when running on serverless or limited-connection environments), the recommended connection method depends on how you are running your app.

## Recommended: Transaction Pooler (Port 6543)
**Use this for almost all production Laravel apps.**

*   **Why?** Laravel opens a database connection for every single HTTP request. The Transaction Pooler allows thousands of these short-lived connections to share a small pool of actual database connections.
*   **Settings:**
    *   **Host**: `aws-0-us-west-1.pooler.supabase.com` (Your pooler URL)
    *   **Port**: `6543`
    *   **Mode**: Transaction

## Option 2: Session Pooler (Port 5432)
**Use this only if you use prepared statements with specific session-level features.**

*   **Why?** It maintains a session for the client, but it consumes more connections than the transaction pooler.
*   **Settings:**
    *   **Host**: `aws-0-us-west-1.pooler.supabase.com`
    *   **Port**: `5432`

## Option 3: Direct Connection (Port 5432)
**Use this ONLY for running migrations.**

*   **Why?** Migrations often require schema changes that transaction poolers don't handle well or require lock management.
*   **Settings:**
    *   **Host**: `db.projectref.supabase.co` (The direct DB URL, not the pooler)
    *   **Port**: `5432`

---

## Best Practice for Your Project

1.  **For Running the App (`php artisan serve`)**:
    Use the **Transaction Pooler** (Port 6543) in your `.env`.
    ```env
    DB_PORT=6543
    ```

2.  **For Running Migrations (`php artisan migrate`)**:
    If migrations fail on port 6543, temporarily switch to the **Session Pooler** or **Direct Connection** (Port 5432), run the migration, and then switch back.
