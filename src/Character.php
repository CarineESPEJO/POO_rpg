<?php

class Character
{
    public string $name;
    public int $life = 100;
    public int $strength;
    public int $intelligence;
    public int $stamina = 100;
    public string $srcImg;

    public function fight(Character $target)  
    {
        if ($this->stamina > 15) {
            $this->stamina = max(0, $this->stamina - 15);

            $damage = (ROUND((random_int(0, 10)/10) * $this->strength) * (1 - $target->defend()/100));
            $damage = max(0, $damage);

            $target->life = max(0, $target->life - $damage);
            $target->intelligence = max(0, $target->intelligence - 3);
        }
    }

    public function defend()
    {
        if ($this->stamina > 20) {
            return $this->stamina - 20;
        } 
     return 0;   
    }

    public function heal()
    {
        $healAmount = 10 * (1 + ($this->intelligence / 100));
        $this->life = min(100, $this->life + $healAmount);
    }
}
