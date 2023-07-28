<?php

namespace Innovation\Cards\Base;

use Innovation\Cards\Card;

class Card3 extends Card
{

  // Archery:
  // - 3rd edition:
  //   - I DEMAND you draw a [1], then transfer the highest card in your hand to my hand!
  // - 4th edition:
  //   - I DEMAND you draw a [1], then transfer the highest card in your hand to my hand!
  //   - Junk an available achievement of value [1] or [2].

  public function initialExecution()
  {
    if (self::isDemand()) {
      self::draw(1);
    }
    self::setMaxSteps(1);
  }

  public function getInteractionOptions(): array
  {
    if (self::isDemand()) {
      return [
        'location_from' => 'hand',
        'owner_to'      => self::getLauncherId(),
        'location_to'   => 'hand',
        'age'           => self::getMaxValueInLocation('hand'),
      ];
    } else {
      return [
        'owner_from'    => 0,
        'location_from' => 'achievements',
        'location_to'   => 'junk',
        'age_min'       => 1,
        'age_max'       => 2,
      ];
    }
  }
}