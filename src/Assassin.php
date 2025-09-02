<?php

require_once __DIR__ . "/Character.php";

class Warrior extends Character
{
    protected const AGILITY_THRESHOLD = 10;
    protected const SNEAK_ATK_THRESHOLD = 40;
    private int $agility;

    public function __construct(string $name, int $strength, int $intelligence, string $srcImg, int $agility)
    {
        parent::__construct($name, $strength, $intelligence, $srcImg);
        $this->setAgility($agility);
    }

   
    public function getAgility(): int
    {
        return $this->agility;
    }

   
    public function setAgility(int $agility): void
    {
        $this->agility = $this->validateStat($agility, 'agility');
    }


    public function attack(Character $target): void
    {
        parent::attack($target);

        if ($this->getAgility() > random_int(0, 100)) {
            parent::attack($target);
            $this->setAgility(max(self::MIN_STATS, $this->getAgility() - self::AGILITY_THRESHOLD));
            echo "The assassin did a 2nd attack";
        }
    }

    public function dodge(): bool
    {
        $dodgeRate = $this->getAgility() / 2;
        if ($dodgeRate > random_int(0, 100)) {
            echo "The assassin dodged";
            return true;
        }
        return false;
    }

    public function defend(): int
    {
        $defense = parent::defend();
        if ($this->dodge()) {
            $defense += 10;
        }
        return $defense;
    }

    public function sneakAttack(Character $target): void
    {
        if ($this->getStamina() < self::SNEAK_ATK_THRESHOLD) {
            return;
        }

        $this->setStamina(max(self::MIN_STATS, $this->getStamina() - self::SNEAK_ATK_THRESHOLD));

        $bonus = $this->getAgility() / 100;
        $damage = $bonus * $this->getStrength();
        $defense = min($target->defend(), 100);
        $damage = (int)($damage * (1 - $defense / 100));
        $damage = max(0, $damage);

        $target->setHealth(max(self::MIN_STATS, $target->getHealth() - $damage));
        $target->setIntelligence(max(self::MIN_STATS, $target->getIntelligence() - self::INTELLIGENCE_ATK_MALUS));

        echo "The assassin did a furtive attack, dealing {$damage} damage. The target has {$target->getHealth()} HP left.";
    }
}
