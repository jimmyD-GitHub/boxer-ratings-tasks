<?php

namespace App\Entity;

class Division
{
    public const OPTIONS = [
        1 => 'heavyweight',
        2 => 'cruiserweight',
        3 => 'light-heavyweight',
        4 => 'super-middleweight',
        5 => 'middleweight',
        6 => 'light-middleweight',
        7 => 'welterweight',
        8 => 'light-welterweight',
        9 => 'lightweight',
        10 => 'super-featherweight',
        11 => 'featherweight',
        12 => 'super-bantamweight',
        13 => 'bantamweight',
        14 => 'super-flyweight',
        15 => 'flyweight',
        16 => 'light-flyweight',
        17 => 'minimumweight',
        18 => 'light-minimumweight'
    ];

    /** @var string */
    private $division;

    /**
     * @param string $division
     */
    public function __construct(string $division)
    {
        $division = strtolower($division);
        $this->division = $division;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->division;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return array_search($this->division, self::OPTIONS, true);
    }
}
