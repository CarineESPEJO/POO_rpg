<?php
require_once __DIR__ . "/Character.php";

class Assassin extends Character
{
    protected const AGILITY_THRESHOLD = 10;
    protected const SNEAK_ATK_THRESHOLD = 40;
    private int $agility;

    public function __construct(string $name, int $strength, int $intelligence, int $agility, string $srcImg)
    {
        parent::__construct($name, $strength, $intelligence, $srcImg);
        $this->setAgility($agility);
    }

    public function getAgility(): int { return $this->agility; }
    public function setAgility(int $agility): void { $this->agility = $this->validateStat($agility, 'agility'); }

  public function attack(Character $target): string
{
    $msg = "";

    // Base attack
    if ($this->getStamina() < self::ATTACK_THRESHOLD) {
        $msg .= "{$this->getName()} tried to attack but doesn't have enough stamina!";
    } else {
        $this->setStamina(max(self::MIN_STATS, $this->getStamina() - self::ATTACK_THRESHOLD));

        $damage = round((random_int(0, 10) / 10) * $this->getStrength());
        $defense = min($target->defend(), 100);
        $damage = (int) ($damage * (1 - $defense / 100));
        $damage = max(0, $damage);

        $target->setHealth(max(self::MIN_STATS, $target->getHealth() - $damage));
        $target->setIntelligence(max(self::MIN_STATS, $target->getIntelligence() - self::INTELLIGENCE_ATK_MALUS));

        $msg .= "{$this->getName()} attacks {$target->getName()}, dealing $damage damage!";
    }

    // Possible second attack due to agility
    if ($this->getAgility() > random_int(0, 100)) {
        $this->setAgility(max(self::MIN_STATS, $this->getAgility() - self::AGILITY_THRESHOLD));

        $damage2 = round((random_int(0, 10) / 10) * $this->getStrength());
        $defense2 = min($target->defend(), 100);
        $damage2 = (int) ($damage2 * (1 - $defense2 / 100));
        $damage2 = max(0, $damage2);

        $target->setHealth(max(self::MIN_STATS, $target->getHealth() - $damage2));
        $target->setIntelligence(max(self::MIN_STATS, $target->getIntelligence() - self::INTELLIGENCE_ATK_MALUS));

        $msg .= " {$this->getName()} performs a SECOND attack, dealing $damage2 damage!";
    }

    return $msg;
}

public function sneakAttack(Character $target): string
{
    if ($this->getStamina() < self::SNEAK_ATK_THRESHOLD) {
        return "{$this->getName()} tried a SNEAK ATTACK but doesn't have enough stamina!";
    }

    $this->setStamina(max(self::MIN_STATS, $this->getStamina() - self::SNEAK_ATK_THRESHOLD));

    $bonus = $this->getAgility() / 100;
    $damage = $bonus * $this->getStrength();
    $defense = min($target->defend(), 100);
    $damage = (int)($damage * (1 - $defense / 100));
    $damage = max(0, $damage);

    $target->setHealth(max(self::MIN_STATS, $target->getHealth() - $damage));
    $target->setIntelligence(max(self::MIN_STATS, $target->getIntelligence() - self::INTELLIGENCE_ATK_MALUS));

    return "{$this->getName()} performs a SNEAK ATTACK on {$target->getName()}, dealing $damage damage!";
}


    public function dodge(): string
    {
        $dodgeRate = $this->getAgility() / 2;
        if ($dodgeRate > random_int(0, 100)) {
            return "{$this->getName()} successfully dodged the attack!";
        }
        return "{$this->getName()} failed to dodge!";
    }
}
