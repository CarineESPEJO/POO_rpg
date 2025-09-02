<?php

class Character
{
    private const MIN_NAME = 3;
    private const MAX_NAME = 20;
    private const MIN_STATS = 0;
    private const MAX_STATS = 100;
    private const ATTACK_THRESHOLD = 15;
    private const DEFENSE_THRESHOLD = 20;
    private const INTELLIGENCE_ATK_MALUS = 3;
    private const HEALING_THRESHOLD = 10;
 private const HEALING_BASE = 10;

    private string $name;
    private int $health = 100;
    private int $strength;
    private int $intelligence;
    private int $stamina = 100;
    private string $srcImg;

    public function __construct(string $name, int $strength, int $intelligence, string $srcImg)
    {
        $this->validateValue('name', $name);
        $this->validateValue('strength', $strength);
        $this->validateValue('intelligence', $intelligence);
        $this->srcImg = $srcImg;
    }

    // Getters
    public function getName(): string { return $this->name; }
    public function getHealth(): int { return $this->health; }
    public function getStrength(): int { return $this->strength; }
    public function getIntelligence(): int { return $this->intelligence; }
    public function getStamina(): int { return $this->stamina; }
    public function getSrcImg(): string { return $this->srcImg; }

    // Validation
    private function validateValue(string $key, mixed $value): void
    {
        switch ($key) {
            case 'name':
                if (!is_string($value) || strlen($value) < self::MIN_NAME || strlen($value) > self::MAX_NAME) {
                    throw new InvalidArgumentException("Invalid {$key}: must be 3–20 characters.");
                }
                break;

            case 'health':
            case 'strength':
            case 'intelligence':
            case 'stamina':
                if (!is_int($value) || $value < self::MIN_STATS || $value > self::MAX_STATS) {
                    throw new InvalidArgumentException("Invalid {$key}: must be an integer between 0 and 100.");
                }
                break;

            default:
                throw new InvalidArgumentException("Unknown property {$key}");
        }

        $this->$key = $value;
    }

    // Setters
    public function setName(string $name): void
    {
        $this->validateValue('name', $name);
    }

    public function setHealth(int $health): void
    {
        $this->validateValue('health', $health);
    }

    public function setIntelligence(int $intelligence): void
    {
        $this->validateValue('intelligence', $intelligence);
    }

    public function setAllStats(int $health, int $strength, int $intelligence, int $stamina): void
    {
        foreach (compact('health', 'strength', 'intelligence', 'stamina') as $key => $value) {
            $this->validateValue($key, $value);
        }
    }


    public function attack(Character $target): void
    {
        if ($this->stamina < self::ATTACK_THRESHOLD) { 
            return;
        }

        $this->stamina = max(self::MIN_STATS, $this->stamina - self::ATTACK_THRESHOLD);

        $damage = round((random_int(0, 10) / 10) * $this->strength);
        $defense = min($target->defend(), 100); 
        $damage = (int) ($damage * (1 - $defense / 100));
        $damage = max(0, $damage);

        $target->setHealth(max(self::MIN_STATS, $target->getHealth() - $damage));
        $target->setIntelligence(max(self::MIN_STATS, $target->getIntelligence() - self::INTELLIGENCE_ATK_MALUS));
    }

    public function defend(): int
    {
        return $this->stamina > self::DEFENSE_THRESHOLD ? $this->stamina - self::DEFENSE_THRESHOLD : self::MIN_STATS;
    }

    public function heal(): void
{
    if ($this->intelligence < self::HEALING_THRESHOLD) {
        return;
    }

    $healAmount = self::HEALING_BASE + (int) round($this->intelligence * 0.1);
    $this->health = min(self::MAX_STATS, $this->health + $healAmount);
    $this->intelligence = max(self::MIN_STATS, $this->intelligence - self::HEALING_THRESHOLD);
}
};