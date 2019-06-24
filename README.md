# Kemana Directory

## Overview

Kemana Directory is a free & open source link (and multipurpose) directory. You can use Kemana Directory to open your own business directory, classifieds, link indexing, and more with ease!

You can also make money by offering Premium & Sponsored listings!

![Kemana Hero](https://www.c97.net/public/image/kemana-hero.jpg)

## More Information

Learn more about Kemana Directory: https://www.c97.net/kemana-the-ultimate-php-directory-script.php

Or try the live demo here: https://www.c97.net/kemana-demonstration.php

If you feel the script is useful, consider purchasing the commercial license: https://www.c97.net/buy-now.php

_You can still use the script for free, but I will be very grateful if you purchase the license. Also you will receive personal support from me._

## Installing

1. Simply download the zip file.
2. Extract in your local machine or server.
3. Create a new database (or use existing one), make sure that it is: MySQL or MariaDB.
  - For best compatibility, use UTF8 as collation.
3. With your browser, open your web site, eg: http://www.example.com/kemana
4. You will be promoted to install the script, do so by following the on screen instruction.
5. Fill the necessary information, including Server Address, database name, password, etc, click "Next".
6. The installer should create `includes/db_config.php` for you.
  - :cry: But if somehow the installer failed to create one, you will be presented with an instruction on how to create one.
  - :grinning: Simply copy & paste the code, and save it to `includes/db_config.php`.

## Running For The First Time

:warning: Before running the script, be sure to remove the installation folder. Remove the `/install` folder.

## Accessing the ACP (Administrator's Control Panel)

Open your site name, add `/admin` as address. Eg: `http://www.example.com/admin`. If you have renamed the ACP folder name, use the new name.

## Renaming Administrator's Folder

:bulb: To increase site security, you can rename the administration (admin) folder from `/admin` to `/any_folder`.
1. Rename the `/admin` folder.
2. Use any text editor to edit `/includes/db_config.php`
3. On line 10, you should find this configuration:
```php
$qe_admin_folder = 'admin';
```
4. Simply change the value to the new name.
