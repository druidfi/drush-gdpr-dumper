<?php

namespace Drupal\gdpr_dumper\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class GdprReplacementsEvent
 * @package Drupal\gdpr_dumper\Event
 */
class GdprReplacementsEvent extends Event
{
    protected array $replacements;

    public function __construct($replacements)
    {
        $this->replacements = $replacements;
    }

    public function getReplacements(): array
    {
        return $this->replacements;
    }

    public function setReplacements(array $replacements): GdprReplacementsEvent
    {
        $this->replacements = $replacements;
        return $this;
    }

}
