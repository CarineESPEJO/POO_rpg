<?php
require_once __DIR__ . "/Character.php";
require_once __DIR__ . "/SpecialAbilityInterface.php";

class Arena
{
    protected const MIN_STATS = 0;
    protected const HEAL_THRESHOLD = 10;

    public function playRound(Character $attacker, Character $defender, string $action): string
    {
        // Game over check
        if ($attacker->getHealth() <= self::MIN_STATS || $defender->getHealth() <= self::MIN_STATS ||
            $attacker->getStamina() <= self::MIN_STATS && $attacker->getIntelligence() < self::HEAL_THRESHOLD ||
            $defender->getStamina() <= self::MIN_STATS && $defender->getIntelligence() < self::HEAL_THRESHOLD
        ) {
            return "One of the fighters cannot continue!";
        }

        switch ($action) {
            case 'attack':
                return $attacker->attack($defender);
            case 'heal':
                return $attacker->heal();
            case 'useAbility':
                if ($attacker instanceof SpecialAbilityInterface) {
                    return $attacker->useAbility($defender);
                }
                return "{$attacker->getName()} has no special ability!";
            case 'inspect':
                return $attacker->inspect();
            default:
                throw new InvalidArgumentException("Invalid action: $action");
        }
    }

    public function checkWinner(Character $char1, Character $char2): ?string
    {
        if ($char1->getHealth() <= self::MIN_STATS && $char2->getHealth() <= self::MIN_STATS) {
            return "It's a draw!";
        }
        if ($char1->getHealth() <= self::MIN_STATS || ($char1->getStamina() < 15 && $char1->getIntelligence() < self::HEAL_THRESHOLD)) {
            return "{$char2->getName()} wins!";
        }
        if ($char2->getHealth() <= self::MIN_STATS || ($char2->getStamina() < 15 && $char2->getIntelligence() < self::HEAL_THRESHOLD)) {
            return "{$char1->getName()} wins!";
        }
        return null; // No winner yet
    }
}
