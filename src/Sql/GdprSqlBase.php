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
     * @throws \Exception
     */
    public static function create(array $options = []): SqlBase
    {
        // Set defaults in the unfortunate event that caller doesn't provide values.
        $options += [
            'database' => 'default',
            'target' => 'default',
            'db-url' => NULL,
            'databases' => NULL,
            'db-prefix' => NULL,
        ];
        $database = $options['database'];
        $target = $options['target'];
        $event_dispatcher = \Drupal::service('event_dispatcher');

        if ($url = $options['db-url']) {
            $url = is_array($url) ? $url[$database] : $url;
            $db_spec = self::dbSpecFromDbUrl($url);
            $db_spec['db_prefix'] = $options['db-prefix'];
            return self::getInstance($db_spec, $options, $event_dispatcher);
        }
        elseif (($databases = $options['databases']) && (array_key_exists($database, $databases)) && (array_key_exists($target, $databases[$database]))) {
            $db_spec = $databases[$database][$target];
            return self::getInstance($db_spec, $options, $event_dispatcher);
        }
        elseif ($info = Database::getConnectionInfo($database)) {
            $db_spec = $info[$target];
            return self::getInstance($db_spec, $options, $event_dispatcher);
        }
        else {
            throw new \Exception(dt('Unable to load Drupal settings. Check your --root, --uri, etc.'));
        }
    }

    public static function getInstance($db_spec, $options, EventDispatcherInterface $event_dispatcher = NULL): SqlBase
    {
        $driver = $db_spec['driver'];
        $className = 'Drupal\gdpr_dumper\Sql\GdprSql' . ucfirst($driver);
        // Fetch module settings.
        $config = \Drupal::config('gdpr_dumper.settings');

        if (empty($options['extra-dump']) || strpos($options['extra-dump'], '--gdpr-expressions') === FALSE) {

            // Dispatch event so the expressions can be altered.
            $event = new GdprExpressionsEvent($config->get('gdpr_expressions'));
            $event_dispatcher->dispatch(GdprDumperEvents::GDPR_EXPRESSIONS, $event);
            // Add the configured GDPR expressions to the command.
            if($expressions = Json::encode($event->getExpressions())){
                $options['extra-dump'] .= " --gdpr-expressions='$expressions'";
            }
        }

        if (empty($options['extra-dump']) || strpos($options['extra-dump'], '--gdpr-replacements') === FALSE) {
            // Dispatch event so the replacements can be altered.
            $event = new GdprReplacementsEvent($config->get('gdpr_replacements'));
            $event_dispatcher->dispatch(GdprDumperEvents::GDPR_REPLACEMENTS, $event);
            // Add the configured GDPR replacements to the command.
            if ($replacements = Json::encode($event->getReplacements())){
                $options['extra-dump'] .= " --gdpr-replacements='$replacements'";
            }
        }

        $instance = new $className($db_spec, $options);
        $driver_options = isset($config->get('drivers')[$driver]) ? $config->get('drivers')[$driver] : [];
        // Inject config
        $instance->setConfig(Drush::config());
        $instance->setDriverOptions($driver_options);
        return $instance;
    }

}
