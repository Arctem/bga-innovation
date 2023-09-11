<?php

namespace Innovation\Cards\Artifacts;

use Innovation\Cards\Card;
use Innovation\Enums\Colors;
use Innovation\Enums\Locations;

class Card453 extends Card
{

  // Pizza Pacaya
  //   - I COMPEL you to junk all cards from your board! Draw and meld a card of each value in ascending order!

  public function initialExecution()
  {
    foreach (Colors::ALL as $color) {
      foreach (self::getCardsKeyedByColor(Locations::BOARD)[$color] as $card) {
        self::junk($card);
      }
    }
    for ($i = 1; $i <= 11; $i++) {
      self::drawAndMeld($i);
    }
  }

}