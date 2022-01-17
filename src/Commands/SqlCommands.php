<?php

namespace Drupal\gdpr_dumper\Commands;

use Consolidation\OutputFormatters\StructuredData\PropertyList;
use Drupal\gdpr_dumper\Sql\GdprSqlBase;
use Drush\Commands\sql\SqlCommands as SqlCommandsBase;

/**
 * Class SQlCommands
 * @package Drupal\gdpr_dumper\Commands
 */
class SqlCommands extends SqlCommandsBase
{
    /**
     * Exports a GDPR compliant Drupal DB as SQL using mysqldump or equivalent.
     *
     * @command sql:dump-gdpr
     * @aliases sql-dump-gdpr
     * @optionset_sql
     * @optionset_table_selection
     * @option result-file Save to a file. The file should be relative to Drupal root. If --result-file is provided with the value 'auto', a date-based filename will be created under ~/drush-backups directory.
     * @option create-db Omit DROP TABLE statements. Used by Postgres and Oracle only.
     * @option data-only Dump data without statements to create any of the schema.
     * @option ordered-dump Order by primary key and add line breaks for efficient diffs. Slows down the dump. Mysql only.
     * @option gzip Compress the dump using the gzip program which must be in your $PATH.
     * @option extra Add custom arguments/options when connecting to database (used internally to list tables).
     * @option extra-dump Add custom arguments/options to the dumping of the database (e.g. mysqldump command).
     * @usage drush sql:dump-gdpr --result-file=../18.sql
     *   Save SQL dump to the directory above Drupal root.
     * @usage drush sql:dump-gdpr --skip-tables-key=common
     *   Skip standard tables. @throws \Exception
     * @see example.drush.yml
     * @usage drush sql:dump-gdpr --extra-dump=--no-data
     *   Pass extra option to mysqldump command.
     * @hidden-options create-db
     * @bootstrap max configuration
     *
     * @notes
     *   createdb is used by sql-sync, since including the DROP TABLE statements interfere with the import when the database is created.
     */
    public function dump($options = ['result-file' => self::REQ, 'create-db' => false, 'data-only' => false, 'ordered-dump' => false, 'gzip' => false, 'extra' => self::REQ, 'extra-dump' => self::REQ, 'format' => 'null']): PropertyList
    {
        // Create new GDPR-compliant dump of DB.
        $sql = GdprSqlBase::create($options);
        $return = $sql->dump();

        if ($return === false) {
            throw new \Exception('Unable to dump database. Rerun with --debug to see any error message.');
        }

        // SqlBase::dump() returns null if 'result-file' option is empty.
        if ($return) {
            $this->logger()->success(dt('Database dump saved to !path', ['!path' => $return]));
        }

        return new PropertyList(['path' => $return]);
  }
}
