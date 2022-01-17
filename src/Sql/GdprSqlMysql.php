<?php

namespace Drupal\gdpr_dumper\Sql;

use Drush\Sql\SqlMysql;

/**
 * Class GdprSqlMysql
 * @package Drupal\gdpr_dumper\Commands
 */
class GdprSqlMysql extends SqlMysql {

  use GdprSqlTrait;

}
