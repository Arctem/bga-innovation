<?php

namespace Innovation\Cards\Echoes;

use Innovation\Cards\Card;
use Innovation\Enums\CardTypes;
use Innovation\Enums\Directions;

class Card351 extends Card
{

  // Toothbrush
  // - 3rd edition:
  //   - ECHO: Tuck all cards of one present value from your hand.
  //   - You may splay any one color of your cards left.
  //   - If the [1] deck has at least one card, you may transfer its bottom card to the available achievements.
  // - 4th edition:
  //   - ECHO: Tuck all cards of one present value from your hand.
  //   - You may splay any one color of your cards left.
  //   - You may junk all cards in the [2] deck. If you do, achieve the highest junked card if eligible.

  public function initialExecution()
  {
    if (self::isEcho()) {
      $values = self::getUniqueValues('hand');
      if (count($values) > 0) {
        self::setMaxSteps(2);
        self::setAuxiliaryArray($values);
      }
    } else if (self::isFirstNonDemand() || self::getBaseDeckCount(self::isFirstOrThirdEdition() ? 1 : 2) > 0) {
      self::setMaxSteps(1);
    }
  }

  public function getInteractionOptions(): array
  {
    if (self::isEcho()) {
      if (self::isFirstInteraction()) {
        return [
          'choose_value' => true,
          'age'          => self::getAuxiliaryArray(),
        ];
      } else {
        return [
          'n'             => 'all',
          'location_from' => 'hand',
          'tuck_keyword'  => true,
          'age'           => self::getAuxiliaryValue(),
        ];
      }
    } else if (self::isFirstNonDemand()) {
      return [
        'can_pass'        => true,
        'splay_direction' => Directions::LEFT,
      ];
    } else if (self::isFirstInteraction()) {
      return [
        'can_pass' => true,
        'choices'  => [1],
      ];
    } else {
      return [
        'location_from'       => 'junk',
        'age'                 => self::getMaxValueInLocation('junk'),
        'achieve_if_eligible' => true
      ];
    }
  }

  protected function getPromptForListChoice(): array
  {
    if (self::isFirstOrThirdEdition()) {
      return self::buildPromptFromList([
        1 => [clienttranslate('Transfer bottom ${age} to the available achievements'), 'age' => self::renderValueWithType(1, CardTypes::BASE)],
      ]);
    } else {
      return self::buildPromptFromList([
        1 => [clienttranslate('Junk ${age} deck'), 'age' => self::renderValueWithType(2, CardTypes::BASE)],
      ]);
    }
  }

  public function handleSpecialChoice(int $choice)
  {
    if (self::isEcho()) {
      self::setAuxiliaryValue($choice);
    } else if (self::isFirstOrThirdEdition()) {
      $this->game->executeDraw(0, /*age=*/2, 'achievements', /*bottom_to=*/false, 0, /*bottom_from=*/true);
    } else {
      self::junkBaseDeck(2);
      self::setMaxSteps(2);
    }
  }

}