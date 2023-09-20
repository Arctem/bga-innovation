<?php

namespace Innovation\Cards\Echoes;

use Innovation\Cards\Card;
use Innovation\Enums\Directions;

class Card370 extends Card
{

  // Globe
  // - 3rd edition:
  //   - You may return up to three cards from hand of the same color. If you return one, splay
  //     any color left; two, right; three, up. If you returned at least one card, draw and
  //     foreshadow a [6].
  // - 4th edition:
  //   - You may return all cards from your hand. If you return three cards, splay any color on
  //     your board right, and draw and foreshadow a [6], a [7], and then an [8].
  //   - If Globe was foreseen, foreshadow a top card from any board.

  public function initialExecution()
  {
    if (self::isFirstNonDemand()) {
      self::setMaxSteps(1);
    } else if (self::wasForeseen()) {
      self::setMaxSteps(1);
    }
  }

  public function getInteractionOptions(): array
  {
    return self::isFirstOrThirdEdition() ? self::getFirstAndThirdEditionInteractionOptions() : self::getFourthInteractionOptions();
  }

  private function getFirstAndThirdEditionInteractionOptions(): array
  {
    if (self::isFirstInteraction()) {
      return [
        'can_pass'     => true,
        'choose_color' => true,
        'color'        => self::getUniqueColors('hand'),
      ];
    } else if (self::isSecondInteraction()) {
      return [
        'can_pass'      => true,
        'n_min'         => 1,
        'n_max'         => 3,
        'location_from' => 'hand',
        'location_to'   => 'revealed,deck',
        'color'         => [self::getAuxiliaryValue()],
      ];
    } else {
      return ['splay_direction' => self::getAuxiliaryValue()];
    }
  }

  private function getFourthInteractionOptions(): array
  {
    if (self::isFirstNonDemand()) {
      if (self::isFirstInteraction()) {
        return [
          'can_pass'       => true,
          'n'              => 'all',
          'location_from'  => 'hand',
          'return_keyword' => true,
        ];
      } else {
        return ['splay_direction' => Directions::RIGHT];
      }
    } else {
      return [
        'location_from'      => 'board',
        'owner_from'         => 'any player',
        'foreshadow_keyword' => true,
      ];
    }
  }

  public function handleColorChoice(int $color)
  {
    self::setAuxiliaryValue($color); // Track color being returned
    self::setMaxSteps(2);
  }

  public function afterInteraction()
  {
    if (self::isFirstOrThirdEdition()) {
      if (self::isSecondInteraction() && self::getNumChosen() > 0) {
        self::setAuxiliaryValue(self::getNumChosen()); // Repurpose auxiliary value to store the number of cards returned
        self::setMaxSteps(3);
      } else if (self::isThirdInteraction() && self::getAuxiliaryValue() > 0) {
        self::drawAndForeshadow(6);
      }
    } else if (self::isFirstNonDemand()) {
      if (self::isFirstInteraction() && self::getNumChosen() >= 3) {
        self::setMaxSteps(2);
      } else if (self::isSecondInteraction()) {
        self::drawAndForeshadow(6);
        self::drawAndForeshadow(7);
        self::drawAndForeshadow(8);
      }
    }
  }

  public function handleAbortedInteraction()
  {
    // TODO(LATER): Deduplicate this code with the above.
    if (self::isFirstOrThirdEdition() && self::isThirdInteraction() && self::getAuxiliaryValue() > 0) {
      self::drawAndForeshadow(6);
    } else if (self::isFourthEdition() && self::isSecondInteraction()) {
      self::drawAndForeshadow(6);
      self::drawAndForeshadow(7);
      self::drawAndForeshadow(8);
    }
  }

}