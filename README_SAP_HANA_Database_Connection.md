# SAP HANA Database Connection for Laravel 13

A step-by-step guide for reusing the custom SAP HANA ODBC connection in
this project or setting it up in another Laravel project.

> **Purpose:** Connect Laravel to SAP HANA through the SAP HANA ODBC
> driver (`HDBODBC`) and PDO_ODBC.
>
> **Current project layout:** `app/Database/Hana/`,
> `app/Providers/HanaServiceProvider.php`, `bootstrap/providers.php`,
> and `config/database.php`.
>
> **Important:** Copy the working implementation from the source
> project, then verify the namespaces and constructors match. Do not
> copy only the database configuration; Laravel must also register the
> custom `saphana` driver.

------------------------------------------------------------------------

## 1. Prerequisites

Confirm the target machine has:

-   Laravel 13 and a supported PHP version installed.
-   SAP HANA Client installed, including the `HDBODBC` ODBC driver.
-   The correct HANA host, SQL port, database/tenant, username, and
    password.
-   PHP's `PDO_ODBC` extension enabled for the **same PHP runtime that
    serves the application**.
-   Network access from the Laravel host to the SAP HANA server.

The default HANA SQL port is commonly `3<instance-number>15`; for
instance number `00`, it is often `30015`. Confirm the actual port with
the HANA administrator.

## 2. Required project files

The custom connection in this project uses the following structure:

``` text
app/
├── Database/
│   └── Hana/
│       ├── HanaConnection.php
│       └── HanaGrammar.php
├── Providers/
│   └── HanaServiceProvider.php
bootstrap/
└── providers.php
config/
└── database.php
```

The files have distinct responsibilities:

-   `HanaConnection.php`: custom Laravel database connection behavior.
-   `HanaGrammar.php`: SQL grammar adjustments needed by this
    implementation.
-   `HanaServiceProvider.php`: registers the custom `saphana` driver
    with Laravel.
-   `bootstrap/providers.php`: loads the service provider.
-   `config/database.php`: defines the connection and ODBC DSN.
-   `.env`: stores environment-specific connection settings.

If the source project has additional connector or processor classes,
copy those too. Keep the class names, namespaces, and constructor
signatures consistent with the implementation that has been tested
successfully.

## 3. Install and verify the SAP HANA Client

Install the SAP HANA Client that is appropriate for the operating system
and HANA environment. Confirm that the ODBC driver named `HDBODBC` is
installed.

On Windows, verify the driver through **ODBC Data Sources** (use the
correct 64-bit/32-bit administrator matching PHP) or the installed SAP
HANA Client configuration.

Do not assume the driver is available merely because SAP HANA Studio or
Database Explorer works; those tools may use a different client/runtime.

## 4. Enable PDO_ODBC for PHP

### Windows / Laragon

First, identify which `php.ini` the application uses. A diagnostic
endpoint can report:

``` php
[
    'php_version' => PHP_VERSION,
    'php_ini' => php_ini_loaded_file(),
    'pdo_drivers' => PDO::getAvailableDrivers(),
    'odbc_extension' => extension_loaded('odbc'),
    'pdo_odbc_extension' => extension_loaded('pdo_odbc'),
]
```

Check the `ext` directory belonging to that PHP installation and verify
that the required ODBC extension DLLs are present and compatible with
the PHP version, architecture, and thread-safety build.

If the matching DLLs are available, enable the corresponding entries in
that runtime's `php.ini`, for example:

``` ini
extension=odbc
extension=pdo_odbc
```

Only use extension names and DLLs supplied for the exact PHP build. If a
DLL is missing, install/repair the matching PHP distribution rather than
downloading an untrusted DLL.

Restart the process serving the API after changing `php.ini`:

-   Restart Apache/Nginx and its PHP runtime, as applicable.
-   Stop and restart `php artisan serve`, if used.
-   Restart persistent queue workers or other long-running PHP
    processes.

Check CLI and web/API separately. They can use different PHP
installations or `php.ini` files.

### Verify the PDO driver

Run:

``` powershell
php --ini
php -m
php -r "print_r(PDO::getAvailableDrivers());"
```

The available PDO drivers should include `odbc`. Also verify
`extension_loaded('pdo_odbc')` is `true` in the API's PHP runtime.

## 5. Configure environment variables

Add the relevant settings to `.env`. Use the values provided by your
HANA administrator:

