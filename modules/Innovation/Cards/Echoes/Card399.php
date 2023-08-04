<?php

namespace Innovation\Cards\Echoes;

use Innovation\Cards\Card;

class Card399 extends Card
{

  // Jeans
  // - 3rd edition
  //   - ECHO: Draw two [9]. Return one, foreshadow the other.
  //   - Choose two different values less than [7]. Draw and reveal a card of each value. Meld one,
  //     and return the other.
  // - 4th edition
  //   - ECHO: Draw two [8]. Return one, foreshadow the other.
  //   - Draw and reveal two cards of value equal to your top blue card. Meld one, and return the other.
  //   - Junk all cards in the [7] or [8] deck.
  //   - If Jeans was foreseen, transfer a valued junked card to your hand.

  public function initialExecution()
  {
    if (self::isEcho()) {
      $valueToDraw = self::isFirstOrThirdEdition() ? 9 : 8;
      $card1 = self::draw($valueToDraw);
      $card2 = self::draw($valueToDraw);
      self::setAuxiliaryArray([$card1['id'], $card2['id']]);
      self::setMaxSteps(1);
    } else if (self::isFirstNonDemand()) {
      if (self::isFourthEdition()) {
        $topBlueCard = self::getTopCardOfColor($this->game::BLUE);
        $value = $topBlueCard ? $topBlueCard['faceup_age'] : 0;
        self::setAuxiliaryValue($value); // Track first value to draw and reveal
        self::setAuxiliaryValue2($value); // Track second value to draw and reveal
        self::setNextStep(3);
      }
      self::setMaxSteps(4);
    } else if (self::wasForeseen()) {
      self::setMaxSteps(1);
    }
  }

  public function getInteractionOptions(): array
  {
    if (self::isEcho()) {
      return [
        'location_from'                   => 'hand',
        'return_keyword'                  => true,
        'card_ids_are_in_auxiliary_array' => true,
      ];
    } else if (self::isFirstNonDemand()) {
      if (self::getCurrentStep() === 1) {
        return [
          'choose_value' => true,
          'age'          => [1, 2, 3, 4, 5, 6],
        ];
      } else if (self::getCurrentStep() === 2) {
        return [
          'choose_value' => true,
          'age'          => array_diff([1, 2, 3, 4, 5, 6], [self::getAuxiliaryValue()])
        ];
      } else if (self::getCurrentStep() === 3) {
        self::drawAndReveal(self::getAuxiliaryValue());
        self::drawAndReveal(self::getAuxiliaryValue2());
        return [
          'location_from' => 'revealed',
          'meld_keyword'  => true,
        ];
      } else {
        return [
          'location_from'  => 'revealed',
          'return_keyword' => true,
        ];
      }
    } else {
      return [
        'location_from' => 'junk',
        'location_to'   => 'hand',
      ];
    }
  }

  public function handleCardChoice(array $card)
  {
    if (self::isEcho()) {
      $cardIds = self::getAuxiliaryArray();
      $cardIdToForeshadow = $cardIds[0] == $card['id'] ? $cardIds[1] : $cardIds[0];
      self::foreshadow(self::getCard($cardIdToForeshadow));
    }
  }

  public function handleSpecialChoice($value)
  {
    if (self::getCurrentStep() === 1) {
      self::setAuxiliaryValue($value);
    } else {
      self::setAuxiliaryValue2($value);
    }
  }

}