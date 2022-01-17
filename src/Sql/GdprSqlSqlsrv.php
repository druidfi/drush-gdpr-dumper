<?php

namespace Drupal\gdpr_dumper\Sql;

use Drush\Sql\SqlSqlsrv;

/**
 * Class GdprSqlSqlsrv
 * @package Drupal\gdpr_dumper\Commands
 */
class GdprSqlSqlsrv extends SqlSqlsrv
{
  use GdprSqlTrait;

}
