<?php

namespace Innovation\Enums;

class Locations
{
  const ACHIEVEMENTS = 'achievements';
  const BOARD = 'board';
  const HAND = 'hand';
  const SCORE = 'score';

  // Special values which are used for interaction options but cannot be encoded/decoded
  const AVAILABLE_ACHIEVEMENTS = 'available achievements';
}