``` dotenv
HANA_HOST=your-hana-hostname-or-ip
HANA_PORT=30015
HANA_DATABASE=your-hana-tenant-or-database
HANA_USERNAME=your-hana-user
HANA_PASSWORD="your-password"
```

Notes:

-   Replace all example values.
-   Do not commit `.env` or real credentials to source control.
-   Quote passwords containing special characters when necessary.
-   Confirm whether `HANA_DATABASE` should identify a tenant/database in
    your specific HANA setup; follow the connection convention already
    validated in this project.

## 6. Add the custom connection to `config/database.php`

Add the `saphana` connection inside the `connections` array. Keep it
consistent with the existing custom connector implementation:

``` php
'saphana' => [
    'driver' => 'saphana',
    'dsn' => 'odbc:DRIVER={HDBODBC};SERVERNODE='
        . env('HANA_HOST')
        . ':'
        . env('HANA_PORT', 30015)
        . ';DATABASE='
        . env('HANA_DATABASE')
        . ';CHAR_AS_UTF8=TRUE',
    'host' => env('HANA_HOST'),
    'port' => env('HANA_PORT', 30015),
    'database' => env('HANA_DATABASE'),
    'username' => env('HANA_USERNAME'),
    'password' => env('HANA_PASSWORD'),
    'prefix' => '',
    'options' => [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_EMULATE_PREPARES => true,
    ],
],
```

This configuration is for the custom driver shown in this project. Do
not expect it to work until the service provider registers `saphana` and
the custom connection implementation is in place.

## 7. Register the service provider --- do not skip this

Laravel 13 uses `bootstrap/providers.php` to list application service
providers.

Confirm that `App\Providers\HanaServiceProvider::class` is included
exactly once. For example:

``` php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\HanaServiceProvider::class,
];
```

Keep any other providers already required by the application. Do not
replace the entire file blindly.

Open `app/Providers/HanaServiceProvider.php` and confirm its `boot()`
method registers the custom driver using Laravel's database manager,
normally through `DB::extend('saphana', ...)`. The callback must
construct the project's actual custom connection/connector classes.

**Most common setup mistake:** adding the `'saphana'` entry to
`config/database.php` but forgetting to register `HanaServiceProvider`
in `bootstrap/providers.php`. This produces an error such as:

``` text
Unsupported driver [saphana].
```

Do not paste a generic provider callback without checking the actual
constructor and connector methods in your project.

## 8. Clear cached configuration

From the project root, run:

``` powershell
php artisan optimize:clear
```

If the provider or class files were newly added, confirm their
namespaces and paths match PSR-4 autoloading. If needed, run:

``` powershell
composer dump-autoload
```

Restart the web/API runtime after PHP configuration or provider changes.

## 9. Test the connection in Tinker

Start Tinker:

``` powershell
php artisan tinker
```

### Test A --- Get the PDO connection

``` php
DB::connection('saphana')->getPdo();
```

A successful result should show a `PDO` object with `DRIVER_NAME` equal
to `odbc` and HANA server information.

### Test B --- Run a basic HANA query

``` php
DB::connection('saphana')->select(
    'SELECT CURRENT_USER, CURRENT_SCHEMA FROM DUMMY'
);
```

### Test C --- Check the HANA database version (permissions permitting)

``` php
DB::connection('saphana')->select(
    'SELECT DATABASE_NAME, VERSION FROM SYS.M_DATABASE'
);
```

If the account cannot read `SYS.M_DATABASE`, that may be a privilege
restriction. It does not by itself prove that the connection is broken.

### Test D --- Query an SAP Business One table

Use the actual company schema name:

``` php
DB::connection('saphana')->select(
    'SELECT COUNT(*) AS TABLE_COUNT
     FROM "YOUR_COMPANY_SCHEMA"."OUSR"'
);
```

Replace `YOUR_COMPANY_SCHEMA` with the schema you are authorized to
query. The schema-qualified SQL is intentional for this ODBC
integration.

## 10. Test the API runtime separately

A successful Tinker test proves the **CLI PHP runtime** can connect. It
does not prove the PHP runtime serving the API has the same extensions
or configuration.

Temporarily add a protected/local-only diagnostic endpoint and remove it
after testing:

``` php
use Illuminate\Support\Facades\Route;

Route::get('/diagnostics/php', function () {
    return response()->json([
        'php_version' => PHP_VERSION,
        'php_ini' => php_ini_loaded_file(),
        'pdo_drivers' => \PDO::getAvailableDrivers(),
        'odbc_extension' => extension_loaded('odbc'),
        'pdo_odbc_extension' => extension_loaded('pdo_odbc'),
    ]);
});
```

