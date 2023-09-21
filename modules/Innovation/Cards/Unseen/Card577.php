<?php

namespace Innovation\Cards\Unseen;

use Innovation\Cards\AbstractCard;
use Innovation\Enums\Colors;
use Innovation\Enums\Directions;

class Card577 extends AbstractCard
{

  // Fight Club:
  //   - I DEMAND you transfer one of your secrets to my achievements!
  //   - You may splay your yellow cards up.

  public function initialExecution()
  {
    self::setMaxSteps(1);
  }

  public function getInteractionOptions(): array
  {
    if (self::isDemand()) {
      return [
        'owner_from'    => self::getPlayerId(),
        'location_from' => 'safe',
        'owner_to'      => self::getLauncherId(),
        'location_to'   => 'achievements',
      ];
    } else {
      return [
        'can_pass'        => true,
        'splay_direction' => Directions::UP,
        'color'           => [Colors::YELLOW],
      ];
    }
  }

}