<?php

class Character
{
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

  
    public function getName(): string { return $this->name; }
    public function getHealth(): int { return $this->health; }
    public function getStrength(): int { return $this->strength; }
    public function getIntelligence(): int { return $this->intelligence; }
    public function getStamina(): int { return $this->stamina; }
    public function getSrcImg(): string { return $this->srcImg; }

   
    private function validateValue(string $key, mixed $value): void
    {
        switch ($key) {
            case 'name':
                if (!is_string($value) || strlen($value) < 3 || strlen($value) > 20) {
                    throw new InvalidArgumentException("Invalid {$key}: must be 3–20 characters.");
                }
                break;

            case 'health':
            case 'strength':
            case 'intelligence':
            case 'stamina':
                if (!is_int($value) || $value < 0 || $value > 100) {
                    throw new InvalidArgumentException("Invalid {$key}: must be an integer between 0 and 100.");
                }
                break;

            default:
                throw new InvalidArgumentException("Unknown property {$key}");
        }

        $this->$key = $value;
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

    
    public function fight(Character $target): void
    {
        if ($this->stamina <= 15) {
            return;
        }

        $this->stamina = max(0, $this->stamina - 15);

        $damage = round((random_int(0, 10) / 10) * $this->strength);
        $damage = (int) ($damage * (1 - $target->defend() / 100));
        $damage = max(0, $damage);

        $target->setHealth(max(0, $target->getHealth() - $damage));
        $target->setIntelligence(max(0, $target->getIntelligence() - 3));
    }

    public function defend(): int
    {
        return $this->stamina > 20 ? $this->stamina - 20 : 0;
    }

    public function heal(): void
    {
        $healAmount = (int) round(10 + (1 + ($this->intelligence / 100)));
        $this->health = min(100, $this->health + $healAmount);
    }
}
