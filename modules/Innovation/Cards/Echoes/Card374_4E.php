<?php

namespace Innovation\Cards\Echoes;

use Innovation\Cards\AbstractCard;
use Innovation\Enums\Locations;

class Card374_4E extends AbstractCard
{

  // Toilet (3rd edition):
  //  - ECHO: Draw and tuck a [4].
  //  - I DEMAND you return a card from your score pile matching each different bonus value on my board!
  //  - You may return a card in your hand and draw a card of the same value.

  public function initialExecution()
  {
    if (self::isEcho()) {
      self::drawAndTuck(4);
    } else if (self::isDemand()) {
      $values = array_unique(self::getBonuses());
      if ($values) {
        self::setAuxiliaryArray($values); // Store the values to be returned
        self::setMaxSteps(1);
      }
    } else if (self::isFirstNonDemand()) {
      self::setMaxSteps(1);
    }
  }

  public function getInteractionOptions(): array
  {
    if (self::isDemand()) {
      $values = self::getAuxiliaryArray();
      return [
        'n'                 => count($values),
        'choose_value'      => true,
        'age'               => $values,
        'refresh_selection' => true,
      ];
    } else {
      return [
        'can_pass'       => true,
        'location_from'  => Locations::HAND,
        'return_keyword' => true,
      ];
    }
  }

  public function handleCardChoice(array $card)
  {
    if (self::isNonDemand()) {
      self::draw(self::getValue($card));
    } else if (self::isDemand()) {
      self::removeFromAuxiliaryArray(self::getValue($card));
    }
  }

}