<?php

namespace Innovation\Cards\Base;

use Innovation\Cards\AbstractCard;

class Card440 extends AbstractCard
{

  // Climatology:
  //   - I DEMAND you return two top cards from your board each with the icon of my choice other
  //     than [HEALTH]!
  //   - Return a top card from your board. Return all cards in your score pile of equal or higher
  //     value than the top card.

  public function initialExecution()
  {
    self::setMaxSteps(self::isDemand() ? 3 : 2);
  }

  public function getInteractionOptions(): array
  {
    if (self::isDemand()) {
      if (self::isFirstInteraction()) {
        return [
          'player_id'        => self::getLauncherId(),
          'choose_icon_type' => true,
          'icon'             => [1, 3, 4, 5, 6, 7],
        ];
      } else {
        return [
          'location_from'  => 'board',
          'return_keyword' => true,
          'with_icon'      => self::getAuxiliaryValue(),
        ];
      }
    }
    if (self::isFirstInteraction()) {
      return [
        'location_from'  => 'board',
        'return_keyword' => true,
      ];
    } else {
      return [
        'location_from'  => 'score',
        'return_keyword' => true,
        'age_min'        => self::getAuxiliaryValue(),
        'n'              => 'all',
      ];
    }
  }

  public function afterInteraction()
  {
    if (self::isNonDemand() && self::isFirstInteraction()) {
      $minAgeToReturn = 0;
      if (self::getNumChosen() > 0) {
        $minAgeToReturn = self::getLastSelectedAge();
      }
      self::setAuxiliaryValue($minAgeToReturn);
    }
  }

  public function handleIconChoice(int $icon)
  {
    $this->notifications->notifyIconChoice($icon, self::getPlayerId());
    self::setAuxiliaryValue($icon);
  }
}