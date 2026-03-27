# Code Point Unlock

Code Point Unlock is a web-based educational platform that allows students to solve programming challenges to "unlock" points and advance through levels. Originally using the Piston API for code execution, it now uses **JDoodle** for a more reliable, auth-based free code execution.

## Important Note regarding Code Execution API
The Java & Python compiler functionalities previously used a public API without authentication (Piston). However, due to recent whitelists implemented by that API, this system has been migrated to use **JDoodle API** instead.

To set up JDoodle:
1. Go to [jdoodle.com/compiler-api](https://www.jdoodle.com/compiler-api/) and create a free account.
2. In your dashboard, get your `clientId` and `clientSecret`.
3. In this project, open `includes/.env.php` and set the following constants:
```php
define('JDOODLE_CLIENT_ID', 'YOUR_JDOODLE_CLIENT_ID');
define('JDOODLE_CLIENT_SECRET', 'YOUR_JDOODLE_CLIENT_SECRET');
```

## Setup Instructions
Please refer to `README_HOSTINGER.md` for full database and Hostinger setup instructions.
