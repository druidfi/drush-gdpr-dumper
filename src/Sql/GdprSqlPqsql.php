<?php

namespace Drupal\gdpr_dumper\Sql;

use Drush\Sql\SqlPgsql;

/**
 * Class GdprSqlPqsql
 * @package Drupal\gdpr_dumper\Commands
 */
class GdprSqlPqsql extends SqlPgsql
{
  use GdprSqlTrait;

}
