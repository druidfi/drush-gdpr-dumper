<?php

namespace Drupal\gdpr_dumper\Sql;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Database\Database;
use Drupal\gdpr_dumper\Event\GdprDumperEvents;
use Drupal\gdpr_dumper\Event\GdprExpressionsEvent;
use Drupal\gdpr_dumper\Event\GdprReplacementsEvent;
use Drush\Drush;
use Drush\Sql\SqlBase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class GdprSqlBase
 * @package Drupal\gdpr_dumper\Sql
 */
class GdprSqlBase extends SqlBase
{
    /**
     * {@inheritdoc}
     */
    public static function create(array $options = []): ?SqlBase
    {
        // Set defaults in the unfortunate event that caller doesn't provide values.
        $options += [
            'database' => 'default',
            'target' => 'default',
            'db-url' => null,
            'databases' => null,
            'db-prefix' => null,
        ];
        $database = $options['database'];
        $target = $options['target'];
        $event_dispatcher = \Drupal::service('event_dispatcher');

        if ($url = $options['db-url']) {
            $url = is_array($url) ? $url[$database] : $url;
            $db_spec = static::dbSpecFromDbUrl($url);
            $db_spec['prefix'] = $options['db-prefix'];
            return static::getInstance($db_spec, $options, $event_dispatcher);
        }
        elseif (($databases = $options['databases']) && (array_key_exists($database, $databases)) && (array_key_exists($target, $databases[$database]))) {
            // @todo 'databases' option is not declared anywhere?
            $db_spec = $databases[$database][$target];
            return static::getInstance($db_spec, $options, $event_dispatcher);
        }
        elseif ($info = Database::getConnectionInfo($database)) {
            $db_spec = $info[$target];
            return static::getInstance($db_spec, $options, $event_dispatcher);
        }
        else {
            throw new \Exception(dt('Unable to load Drupal settings. Check your --root, --uri, etc.'));
        }
    }

    public static function getInstance($db_spec, $options, EventDispatcherInterface $event_dispatcher = null): ?self
    {
        $driver = $db_spec['driver'];
        $class_name = 'Drupal\gdpr_dumper\Sql\GdprSql' . ucfirst($driver);
        if (class_exists($class_name)) {
            $instance = new $class_name($db_spec, static::getGdprSettings($options, $event_dispatcher));
            // Inject config
            $instance->setConfig(Drush::config());
            return $instance;
        }
        return null;
    }

    private static function getGdprSettings(array $options, EventDispatcherInterface $event_dispatcher = null): array
    {
        // Fetch module settings.
        $config = \Drupal::config('gdpr_dumper.settings');

        if (empty($options['extra-dump']) || !str_contains($options['extra-dump'], '--gdpr-expressions')) {
            // Dispatch event so the expressions can be altered.
            $event = new GdprExpressionsEvent($config->get('gdpr_expressions'));
            $event_dispatcher->dispatch($event, GdprDumperEvents::GDPR_EXPRESSIONS);
            // Add the configured GDPR expressions to the command.
            if ($expressions = Json::encode($event->getExpressions())) {
                $options['extra-dump'] .= " --gdpr-expressions='$expressions'";
            }
        }

        if (empty($options['extra-dump']) || !str_contains($options['extra-dump'], '--gdpr-replacements')) {
            // Dispatch event so the replacements can be altered.
            $event = new GdprReplacementsEvent($config->get('gdpr_replacements'));
            $event_dispatcher->dispatch($event, GdprDumperEvents::GDPR_REPLACEMENTS);
            // Add the configured GDPR replacements to the command.
            if ($replacements = Json::encode($event->getReplacements())) {
                $options['extra-dump'] .= " --gdpr-replacements='$replacements'";
            }
        }

        return $options;
    }

}
