<?php

namespace Drupal\gdpr_dumper\Commands;

use Drush\Commands\sql\SqlSyncCommands as SqlSyncCommandsBase;

/**
 * Class SqlSyncCommands
 * @package Drupal\gdpr_dumper\Commands
 */
class SqlSyncCommands extends SqlSyncCommandsBase {
    /**
     * Copy DB data from a source site to a target site. Transfers data via rsync.
     *
     * @command sql:sync-gdpr
     * @aliases sql-sync-gdpr
     * @param $source A site-alias or the name of a subdirectory within /sites whose database you want to copy from.
     * @param $target A site-alias or the name of a subdirectory within /sites whose database you want to replace.
     * @optionset_table_selection
     * @option no-dump Do not dump the sql database; always use an existing dump file.
     * @option no-sync Do not rsync the database dump file from source to target.
     * @option runner Where to run the rsync command; defaults to the local site. Can also be <info>source</info> or <info>target</info>.
     * @option create-db Create a new database before importing the database dump on the target machine.
     * @option db-su Account to use when creating a new database (e.g. <info>root</info>).
     * @option db-su-pw Password for the db-su account.
     * @option source-dump The path for retrieving the sql-dump on source machine.
     * @option target-dump The path for storing the sql-dump on target machine.
     * @option extra-dump Add custom arguments/options to the dumping of the database (e.g. mysqldump command).
     * @usage drush sql:sync-gdpr @source @self
     *   Copy the database from the site with the alias 'source' to the local site.
     * @usage drush sql:sync-gdpr @self @target
     *   Copy the database from the local site to the site with the alias 'target'.
     * @usage drush sql:sync-gdpr #prod #dev
     *   Copy the database from the site in /sites/prod to the site in /sites/dev (multisite installation).
     * @topics docs:aliases,docs:policy,docs:configuration,docs:example-sync-via-http
     * @throws \Exception
     */
    public function sqlsync($source, $target, $options = ['no-dump' => false, 'no-sync' => false, 'runner' => self::REQ, 'create-db' => false, 'db-su' => self::REQ, 'db-su-pw' => self::REQ, 'target-dump' => self::REQ, 'source-dump' => self::OPT, 'extra-dump' => self::REQ])
    {
        parent::sqlsync($source, $target, $options);
    }

    /**
     * Perform sql-dump on source unless told otherwise.
     *
     * @param $options
     * @param $global_options
     * @param $sourceRecord
     *
     * @return string
     *   Path to the source dump file.
     * @throws \Exception
     */
    public function dump($options, $global_options, $sourceRecord)
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
