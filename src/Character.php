<?php

class Character
{
    private string $name;
    private int $life = 100;
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
    public function getLife(): int { return $this->life; }
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

            case 'life':
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


    public function setLife(int $life): void
    {
        $this->validateValue('life', $life);
    }

    public function setIntelligence(int $intelligence): void
    {
        $this->validateValue('intelligence', $intelligence);
    }

   
    public function setAllStats(int $life, int $strength, int $intelligence, int $stamina): void
    {
        foreach (compact('life', 'strength', 'intelligence', 'stamina') as $key => $value) {
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

        $target->setLife(max(0, $target->getLife() - $damage));
        $target->setIntelligence(max(0, $target->getIntelligence() - 3));
    }

    public function defend(): int
    {
        return $this->stamina > 20 ? $this->stamina - 20 : 0;
    }

    public function heal(): void
    {
        $healAmount = (int) round(10 + (1 + ($this->intelligence / 100)));
        $this->life = min(100, $this->life + $healAmount);
    }
}
