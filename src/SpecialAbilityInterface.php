<?php 

interface SpecialAbilityInterface {
    public function useAbility(Character $target):string;
    public function getAbilityName():string;
}