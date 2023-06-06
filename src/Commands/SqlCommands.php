<?php

namespace Drupal\gdpr_dumper\Commands;

use Consolidation\OutputFormatters\StructuredData\PropertyList;
use Consolidation\SiteAlias\SiteAliasManagerAwareInterface;
use Consolidation\SiteAlias\SiteAliasManagerAwareTrait;
use Drupal\gdpr_dumper\Sql\GdprSqlBase;
use Drush\Attributes as CLI;
use Drush\Boot\DrupalBootLevels;
use Drush\Commands\DrushCommands;

/**
 * Class SqlSyncCommands
 * @package Drupal\gdpr_dumper\Commands
 */
final class SqlCommands extends DrushCommands implements SiteAliasManagerAwareInterface
{
    use SiteAliasManagerAwareTrait;

    const DUMP_GDPR = 'sql:dump-gdpr';

    /**
     * Exports the Drupal DB as SQL using druidfi/gdpr-mysqldump.
     *
     * --createdb is used by sql-sync, since including the DROP TABLE statements interferes with the import when the database is created.
     *
     * @see \Drush\Commands\sql\SqlCommands::dump
     */
    #[CLI\Command(name: self::DUMP_GDPR, aliases: ['sql-dump-gdpr'])]
    #[CLI\Bootstrap(level: DrupalBootLevels::MAX, max_level: DrupalBootLevels::CONFIGURATION)]
    #[CLI\OptionsetSql]
    #[CLI\OptionsetTableSelection]
    #[CLI\Option(name: 'result-file', description: "Save to a file. The file should be relative to Drupal root. If --result-file is provided with the value 'auto', a date-based filename will be created under ~/drush-backups directory.")]
    #[CLI\Option(name: 'create-db', description: 'Omit DROP TABLE statements. Used by Postgres and Oracle only.')]
    #[CLI\Option(name: 'data-only', description: 'Dump data without statements to create any of the schema.')]
    #[CLI\Option(name: 'ordered-dump', description: 'Order by primary key and add line breaks for efficient diffs. Slows down the dump. Mysql only.')]
    #[CLI\Option(name: 'gzip', description: 'Compress the dump using the gzip program which must be in your <info>$PATH</info>.')]
    #[CLI\Option(name: 'extra', description: 'Add custom arguments/options when connecting to database (used internally to list tables).')]
    #[CLI\Option(name: 'extra-dump', description: 'Add custom arguments/options to the dumping of the database (e.g. <info>mysqldump</info> command).')]
    #[CLI\Usage(name: 'drush sql:dump-gdpr --result-file=../18.sql', description: 'Save SQL dump to the directory above Drupal root.')]
    #[CLI\Usage(name: 'drush sql:dump-gdpr --skip-tables-key=common', description: 'Skip standard tables. See [Drush configuration](../../using-drush-configuration)')]
    #[CLI\Usage(name: 'drush sql:dump-gdpr --extra-dump=--no-data', description: 'Pass extra option to <info>mysqldump</info> command.')]
    #[CLI\FieldLabels(labels: ['path' => 'Path'])]
    public function dump($options = ['result-file' => self::REQ, 'create-db' => false, 'data-only' => false, 'ordered-dump' => false, 'gzip' => false, 'extra' => self::REQ, 'extra-dump' => self::REQ, 'format' => 'null']): PropertyList
    {
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
