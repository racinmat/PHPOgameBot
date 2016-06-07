<?php

namespace App\Model\Type;

use App\Enum\PlayerStatus;

class PlayerStatusType extends EnumType {

    protected $name = 'playerstatus';
    protected $enumClass = PlayerStatus::class;

}
