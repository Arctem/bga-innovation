<?php

namespace Innovation\Cards\Unseen;

use Innovation\Cards\Card;

class Card582 extends Card
{

  // Whatchamacallit:
  //   - For each value, in ascending order, if that value is not a value of a top card on your board or a card in your score pile, draw and score a card of that value.

  public function initialExecution()
  {
    $values = [];
    foreach ($this->game->getTopCardsOnBoard(self::getPlayerId()) as $card) {
      if (!in_array($card['faceup_age'], $values)) {
        $values[] = $card['faceup_age'];
      }
    }
    foreach ($this->game->getCardsInScorePile(self::getPlayerId()) as $card) {
      if (!in_array($card['age'], $values)) {
        $values[] = $card['age'];
      }
    }
    // For each value, in ascending order, if that value is not a value of a top card on your board or a card in your score pile, draw and score a card of that value.
    for ($i = 1; $i <= 11; $i++) {
      if (!in_array($i, $values)) {
        self::drawAndScore($i);
      }
    }
  }

}