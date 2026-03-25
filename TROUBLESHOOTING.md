# Troubleshooting: "Could not find driver" Error

The error `Illuminate\Database\QueryException: could not find driver` indicates that your local PHP installation does not have the **PostgreSQL extension** enabled.

## How to Fix

1.  **Locate your `php.ini` file.**
    *   If you are using XAMPP, it is usually at `C:\xampp\php\php.ini`.
    *   If you are using Laragon, it is accessible via the Menu > PHP > php.ini.
    *   You can find the location by running `php --ini` in your terminal.

2.  **Edit `php.ini`.**
    Open the file in a text editor (Notepad, VS Code, etc.) and search for the following lines:

    ```ini
    ;extension=pdo_pgsql
    ;extension=pgsql
    ```

3.  **Enable the Extensions.**
    Remove the semicolon (`;`) from the beginning of those lines to uncomment them:

    ```ini
    extension=pdo_pgsql
    extension=pgsql
    ```

4.  **Save the file.**

5.  **Verify the fix.**
    Run the following command in your terminal to list installed modules:
    ```bash
    php -m
    ```
    You should see `PDO_PGSQL` and `pgsql` in the list.

6.  **Retry Migration.**
    Now run the migration command again:
    ```bash
    php artisan migrate
    ```

## Note on Supabase Ports
Once the driver is fixed, if you see connection errors, ensure your `.env` uses the correct port for the Supabase pooler (often `6543` for transaction mode or `5432` for session mode).

```env
DB_PORT=5432
```
(Change to 6543 if 5432 fails or times out).
