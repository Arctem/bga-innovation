<?php

namespace Innovation\Cards\Echoes;

use Innovation\Cards\Card;

class Card361 extends Card
{

  // Deoderant
  // - 3rd edition:
  //   - ECHO: Draw and meld a [3].
  //   - If you have a top card with a [AUTHORITY], draw and meld a [3]. Otherwise, draw a [4].
  // - 4th edition:
  //   - ECHO: Draw and meld a [3].
  //   - If you have a top card with a [AUTHORITY], draw and meld a [3]. Otherwise, draw a [4].
  //   - If you have a top card with a [INDUSTRY], junk all cards in the [4] deck.

  public function initialExecution()
  {
    if (self::isEcho()) {
      self::drawAndMeld(3);
    } else if (self::getEffectNumber() === 1) {
      $hasAuthority = false;
      $hasIndustry = false;
      foreach (self::getTopCards() as $card) {
        if (self::hasIcon($card, $this->game::AUTHORITY)) {
          $hasAuthority = true;
        }
        if (self::hasIcon($card, $this->game::INDUSTRY)) {
          $hasIndustry = true;
        }
      }
      if ($hasAuthority) {
        self::drawAndMeld(3);
      } else {
        self::draw(4);
      }
      if ($hasIndustry) {
        self::junkBaseDeck(4);
      }
    }
  }

}