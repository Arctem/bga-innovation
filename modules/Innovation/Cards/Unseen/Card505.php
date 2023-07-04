<?php

namespace Innovation\Cards\Unseen;

use Innovation\Cards\Card;

class Card505 extends Card
{

  // Brethren of Purity:
  //   - Draw and meld a [3] or a card of value one higher than the last card melded due to
  //     Brethren of Purity during this action. If you meld over a card with a [CONCEPT], repeat
  //     this effect.

  public function initialExecution()
  {
    self::setMaxSteps(1);
  }

  public function getInteractionOptions(): array
  {
    // The array will either contain a single value (if a card has been melded due to Brethren of
    // Purity during this action) or it will be empty.
    $array = self::getActionScopedAuxiliaryArray();
    $lastValue = empty($array) ? null : $array[0];
    return [
      'choose_value' => true,
      'age' => $lastValue === null ? [3] : array_unique([3, $lastValue + 1]),
    ];
  }

  public function handleSpecialChoice(int $value) {
    $card = self::drawAndMeld($value);
    self::setActionScopedAuxiliaryArray([$card['age']]);
    $pile = $this->game->getCardsInLocationKeyedByColor(self::getPlayerId(), 'board')[$card['color']];
    $numCards = count($pile);
    if ($numCards >= 2 && $this->game->hasRessource($pile[$numCards - 2], $this->game::CONCEPT)) {
      self::setNextStep(1);
    }
  }

}