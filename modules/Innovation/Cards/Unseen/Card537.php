<?php

namespace Innovation\Cards\Unseen;

use Innovation\Cards\Card;

class Card537 extends Card
{

  // Red Herring:
  //   - Splay your red cards left, right, or up.
  //   - Draw and tuck a [6]. If the color on your board of the card you tuck is splayed in the
  //     same direction as your red  cards, splay that color up. Otherwise, unsplay that color.

  public function initialExecution()
  {
    if (self::getEffectNumber() == 1) {
      self::setMaxSteps(1);
    } else {
      $card = self::drawAndTuck(6);
      if (
        $this->game->getCurrentSplayDirection(self::getPlayerId(), $this->game::RED) ==
        $this->game->getCurrentSplayDirection(self::getPlayerId(), $card['color'])
      ) {
        self::splayUp($card['color']);
      } else {
        self::unsplay($card['color']);
      }
    }
  }

  public function getInteractionOptions(): array
  {
    return ['choices' => [1, 2, 3]];
  }

  public function getSpecialChoicePrompt(): array
  {
    return [
      "message_for_player" => clienttranslate('${You} may make a choice'),
      "message_for_others" => clienttranslate('${player_name} may make a choice among the two possibilities offered by the card'),
      "options"            => [
        [
          'value' => 1,
          'text'  => clienttranslate('Splay red left'),
        ],
        [
          'value' => 2,
          'text'  => clienttranslate('Splay red right'),
        ],
        [
          'value' => 3,
          'text'  => clienttranslate('Splay red up'),
        ],
      ],
    ];
  }

  public function handleSpecialChoice(int $choice): void
  {
    if ($choice === 1) {
      self::splayLeft($this->game::RED);
    } else if ($choice == 2) {
      self::splayRight($this->game::RED);
    } else {
      self::splayUp($this->game::RED);
    }
  }
}