<?php
require_once __DIR__ . "/Character.php";

class Warrior extends Character
{
    protected const POWERSTRIKE_THRESHOLD = 30;
    private int $armor;

    public function __construct(string $name, int $strength, int $intelligence, int $armor, string $srcImg)
    {
        parent::__construct($name, $strength, $intelligence, $srcImg);
        $this->setArmor($armor);
    }

    public function getArmor(): int { return $this->armor; }
    public function setArmor(int $armor): void { $this->armor = $this->validateStat($armor, 'armor'); }

    public function defend(): int { return parent::defend() + $this->getArmor(); }

    public function attack(Character $target): string
{
    if ($this->getStamina() < self::ATTACK_THRESHOLD) {
        return "{$this->getName()} tried to attack but doesn't have enough stamina!";
    }

    $this->setStamina(max(self::MIN_STATS, $this->getStamina() - self::ATTACK_THRESHOLD));

    $damage = round((random_int(0, 10) / 10) * $this->getStrength());
    $defense = min($target->defend(), 100);
    $damage = (int) ($damage * (1 - $defense / 100));
    $damage = max(0, $damage);

    $target->setHealth(max(self::MIN_STATS, $target->getHealth() - $damage));
    $target->setIntelligence(max(self::MIN_STATS, $target->getIntelligence() - self::INTELLIGENCE_ATK_MALUS));

    return "{$this->getName()} strikes {$target->getName()} with a normal attack, dealing $damage damage!";
}

public function powerStrike(Character $target): string
{
    if ($this->getStamina() < self::POWERSTRIKE_THRESHOLD) {
        return "{$this->getName()} tried a POWER STRIKE but doesn't have enough stamina!";
    }

    $this->setStamina(max(self::MIN_STATS, $this->getStamina() - self::POWERSTRIKE_THRESHOLD));

    $damage = $this->getStrength() * 1.5;
    $defense = min($target->defend(), 100);
    $damage = (int) ($damage * (1 - $defense / 100));
    $damage = max(0, $damage);

    $target->setHealth(max(self::MIN_STATS, $target->getHealth() - $damage));

    return "{$this->getName()} performs a POWER STRIKE on {$target->getName()}, dealing $damage damage!";
}
}