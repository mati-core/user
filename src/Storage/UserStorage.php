<?php

declare(strict_types=1);

namespace MatiCore\User;

use Baraja\Doctrine\EntityManager;
use Baraja\Doctrine\EntityManagerException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Nette\Http\Session;
use Nette\Http\UserStorage as NetteUserStorage;
use Nette\Security\IIdentity;

/**
 * Class UserStorage
 * @package MatiCore\User
 */
class UserStorage extends NetteUserStorage
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * UserStorage constructor.
	 * @param Session $sessionHandler
	 * @param EntityManager $entityManager
	 */
	public function __construct(Session $sessionHandler, EntityManager $entityManager)
	{
		parent::__construct($sessionHandler);
		$this->entityManager = $entityManager;
	}

	/**
	 * @param IIdentity|null $identity
	 * @return NetteUserStorage
	 */
	public function setIdentity(?IIdentity $identity = null): NetteUserStorage
	{
		if ($identity instanceof BaseUser) {
			$storageIdentity = new StorageIdentity($identity->getId(), $identity);

			return parent::setIdentity($storageIdentity);
		}

		return parent::setIdentity($identity);
	}

	/**
	 * @return IIdentity|null
	 */
	public function getIdentity(): ?IIdentity
	{
		$identity = parent::getIdentity();

		if ($identity instanceof StorageIdentity) {
			$class = $identity->getClass();

			try {
				/** @var BaseUser|null $user */
				$user = $this->entityManager->getReference(
					class_exists($class) ? $class : BaseUser::class,
					$identity->getId()
				);

				$identity->setUser($user);
			} catch (EntityManagerException $e) {
			}
		}

		return $identity;
	}

}