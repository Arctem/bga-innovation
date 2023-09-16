<?php

namespace Innovation\Cards\Unseen;

use Innovation\Cards\Card;
use Innovation\Enums\Colors;

class Card559 extends Card
{

  // Handbag:
  //   - You may choose to either transfer the bottom card of each color from your board to your
  //     hand, or tuck all cards from your score pile, or choose a value and score all cards from
  //     your hand of that value.

  public function initialExecution()
  {
    self::setMaxSteps(1);
  }

  public function getInteractionOptions(): array
  {
    if (self::isFirstInteraction()) {
      return ['choices' => [1, 2, 3]];
    } else {
      if (self::getAuxiliaryValue() === 2) {
        return [
          'n'             => 'all',
          'location_from' => 'score',
          'tuck_keyword'  => true,
        ];
      } else {
        return ['choose_value' => true];
      }
    }
  }

  protected function getPromptForListChoice(): array
  {
    return self::buildPromptFromList([
      1 => clienttranslate('Transfer your bottom cards to your hand'),
      2 => clienttranslate('Tuck all cards from your score pile'),
      3 => clienttranslate('Score all cards from your hand of a specific value'),
    ]);
  }

  public function handleListChoice(int $choice): void
  {
    if ($choice === 1) {
      foreach (Colors::ALL as $color) {
        self::transferToHand(self::getBottomCardOfColor($color));
      }
    } else {
      self::setAuxiliaryValue($choice);
      self::setMaxSteps(2);
    }
  }

  public function handleValueChoice(int $value)
  {
    $this->notifications->notifyValueChoice($value, self::getPlayerId());
    foreach (self::getCardsKeyedByValue('hand')[$value] as $card) {
      self::score($card);
    }
  }

}