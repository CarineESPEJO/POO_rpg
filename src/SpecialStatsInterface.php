<?php 

interface SpecialStatsInterface {
   
    public function getStatsNames():string;

     public function getStatsValues(Character $target):string;
}