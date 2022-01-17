<?php

namespace Drupal\gdpr_dumper\Event;

/**
 * Defines events for the gdpr_dumper module.
 */
final class GdprDumperEvents
{
  /**
   * Name of the event fired building the GDPR expressions.
   *
   * @Event
   *
   * @see \Drupal\gdpr_dumper\Event\GdprExpressionsEvent
   */
  const GDPR_EXPRESSIONS = 'gdpr_dumper.expressions';

  /**
   * Name of the event fired building the GDPR replacements.
   *
   * @Event
   *
   * @see \Drupal\gdpr_dumper\Event\GdprReplacementsEvent
   */
  const GDPR_REPLACEMENTS = 'gdpr_dumper.replacements';

}
