<?php
class Character
{
    protected const MIN_NAME = 3;
    protected const MAX_NAME = 20;
    protected const MIN_STATS = 0;
    protected const MAX_STATS = 100;
    protected const ATTACK_THRESHOLD = 15;
    protected const DEFENSE_THRESHOLD = 20;
    protected const INTELLIGENCE_ATK_MALUS = 3;
    protected const HEALING_THRESHOLD = 10;
    protected const HEALING_BASE = 10;

    protected string $name;
    protected int $health = 100;
    protected int $strength;
    protected int $intelligence;
    protected int $stamina = 100;
    protected string $srcImg;

    public function __construct(string $name, int $strength, int $intelligence, string $srcImg)
    {
        $this->setName($name);
        $this->setStrength($strength);
        $this->setIntelligence($intelligence);
        $this->srcImg = $srcImg;
    }

 
    public function getName(): string { return $this->name; }
    public function getHealth(): int { return $this->health; }
    public function getStrength(): int { return $this->strength; }
    public function getIntelligence(): int { return $this->intelligence; }
    public function getStamina(): int { return $this->stamina; }
    public function getSrcImg(): string { return $this->srcImg; }


    protected function validateName(string $name): string
    {
        if (strlen($name) < self::MIN_NAME || strlen($name) > self::MAX_NAME) {
            throw new InvalidArgumentException("Invalid name: must be 3–20 characters.");
        }
        return $name;
    }

    protected function validateStat(int $value, string $key = 'stat'): int
    {
        if ($value < self::MIN_STATS || $value > self::MAX_STATS) {
            throw new InvalidArgumentException("Invalid {$key}: must be between 0 and 100.");
        }
        return $value;
    }


    public function setName(string $name): void
    {
        $this->name = $this->validateName($name);
    }

    public function setHealth(int $health): void
    {
        $this->health = $this->validateStat($health, 'health');
    }

    public function setStrength(int $strength): void
    {
        $this->strength = $this->validateStat($strength, 'strength');
    }

    public function setIntelligence(int $intelligence): void
    {
        $this->intelligence = $this->validateStat($intelligence, 'intelligence');
    }

    public function setStamina(int $stamina): void
    {
        $this->stamina = $this->validateStat($stamina, 'stamina');
    }

    public function setAllStats(int $health, int $strength, int $intelligence, int $stamina): void
    {
        $this->setHealth($health);
        $this->setStrength($strength);
        $this->setIntelligence($intelligence);
        $this->setStamina($stamina);
    }

 
    public function attack(Character $target): void
    {
        if ($this->getStamina() < self::ATTACK_THRESHOLD) {
            return;
        }

        $this->setStamina(max(self::MIN_STATS, $this->getStamina() - self::ATTACK_THRESHOLD));

        $damage = round((random_int(0, 10) / 10) * $this->getStrength());
        $defense = min($target->defend(), 100);
        $damage = (int) ($damage * (1 - $defense / 100));
        $damage = max(0, $damage);

        $target->setHealth(max(self::MIN_STATS, $target->getHealth() - $damage));
        $target->setIntelligence(max(self::MIN_STATS, $target->getIntelligence() - self::INTELLIGENCE_ATK_MALUS));
    }

    public function defend(): int
    {
        return $this->getStamina() > self::DEFENSE_THRESHOLD
            ? $this->getStamina() - self::DEFENSE_THRESHOLD
            : self::MIN_STATS;
    }

    public function heal(): void
    {
        if ($this->getIntelligence() < self::HEALING_THRESHOLD) {
            return;
        }

        $healAmount = self::HEALING_BASE + (int) round($this->getIntelligence() * 0.1);
        $this->setHealth(min(self::MAX_STATS, $this->getHealth() + $healAmount));
        $this->setIntelligence(max(self::MIN_STATS, $this->getIntelligence() - self::HEALING_THRESHOLD));
    }
}
