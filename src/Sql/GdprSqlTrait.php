<?php

namespace Drupal\gdpr_dumper\Sql;

use DrupalFinder\DrupalFinder;

/**
 * Trait GdprSqlTrait
 * @package Drupal\gdpr_dumper\Sql
 */
trait GdprSqlTrait {

  protected array $driverOptions;

  /**
   * {@inheritdoc}
   */
  public function dumpCmd($table_selection): string {
    $cmd = parent::dumpCmd($table_selection);

    $drupal_finder = new DrupalFinder();
    $drupal_finder->locateRoot(DRUPAL_ROOT);
    $vendor_dir = $drupal_finder->getVendorDir();

    if ($vendor_dir && isset($this->driverOptions['dump_command'])) {
      // Replace default dump command with the GDPR compliant one.
      $cmd = str_replace($this->driverOptions['dump_command'], $vendor_dir . '/bin/mysqldump', $cmd);
    }

    return $cmd;
  }

  /**
   * @return array
   */
  public function getDriverOptions(): array {
    return $this->driverOptions;
  }

  /**
   * @param array $options
   */
  public function setDriverOptions(array $options) {
    $this->driverOptions = $options;
  }
}
