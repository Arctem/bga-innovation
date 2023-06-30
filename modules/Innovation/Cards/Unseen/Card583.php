<?php

namespace Innovation\Cards\Unseen;

use Innovation\Cards\Card;

class Card583 extends Card
{

  // 3D Printing:
  //   - Return a top or bottom card on your board. Achieve one of your secrets of value equal to
  //     the returned card regardless of eligibility, then safeguard an available standard
  //     achievement. If you do, repeat this effect.

  public function initialExecution()
  {
    self::setMaxSteps(4);
  }

  public function getInteractionOptions(): array
  {
    if (self::getCurrentStep() == 1) {
      // Skip the first interaction if no color has more than 1 card on the board
      $needsToChoose = false;
      $cardCounts = $this->game->countCardsInLocationKeyedByAge(self::getPlayerId(), 'board');
      for ($i = 1; $i <= 11; $i++) {
        if ($cardCounts[$i] > 1) {
          $needsToChoose = true;
          break;
        }
      }
      return ['choices' => $needsToChoose ? [1, 2] : [1]];
    } else if (self::getCurrentStep() == 2) {
      $returnBottomCard = self::getAuxiliaryValue() == 2;
      self::setAuxiliaryValue(0); // Track which value was returned
      return [
        'location_from' => 'board',
        'bottom_from'   => $returnBottomCard,
        'location_to'   => 'deck',
      ];
    } else if (self::getCurrentStep() == 3) {
      $value = self::getAuxiliaryValue();
      self::setAuxiliaryValue(0); // Track how many cards were transferred in the 2nd sentence of the effect
      return [
        'location_from' => 'safe',
        'location_to'   => 'achievements',
        'age'           => $value,
      ];
    } else {
      return [
        'location_from' => 'achievements',
        'owner_from'    => 0,
        'location_to'   => 'safe',
      ];
    }
  }

  public function getSpecialChoicePrompt(): array
  {
    if (self::getCurrentStep() == 1) {
      return self::getPromptForChoiceFromList([
        1 => clienttranslate('Return top card'),
        2 => clienttranslate('Return bottom card'),
      ]);
    } else {
      return self::getPromptForValueChoice();
    }
  }

  public function handleSpecialChoice($choice)
  {
    self::setAuxiliaryValue($choice);
  }

  public function handleCardChoice($cardId)
  {
    if (self::getCurrentStep() == 2) {
      self::setAuxiliaryValue(self::getLastSelectedAge());
    } else {
      self::setAuxiliaryValue(self::getAuxiliaryValue() + 1);
    }
  }

  public function afterInteraction()
  {
    if (self::getCurrentStep() == 4 && self::getAuxiliaryValue() == 2) {
      self::setNextStep(1);
    }
  }

}