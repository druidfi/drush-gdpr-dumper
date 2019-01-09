<?php

namespace Drupal\gdpr_dumper\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class GdprExpressionsEvent
 * @package Drupal\gdpr_dumper\Event
 */
class GdprExpressionsEvent extends Event {

  /**
   * @var array
   */
  protected $expressions;


  /**
   * GdprExpressionsEvent constructor.
   * @param $expressions
   */
  public function __construct($expressions) {
    $this->expressions = $expressions;
  }

  /**
   * @return array
   */
  public function getExpressions() {
    return $this->expressions;
  }

  /**
   * @param array $expressions
   * @return $this
   */
  public function setExpressions(array $expressions) {
    $this->expressions = $expressions;
    return $this;
  }

}
