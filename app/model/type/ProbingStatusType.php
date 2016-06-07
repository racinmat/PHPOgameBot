<?php

namespace App\Model\Type;

use App\Enum\ProbingStatus;

class ProbingStatusType extends EnumType {

    protected $name = 'probingstatus';
    protected $enumClass = ProbingStatus::class;

}
