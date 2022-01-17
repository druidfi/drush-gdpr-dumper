<?php

namespace Drupal\gdpr_dumper\Commands;

use Drush\Commands\sql\SqlSyncCommands as SqlSyncCommandsBase;

/**
 * Class SqlSyncCommands
 * @package Drupal\gdpr_dumper\Commands
 */
class SqlSyncCommands extends SqlSyncCommandsBase
{
    /**
     * @inheritDoc
     */
    public function sqlsync($source, $target, $options = ['no-dump' => false, 'no-sync' => false, 'runner' => self::REQ, 'create-db' => false, 'db-su' => self::REQ, 'db-su-pw' => self::REQ, 'target-dump' => self::REQ, 'source-dump' => self::OPT, 'extra-dump' => self::REQ]): void
    {
        parent::sqlsync($source, $target, $options);
    }

    /**
     * Perform sql-dump on source unless told otherwise.
     *
     * @param $options
     * @param $global_options
     * @param $sourceRecord
     * @return string
     *   Path to the source dump file.
     * @throws \Exception
     */
    public function dump($options, $global_options, $sourceRecord): string
    {
        $dump_options = $global_options + [
            'gzip' => true,
            'result-file' => $options['source-dump'] ?: 'auto',
        ];
        if (!$options['no-dump']) {
            $this->logger()->notice(dt('Starting to dump database on source.'));
            $process = $this->processManager()->drush($sourceRecord, 'sql-dump-gdpr', [], $dump_options + ['format' => 'json']);
            $process->mustRun();

            if ($this->getConfig()->simulate()) {
                $source_dump_path = '/simulated/path/to/dump.tgz';
            } else {
                // First try a Drush 9.6+ return format.
                $json = $process->getOutputAsJson();
                if (!empty($json['path'])) {
                    $source_dump_path = $json['path'];
                } else {
                    // Next, try 9.5- format.
                    $return = drush_backend_parse_output($process->getOutput());
                    if (!$return['error_status'] || !empty($return['object'])) {
                        $source_dump_path = $return['object'];
                    }
                }
            }
        } else {
            $source_dump_path = $options['source-dump'];
        }

        if (empty($source_dump_path)) {
            throw new \Exception(dt('The Drush sql:dump command did not report the path to the dump file.'));
        }
        return $source_dump_path;
    }
}
