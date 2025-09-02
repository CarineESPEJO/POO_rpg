<?php

require_once __DIR__ . "/Character.php";

class Warrior extends Character
{
    protected const POWERSTRIKE_THRESHOLD = 30;
    private int $armor;

    public function __construct(string $name, int $strength, int $intelligence, string $srcImg, int $armor)
    {
        parent::__construct($name, $strength, $intelligence, $srcImg);
        $this->setArmor($armor);
    }

   
    public function getArmor(): int
    {
        return $this->armor;
    }


    public function setArmor(int $armor): void
    {
        $this->armor = $this->validateStat($armor, 'armor');
    }


    public function defend(): int
    {
        $baseDefense = parent::defend();
        return $baseDefense + $this->getArmor();
    }

    public function powerStrike(Character $target): void
    {
        if ($this->getStamina() < self::POWERSTRIKE_THRESHOLD) {
            return;
        }

        $this->setStamina(max(self::MIN_STATS, $this->getStamina() - self::POWERSTRIKE_THRESHOLD));

        $damage = $this->getStrength() * 1.5;
        $defense = min($target->defend(), 100);
        $damage = (int) ($damage * (1 - $defense / 100));
        $damage = max(0, $damage);

        $target->setHealth(max(self::MIN_STATS, $target->getHealth() - $damage));
    }
}
