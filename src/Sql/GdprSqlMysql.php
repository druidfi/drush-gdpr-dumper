<?php

namespace Drupal\gdpr_dumper\Sql;

use DrupalFinder\DrupalFinder;
use Drush\Sql\SqlMysql;

/**
 * Class GdprSqlMysql
 * @package Drupal\gdpr_dumper\Commands
 */
class GdprSqlMysql extends SqlMysql
{
    /**
     * @see SqlMysql::dumpCmd()
     */
    public function dumpCmd($table_selection): string
    {
        $exec = parent::dumpCmd($table_selection);

        $drupal_finder = new DrupalFinder();
        $drupal_finder->locateRoot(DRUPAL_ROOT);
        $vendor_dir = $drupal_finder->getVendorDir();

        // Replace default dump command with the GDPR-compliant one.
        return str_replace('mysqldump', $vendor_dir . '/bin/mysqldump', $exec);
    }

}
