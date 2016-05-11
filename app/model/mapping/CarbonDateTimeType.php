<?php
/**
 * Created by PhpStorm.
 * User: Matěj Račinský
 * Date: 6. 11. 2014
 * Time: 11:47
 */

namespace App\Model\Mapping;

use App\Utils\CarbonDateTime;
use Carbon\Carbon;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;

class CarbonDateTimeType extends DateTimeType {

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Carbon
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        $dateTime = parent::convertToPHPValue($value, $platform);
        if ($dateTime instanceof \DateTime) {
            return Carbon::instance($dateTime);
        }
        return $dateTime;
    }

    public function getName() {
        return "carbon";
    }


} 