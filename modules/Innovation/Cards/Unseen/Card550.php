<?php

namespace Innovation\Cards\Unseen;

use Innovation\Cards\Card;
use Innovation\Cards\ExecutionState;
use SebastianBergmann\Type\VoidType;

class Card550 extends Card
{

  // Plot Voucher
  //   - Meld a card from your score pile. Safeguard the lowest available standard achievement. 
  //     If you do, fully execute the melded card if it is your turn, otherwise self-execute it.

  public function initialExecution()
  {
    self::setAuxiliaryValue(-1);
    self::setMaxSteps(2);
  }

  public function getInteractionOptions(): array
  {

    if (self::getCurrentStep() == 1) {
      return [
        'location_from' => 'score',
        'location_to'   => 'board',
        'meld_keyword'  => true,
      ];
    } else {
      return [
        'owner_from'    => 0,
        'location_from' => 'achievements',
        'location_to'   => 'safe',
        'age'           => self::getLowestAvailableAchievementValue(),
      ];
    }
  }

  public function handleCardChoice(int $cardId)
  {
    if (self::getCurrentStep() == 1) {
      self::setAuxiliaryValue(self::getLastSelectedId());
    } else {
      $secret = self::getLastSelectedCard();
      // Make sure the card is actually in the safe (the safe could have been full)
      if ($secret['location'] == 'safe' && $secret['owner'] == self::getPlayerId()) {
        $meldedCard = self::getCard(self::getAuxiliaryValue());
        if (self::getPlayerId() == self::getLauncherId()) {
          self::fullyExecute($meldedCard);
        } else {
          self::selfExecute($meldedCard);
        }
      }
    }
  }

  private function getLowestAvailableAchievementValue(): int
  {
    return $this->game->getMinOrMaxAgeInLocation(0, 'achievements', 'MIN');
  }

}