<?php

namespace App\Model\Type;

use App\Enum\PlanetProbingStatus;

class PlanetProbingStatusType extends EnumType {

    protected $name = 'planetprobingstatus';
    protected $enumClass = PlanetProbingStatus::class;

}
