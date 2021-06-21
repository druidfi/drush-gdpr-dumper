# Drush GDPR Dumper

A drop-in replacement for drush sql-dump that optionally sanitizes DB fields for better GDPR conformity.

It is based on the [druidfi/gdpr-mysqldump](https://github.com/druidfi/gdpr-mysqldump) library, 
and can in principle dump any database that PDO supports. 

## Drush

If you want to create an sql dump on live servers for local purposes (as a developer, themer, ...), 
you should use following command:

```
drush sql-dump-gdpr > file.sql
```

instead of 

```
drush sql-dump > file.sql
```

`drush sql-dump-gdpr` will automatically strip all GDPR related data from the sql dump to help you 
be GDPR compliant YO!

## Configuration

This module can be configured by editing the `gdpr_dumper.settings.yml` [file](https://github.com/robiningelbrecht/gdpr-dumper/blob/master/config/install/gdpr_dumper.settings.yml).

[druidfi/gdpr-mysqldump](https://github.com/druidfi/gdpr-mysqldump) contains more info about 
the **gdpr-expressions** and **gdpr-replacement** options. 

The provided yml file expects the same structure as explained in the readme above.

## Events

The module dispatches two events:
* `GdprDumperEvents::GDPR_EXPRESSIONS`
* `GdprDumperEvents::GDPR_REPLACEMENTS`
 
This allows developers to alter the expressions and replacements through event subscribers on run-time

Happy GDPR'ing!
