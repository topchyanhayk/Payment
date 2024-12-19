<?php


namespace App\Services\Plan\Dto;


class PlanEntityDto
{
    public string $id;
    public string $platform_type;

    public function __construct(string $id, string $platform_type)
    {
        $this->id = $id;
        $this->platform_type = $platform_type;
    }
}
