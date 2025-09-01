<?php

class Character
{
    public $name;
    public $life = 100;
    public $strength;
    public $intelligence;
    public $stamina = 100;

    public $srcImg;
  public function fight($target)
    {
        if ($this->stamina > 15) {
            $this->stamina = max(0, $this->stamina - 15);

            $damage = random_int(0, 1) * $this->strength - $target->defend();
            $damage = max(0, $damage); 

            $target->life = max(0, $target->life - $damage);
            $target->intelligence -= 3;
        }
    }

  public function defend()
{
    if ($this->stamina > 20) {
        $shield = $this->stamina - 20; 
        return $shield / 100;
    } else {
        return 0;
    }
}


    public function heal()
    {
        $this->life += 10*(1 + ($this->intelligence/100));
    }
}