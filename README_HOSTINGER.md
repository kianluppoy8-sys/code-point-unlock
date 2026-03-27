# Hosting on Hostinger - Step by Step Guide

To host **Code Point Unlock** on Hostinger, follow these instructions:

## 1. Upload Files
- Using Hostinger's **File Manager** or **FTP (FileZilla)**, upload all project files to the `public_html` directory.
- Ensure the `.htaccess` file is also uploaded.

## 2. Create Database
1. Log in to your Hostinger hPanel.
2. Go to **Databases** -> **MySQL Databases**.
3. Create a new database and a new user. Keep the **Database Name**, **Username**, and **Password** handy.
4. Go to **phpMyAdmin** for your new database.
5. Click **Import** and select the `database.sql` file from the project root.
6. (Optional) If you want the Grade 12 curriculum, import `grade12_curriculum.sql` as well.

## 3. Configuration
1. In the `includes/` directory, find `.env.example.php`.
2. Rename it to `.env.php` (or create a copy).
3. Open `.env.php` and fill in your database details:
   - `DB_NAME`: Your Hostinger database name (e.g., `u123456789_db`)
   - `DB_USER`: Your Hostinger database user (e.g., `u123456789_admin`)
   - `DB_PASS`: The password you created.
   - `SITE_URL`: Your full project link.
     - If in root: `https://yourdomain.com`
     - If in folder: `https://yourdomain.com/code-point-unlock-php` (No trailing slash)

## 4. Run Import (Optional)
If you want to add the default challenges, visit `https://yourdomain.com/import_questions.php` in your browser. You can delete this file after use for security.

## 5. Security Notes
- The `.htaccess` file already protects your configuration and SQL files from public access.
- It also forces HTTPS and removes the `.php` extension from your URLs for a professional look.
- Ensure your PHP version is set to 7.4 or higher (8.1+ recommended) in hPanel.

## Troubleshooting
- **Database Connection Error**: Double-check the host (usually `localhost`), username, and password in `includes/.env.php`.
- **404 Errors**: Ensure `mod_rewrite` is enabled on your Hostinger plan (it's enabled by default on most).
- **Compilation Errors**: Code Point Unlock uses the **JDoodle API** globally for code execution because other free APIs implemented whitelists. You need to create a free account at [jdoodle.com/compiler-api](https://www.jdoodle.com/compiler-api/), get your `clientId` and `clientSecret`, and place them inside your `includes/.env.php` file using `JDOODLE_CLIENT_ID` and `JDOODLE_CLIENT_SECRET`.
