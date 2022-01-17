<?php

namespace Drupal\gdpr_dumper\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class GdprExpressionsEvent
 * @package Drupal\gdpr_dumper\Event
 */
class GdprExpressionsEvent extends Event
{
    protected array $expressions;

    public function __construct($expressions)
    {
        $this->expressions = $expressions;
    }

    public function getExpressions(): array
    {
        return $this->expressions;
    }

    public function setExpressions(array $expressions): GdprExpressionsEvent
    {
        $this->expressions = $expressions;
        return $this;
    }

}
