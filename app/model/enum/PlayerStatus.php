<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;



/**
 * Class Building
 * @package App\Enum
 * @method static PlayerStatus _(string $value)
 */
class PlayerStatus extends Enum
{

	const
		STATUS_INACTIVE = 'inactive',
		STATUS_LONG_INACTIVE = 'long inactive',
		STATUS_NOOB = 'noob',
		STATUS_HONORABLE_TARGET = 'honorable target',
		STATUS_ACTIVE = 'active',
		STATUS_VACATION = 'vacation',
		STATUS_STRONG = 'strong',
		STATUS_ADMIN = 'admin',
		STATUS_OUTLAW = 'outlaw',
		STATUS_BANNED = 'banned'
	;

	public static function fromClass(string $class) : PlayerStatus
	{

		$classToStatus = [
			'status_abbr_noob' => static::STATUS_NOOB,
			'status_abbr_active' => static::STATUS_NOOB,
			'status_abbr_honorableTarget' => static::STATUS_NOOB,
			'status_abbr_vacation' => static::STATUS_VACATION,
			'status_abbr_inactive' => static::STATUS_INACTIVE,
			'status_abbr_strong' => static::STATUS_STRONG,
			'status_abbr_longinactive' => static::STATUS_LONG_INACTIVE,
			'status_abbr_admin' => static::STATUS_ADMIN,
			'status_abbr_outlaw' => static::STATUS_OUTLAW,
			'status_abbr_banned' => static::STATUS_BANNED
		];
		return static::newInstance($classToStatus[$class]);
	}

}