Call the endpoint using your actual API host/port. Compare its output
with the CLI checks. Do not leave an unrestricted diagnostic endpoint
deployed publicly.

## 11. Common errors and fixes

  -------------------------------------------------------------------------------------------
  Error                            Likely cause             What to check
  -------------------------------- ------------------------ ---------------------------------
  `Unsupported driver [saphana]`   Custom driver not        `bootstrap/providers.php`,
                                   registered               provider namespace,
                                                            `DB::extend('saphana', ...)`

  `could not find driver`          PDO_ODBC unavailable in  `php_ini_loaded_file()`,
                                   the PHP runtime making   `PDO::getAvailableDrivers()`,
                                   the connection           `extension_loaded('pdo_odbc')`;
                                                            test CLI and API separately

  `Data source name not found` /   `HDBODBC` not            SAP HANA Client installation and
  ODBC driver not found            installed/registered for Windows ODBC driver list
                                   the relevant             
                                   architecture or runtime  

  Connection refused / timeout     Wrong host/port, network Host, SQL port, firewall, tenant
                                   route, firewall, or HANA configuration
                                   listener issue           

  Authentication failure           Incorrect credentials,   Verify account and credentials
                                   locked/deactivated       with the HANA administrator
                                   account, or              
                                   authentication policy    

  Permission denied on a           The connected HANA user  Request least-privilege grants
  schema/table                     lacks required           for the required schema and
                                   privileges               operations

  Tinker works but API fails       CLI and web/API use      Compare PHP version, `php.ini`,
                                   different PHP            PDO drivers, and configured
                                   runtime/configuration,   connection name
                                   or API uses a different  
                                   connection               
  -------------------------------------------------------------------------------------------

## 12. Use the connection in application code

Explicitly select the custom connection for queries and models.

Example query:

``` php
$users = DB::connection('saphana')->select(
    'SELECT USERID, USER_CODE, U_NAME
     FROM "YOUR_COMPANY_SCHEMA"."OUSR"
     ORDER BY USER_CODE'
);
```

Example Eloquent model:

``` php
<?php

namespace App\Models\Hana;

use Illuminate\Database\Eloquent\Model;

class BusinessPartner extends Model
{
    protected $connection = 'saphana';

    protected $table = 'YOUR_COMPANY_SCHEMA.OCRD';

    public $timestamps = false;

    protected $primaryKey = 'CardCode';

    public $incrementing = false;

    protected $keyType = 'string';
}
```

**Important:** Verify whether the custom connection correctly quotes
schema-qualified table names before relying on Eloquent. If it does not,
use explicit schema-qualified SQL or adapt the grammar/model strategy
and test it. SAP HANA identifiers can be case-sensitive when quoted.

For read-only integration, grant only the permissions the application
needs. Do not use the SAP HANA `SYSTEM` account for routine application
queries.

## 13. New-project delegation checklist

-   [ ] SAP HANA Client and `HDBODBC` installed.
-   [ ] Correct PHP version and matching PDO_ODBC extension enabled.
-   [ ] API runtime checked independently from CLI.
-   [ ] `HanaConnection.php` and `HanaGrammar.php` copied with correct
    namespaces.
-   [ ] Any additional connector/processor classes from the working
    implementation copied.
-   [ ] `HanaServiceProvider.php` copied and registered in
    `bootstrap/providers.php`.
-   [ ] `saphana` connection added to `config/database.php`.
-   [ ] HANA settings added to `.env`; credentials not committed.
-   [ ] `php artisan optimize:clear` executed.
-   [ ] `DB::connection('saphana')->getPdo()` succeeds.
-   [ ] Basic query against `DUMMY` succeeds.
-   [ ] Query against the intended company schema succeeds.
-   [ ] API runtime reports `odbc` in its PDO driver list.
-   [ ] API endpoint tested successfully.
-   [ ] Diagnostic endpoint removed.
-   [ ] Required database privileges reviewed using least privilege.

## 14. Security and operational notes

-   Never commit passwords or production `.env` files.
-   Avoid logging credentials, DSNs containing secrets, or sensitive
    business data.
-   Do not use `SYSTEM` as the application's regular database account.
-   Test changes against a non-production company schema first.
-   A successful Laravel-to-HANA connection is separate from SAP
    Business One company registration, SLD configuration, licensing, and
    application login.
-   Back up important data before importing, replacing, or modifying
    company schemas.
