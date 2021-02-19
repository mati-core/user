<?php

declare(strict_types=1);

namespace MatiCore\User;

/**
 * Class SuperUserAccess
 * @package MatiCore\User
 */
class SuperUserAccess extends \Exception
{

	/**
	 * @throws SuperUserAccess
	 */
	public static function Allowed(): void{
		throw new self('Super admin access allowed!', 999886);
	}

}