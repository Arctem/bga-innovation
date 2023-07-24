<?php

namespace Innovation\Cards\Unseen;

use Innovation\Cards\Card;

class Card498 extends Card
{

  // Password:
  //   - Draw and reveal a [2]. You may safeguard another card from your hand of the color of the
  //     drawn card. If you do, score the drawn card. Otherwise, return all cards from your hand
  //     except the drawn card.

  public function initialExecution()
  {
    $card = self::putInHand(self::drawAndReveal(2));
    self::setAuxiliaryValue($card['id']);
    self::setMaxSteps(1);
  }

  public function getInteractionOptions(): array
  {
    $drawnCardId = self::getAuxiliaryValue();
    if (self::getCurrentStep() === 1) {
      return [
        'can_pass'      => true,
        'location_from' => 'hand',
        'location_to'   => 'safe',
        'not_id'        => $drawnCardId,
        'color'         => [self::getCard($drawnCardId)['color']],
      ];
    } else {
      return [
        'n'             => 'all',
        'location_from' => 'hand',
        'location_to'   => 'deck',
        'not_id'        => $drawnCardId,
      ];
    }
  }

  public function afterInteraction()
  {
    if (self::getNumChosen() === 0) {
      self::setMaxSteps(2);
    } else {
      self::score(self::getCard(self::getAuxiliaryValue()));
    }
  }

  public function handleAbortedInteraction()
  {
    // If the interaction was aborted due to a full safe, the other cards must be returned.
    self::setMaxSteps(2);
  }

}