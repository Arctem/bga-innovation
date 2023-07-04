<?php

namespace Innovation\Cards\Unseen;

use Innovation\Cards\Card;

class Card538 extends Card
{

  // Sniping:
  //   - I DEMAND you unsplay the color on your board of my choice! Meld your bottom card of that
  //     color! Transfer your bottom card of that color to my board!

  public function initialExecution()
  {
    self::setMaxSteps(1);
  }

  public function getInteractionOptions(): array
  {
    $colors = $this->game->getSplayableColorsOnBoard(self::getPlayerId(), $this->game::UNSPLAYED);
    if (empty($colors)) {
      return [];
    }
    return [
      'player_id'    => self::getLauncherId(),
      'choose_color' => true,
      'color' => $colors,
    ];
  }

  public function handleSpecialChoice(int $color): void
  {
    $this->game->gamestate->changeActivePlayer(self::getPlayerId());
    self::unsplay($color);
    self::meld(self::getBottomCardOfColor($color));
    self::transferToBoard(self::getBottomCardOfColor($color), self::getLauncherId());
  }

}