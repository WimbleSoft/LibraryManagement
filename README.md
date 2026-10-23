# Library Records

A small PHP web application for keeping track of a library's books, computers
and room keys, and of who has borrowed them.

## Requirements

- PHP 8.2 or newer with the `pdo_mysql` and `mbstring` extensions,
  on a release that includes the October 2026 security fixes
  (8.2.35, 8.3.36, 8.4.27, 8.5.12 or later). See [SECURITY.md](SECURITY.md).
- MySQL 5.7+ or MariaDB 10.3+
- Apache (the bundled `.htaccess` protects internal folders) or another web
  server configured to deny access to `includes/`, `lang/` and `database/`.

## Installation

1. Create the database and the first administrator account:

   ```sh
   mysql -u root -p < database/libraryrecords.sql
   ```

2. Create a database user for the application with only the rights it needs:

   ```sql
   CREATE USER 'library'@'localhost' IDENTIFIED BY 'a-strong-password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON libraryrecords.* TO 'library'@'localhost';
   ```

3. Give the application its database settings, either as environment variables
   (`LIBRARY_DB_HOST`, `LIBRARY_DB_PORT`, `LIBRARY_DB_NAME`, `LIBRARY_DB_USER`,
   `LIBRARY_DB_PASSWORD`) or by editing `includes/config.php`.

4. Open the site and sign in as **admin** with the password **ChangeMe123!**,
   then change the password straight away on the *Settings* page.

### Upgrading from version 1

Back up your database, then run the upgrade script once:

```sh
mysql -u root -p libraryrecords < database/upgrade-v1-to-v2.sql
```

Existing passwords keep working and are converted to secure hashes the next
time each person signs in.

## Project structure

| Path                    | Contents                                                     |
| ----------------------- | ------------------------------------------------------------ |
| `*.php` (root)          | One file per screen; each also handles that screen's forms   |
| `includes/`             | Start-up code, configuration, helpers and the page layout    |
| `lang/`                 | Interface translations (`en`, `tr`)                          |
| `database/`             | Schema for new installs and the version 1 upgrade script     |
| `assets/`               | The application's own CSS and JavaScript                     |
| `dist/`                 | AdminLTE theme files                                         |

## Issues

Please open an issue if you find a problem. This project is not under
constant development, but reports are welcome.
