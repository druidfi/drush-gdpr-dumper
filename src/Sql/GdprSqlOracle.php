<?php

namespace Drupal\gdpr_dumper\Sql;

use Drush\Sql\SqlOracle;

/**
 * Class GdprSqlOracle
 * @package Drupal\gdpr_dumper\Commands
 */
class GdprSqlOracle extends SqlOracle
{
  use GdprSqlTrait;

}
