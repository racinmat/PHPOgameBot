<?php
namespace App\Model\Type;

use App\Enum\Enum;
use Doctrine\DBAL\Types\StringType;

use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class EnumType extends StringType
{
	protected $name;
	/** @var Enum */
	protected $enumClass;

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		$enumClass = $this->enumClass;
		return $enumClass::newInstance($value);
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof $this->enumClass) {
            return parent::convertToDatabaseValue($value->getValue(), $platform);
        }
        return parent::convertToDatabaseValue($value, $platform);
    }

	public function getName()
	{
		return $this->name;
	}

}
