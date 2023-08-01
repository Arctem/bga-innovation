<?php

namespace Innovation\Cards\Unseen;

use Innovation\Cards\Card;

class Card534 extends Card
{

  // Pen Name:
  //   - Choose to either splay an unsplayed non-purple color on your board left and self-execute
  //     its top card, or meld a card from your hand and splay its color on your board right.

  public function initialExecution()
  {
    self::setMaxSteps(2);
  }

  public function getInteractionOptions(): array
  {
    if (self::getCurrentStep() === 1) {
      return ['choices' => [1, 2]];
    } else if (self::getAuxiliaryValue() === 1) {
      return [
        'splay_direction'     => $this->game::LEFT,
        'has_splay_direction' => [$this->game::UNSPLAYED],
        'color'               => self::getAllColorsOtherThan($this->game::PURPLE),
      ];
    } else {
      return [
        'location_from' => 'hand',
        'meld_keyword'  => true,
      ];
    }
  }

  public function getSpecialChoicePrompt(): array
  {
    return self::getPromptForChoiceFromList([
      1 => clienttranslate('Splay a non-purple color left and self-execute the top card'),
      2 => clienttranslate('Meld a card from your hand and splay its color right'),
    ]);
  }

  public function handleSpecialChoice(int $choice): void
  {
    if ($choice === 1) {
      self::notifyPlayer(
          clienttranslate('${You} have chosen to splay a non-purple color left and self-execute the top card.'),
          ['You' => 'You']
        );
      self::notifyOthers(
          clienttranslate('${player_name} has chosen to splay a non-purple color left and self-execute the top card.'),
          ['player_name' => $this->game->getColoredPlayerName(self::getPlayerId())]
        );
    } else {
      self::notifyPlayer(
          clienttranslate('${You} have chosen to meld a card from your hand and splay its color right.'),
          ['You' => 'You']
        );
      self::notifyOthers(
          clienttranslate('${player_name} has chosen to meld a card from his hand and splay its color right.'),
          ['player_name' => $this->game->getColoredPlayerName(self::getPlayerId())]
        );
    }
    self::setAuxiliaryValue($choice);
    
  }

  public function handleCardChoice(array $card)
  {
    self::splayRight($card['color']);
  }

  public function afterInteraction()
  {
    if (self::getAuxiliaryValue() === 1) {
      self::selfExecute(self::getTopCardOfColor(self::getLastSelectedColor()));
    }
  }

}