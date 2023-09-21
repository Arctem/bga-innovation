<?php

namespace Innovation\Cards\Echoes;

use Innovation\Cards\AbstractCard;
use Innovation\Enums\Colors;
use Innovation\Enums\Directions;

class Card386 extends AbstractCard
{

  // Stethoscope
  // - 3rd edition
  //   - ECHO: Meld a blue or yellow card from your hand. 
  //   - Draw a [7]. If you melded a blue card due to Stethoscope's echo effect, draw an [8].
  //   - You may splay your yellow cards right.
  // - 4th edition
  //   - ECHO: Meld a blue or yellow card from your hand. 
  //   - Draw a [7]. If you melded a blue card due to Stethoscope's echo effect, draw an [8], and
  //     if Stethoscope was foreseen, draw a [9].
  //   - You may splay your yellow cards right.

  public function initialExecution()
  {
    if (self::isEcho()) {
      // Only reset the auxiliary value if the echo effect is not being repeated for a second time
      if (!$this->game->isExecutingAgainDueToEndorsedAction()) {
        $this->game->setIndexedAuxiliaryValue(self::getPlayerId(), -1);
      }
      self::setMaxSteps(1);
    } else if (self::isFirstNonDemand()) {
      self::draw(7);
      if ($this->game->echoEffectWasExecuted() && $this->game->getIndexedAuxiliaryValue(self::getPlayerId()) === 1) {
        self::draw(8);
        if (self::wasForeseen()) {
          self::draw(9);
        }
      }
    } else {
      self::setMaxSteps(1);
    }
  }

  public function getInteractionOptions(): array
  {
    if (self::isEcho()) {
      return [
        'location_from'    => 'hand',
        'meld_keyword'     => true,
        'color'            => [Colors::BLUE, Colors::YELLOW],
        'reveal_if_unable' => true,
      ];
    } else {
      return [
        'can_pass'        => true,
        'splay_direction' => Directions::RIGHT,
        'color'           => [Colors::YELLOW],
      ];
    }
  }

  public function handleCardChoice(array $card)
  {
    self::notifyAll("color: " . $card['color']);
    if (self::isEcho() && self::isBlue($card)) {
      self::notifyAll("setting var to 1");
      $this->game->setIndexedAuxiliaryValue(self::getPlayerId(), 1); // Track whether a blue card was melded
    }
  }

}