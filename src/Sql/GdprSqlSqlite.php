<?php

namespace Drupal\gdpr_dumper\Sql;

use Drush\Sql\SqlSqlite;

/**
 * Class GdprSqlSqlite
 * @package Drupal\gdpr_dumper\Commands
 */
class GdprSqlSqlite extends SqlSqlite {

  use GdprSqlTrait;

}
