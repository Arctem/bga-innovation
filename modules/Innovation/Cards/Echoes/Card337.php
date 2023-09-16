<?php

namespace Innovation\Cards\Echoes;

use Innovation\Cards\Card;

class Card337 extends Card
{

  // Ice Skates
  // - 3rd edition:
  //   - Return up to three cards from your hand. For each card returned, either draw and meld a
  //     [2], or draw and foreshadow a [3]. Return your highest top card.
  // - 4th edition:
  //   - If Ice Skates was foreseen, junk all cards in the [1] deck and [2] deck.
  //   - Return up to two cards from your hand. For each card you return, either draw and meld a
  //     [2], or draw and foreshadow a [3].

  public function initialExecution()
  {
    if (self::isFirstOrThirdEdition()) {
      self::setMaxSteps(3);
    } else if (self::isFirstNonDemand()) {
      if (self::wasForeseen()) {
        self::junkBaseDeck(1);
        self::junkBaseDeck(2);
      }
    } else {
      self::setMaxSteps(1);
    }
  }

  public function getInteractionOptions(): array
  {
    if (self::isFirstInteraction()) {
      return [
        'can_pass'       => true,
        'n_min'          => 1,
        'n_max'          => self::isFirstOrThirdEdition() ? 3 : 2,
        'location_from'  => 'hand',
        'return_keyword' => true,
      ];
    } else if (self::isSecondInteraction()) {
      return ['choices' => [1, 2]];
    } else {
      return [
        'location_from'  => 'board',
        'return_keyword' => true,
        'age'            => self::getMaxValue(self::getTopCards()),
      ];
    }
  }

  protected function getPromptForListChoice(): array
  {
    return self::buildPromptFromList([
      1 => [clienttranslate('Draw and meld a ${age}'), 'age' => self::renderValue(2)],
      2 => [clienttranslate('Draw and foreshadow a ${age}'), 'age' => self::renderValue(3)],
    ]);
  }

  public function handleListChoice(int $choice): void
  {
    if ($choice === 1) {
      self::drawAndMeld(2);
    } else {
      self::drawAndForeshadow(3);
    }
  }

  public function afterInteraction()
  {
    if (self::isFirstOrThirdEdition() && self::isFirstInteraction() && self::getNumChosen() === 0) {
      // Skip to the third interaction if no cards were returned
      self::setNextStep(3);
    }
    if (self::isFirstInteraction() && self::getNumChosen() > 0) {
      self::setAuxiliaryValue(self::getNumChosen()); // Tracks how many cards need to be melded or foreshadowed
      if (self::getMaxSteps() < 2) {
        self::setMaxSteps(2);
      }
    } else if (self::isSecondInteraction()) {
      $choicesLeft = self::getAuxiliaryValue() - 1;
      if ($choicesLeft > 0) {
        self::setAuxiliaryValue($choicesLeft);
        self::setNextStep(2);
      }
    }
  }

}