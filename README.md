# Drush GDPR Dumper

A drop-in replacement for drush sql-dump that optionally sanitizes DB fields for better GDPR conformity.

It is based on the [druidfi/gdpr-mysqldump](https://github.com/druidfi/gdpr-mysqldump) library, 
and can in principle dump any database that PDO supports. 

## Install

Require druidfi/gdpr-mysqldump with Composer:

```
composer require druidfi/gdpr-mysqldump
```

Enable the module in Drupal UI or with Drush:

```
drush en gdpr_dumper
```

## Drush

If you want to create a sql dump on live servers for local purposes (as a developer, themer, ...), 
you should use following command:

```
drush sql-dump-gdpr > dump.sql
```

instead of 

```
drush sql-dump > dump.sql
```

`drush sql-dump-gdpr` will automatically strip all GDPR related data from the sql dump to help you 
be GDPR compliant YO!

## Configuration

In $settings.php you can do the configuration like this:

```
$config['gdpr_dumper.settings']['gdpr_replacements'] = [
  'users_field_data' => [ // Table
    'name' => [ // Field
      'formatter' => 'userName', // Faker formatter
    ],
  ],
];
```

This module can be configured by editing the `gdpr_dumper.settings.yml` [file](config/install/gdpr_dumper.settings.yml).

[druidfi/gdpr-mysqldump](https://github.com/druidfi/gdpr-mysqldump) contains more info about 
the **gdpr-expressions** and **gdpr-replacement** options.

[Faker](https://fakerphp.github.io/) documentation lists all available formatters.

## Events

The module dispatches two events:
* `GdprDumperEvents::GDPR_EXPRESSIONS`
* `GdprDumperEvents::GDPR_REPLACEMENTS`
 
This allows developers to alter the expressions and replacements through event subscribers on run-time

Happy GDPR'ing!

## Forked from

This tool is a fork if [robiningelbrecht/drush-gdpr-dumper](https://github.com/robiningelbrecht/drush-gdpr-dumper).

## License

This component is under the GPL2 license. See the complete license in the LICENSE file.

## Other information

This project can be found from the Packagist: https://packagist.org/packages/druidfi/drush-gdpr-dumper
