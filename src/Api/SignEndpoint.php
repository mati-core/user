<?php

declare(strict_types=1);

namespace App\Api;


use Baraja\Doctrine\DatabaseException;
use Baraja\StructuredApi\BaseEndpoint;
use Nette\Security\User;
use MatiCore\User\StorageIdentity;
use MatiCore\User\UserException;
use MatiCore\User\UserManagerAccessor;

/**
 * Class SignEndpoint
 * @package App\Api
 * @public
 */
class SignEndpoint extends BaseEndpoint
{

	/**
	 * @var UserManagerAccessor
	 * @inject
	 */
	public $userManager;

	/**
	 * @var User
	 * @inject
	 */
	public $user;

	/**
	 * @param string $login
	 * @param string $pass
	 */
	public function postSignIn(string $login, string $pass): void
	{
		try {
			$user = $this->userManager->get()->authenticate([$login, $pass]);

			if ($user instanceof StorageIdentity) {
				$this->sendOk([
					'loginStatus' => true,
				]);
			} else {
				$this->sendOk([
					'loginStatus' => false,
					'errorMsg' => 'BAD user instance.',
				]);
			}
		} catch (UserException $e) {
			$this->sendOk([
				'loginStatus' => false,
				'errorMsg' => $e->getMessage(),
			]);
		} catch (DatabaseException $e) {
			$this->sendError($e->getMessage());
		}
	}

}