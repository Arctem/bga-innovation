<?php

namespace Innovation\Cards\Echoes;

use Innovation\Cards\AbstractCard;
use Innovation\Enums\Colors;
use Innovation\Enums\Icons;

class Card378 extends AbstractCard
{

  // Octant
  // - 3rd edition:
  //   - I DEMAND you transfer a top non-red card with a [HEALTH] or [INDUSTRY] from your board to
  //     my board! If you do, draw and foreshadow a [6]!
  //   - Draw and foreshadow a [6].
  // - 4th edition:
  //   - I DEMAND you transfer your top card with [HEALTH] or [INDUSTRY] of each non-red color to
  //     my board! If you do, and Octant wasn't foreseen, draw and foreshadow a [6]!
  //   - Draw and foreshadow a [6].

  public function initialExecution()
  {
    if (self::isDemand()) {
      self::setMaxSteps(1);
    } else {
      self::drawAndForeshadow(6);
    }
  }

  public function getInteractionOptions(): array
  {
    return [
      'n'             => self::isFirstOrThirdEdition() ? 1 : 'all',
      'location_from' => 'board',
      'owner_to'      => self::getLauncherId(),
      'location_to'   => 'board',
      'color'         => Colors::NON_RED,
      'with_icons'    => [Icons::HEALTH, Icons::INDUSTRY],
    ];
  }

  public function afterInteraction()
  {
    if (self::getNumChosen() > 0 && self::isFourthEdition() && !self::wasForeseen()) {
      self::drawAndForeshadow(6);
    }
  }

}