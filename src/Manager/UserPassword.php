<?php

declare(strict_types=1);

namespace MatiCore\User;

use Nette\Security\Passwords;

/**
 * Class UserSecurity
 * @package MatiCore\User
 */
class UserPassword
{

	/**
	 * @var Passwords|null
	 */
	private static $passwords;

	private static function getPasswordService(): Passwords
	{
		if (self::$passwords === null) {
			self::$passwords = new Passwords(PASSWORD_BCRYPT);
		}

		return self::$passwords;
	}

	/**
	 * @param string $password
	 * @return string
	 */
	public static function hash(string $password): string
	{
		return self::getPasswordService()->hash($password);
	}

	/**
	 * @param string $hash
	 * @return bool
	 */
	public static function needsRehash(string $hash): bool
	{
		return self::getPasswordService()->needsRehash($hash);
	}

	/**
	 * @param string $password
	 * @param string $hash
	 * @return bool
	 */
	public static function verify(string $password, string $hash): bool
	{
		if (self::getPasswordService()->verify($password, $hash)) {
			return true;
		}

		return false;
	}
}