<?php

declare(strict_types=1);


namespace MatiCore\User;


use Baraja\Doctrine\DatabaseException;
use Baraja\Doctrine\EntityManager;
use Baraja\Doctrine\EntityManagerException;
use Contributte\Translation\Translator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\PersistentCollection;
use Nette\Application\AbortException;
use Nette\Http\Request;
use Nette\Security\IAuthenticator;
use Nette\Security\IAuthorizator;
use Nette\Security\IIdentity;
use Nette\Security\IUserStorage;
use Nette\Security\User;
use Nette\Utils\DateTime;
use Tracy\Debugger;

/**
 * Class UserManager
 * @package MatiCore\User
 */
class UserManager implements IAuthenticator
{

	/**
	 * @var string[]
	 */
	private $params;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var IUserStorage
	 */
	private $userStorage;

	/**
	 * @var IAuthorizator
	 */
	private $authorizator;

	/**
	 * @var Request
	 */
	private $httpRequest;

	/**
	 * UserManager constructor.
	 * @param array<string> $params
	 * @param EntityManager $entityManager
	 * @param IUserStorage $userStorage
	 * @param IAuthorizator $authorizator
	 * @param Request $httpRequest
	 */
	public function __construct(array $params, EntityManager $entityManager, IUserStorage $userStorage, IAuthorizator $authorizator, Request $httpRequest)
	{
		$this->params = $params;
		$this->entityManager = $entityManager;
		$this->userStorage = $userStorage;
		$this->authorizator = $authorizator;
		$this->httpRequest = $httpRequest;
	}

	/**
	 * @param string|null $userEntityName
	 * @return array|Collection,0
	 *
	 */
	public function getAllUsers(?string $userEntityName = null): array|Collection
	{
		return $this->entityManager->getRepository($userEntityName ?? $this->getUserEntityName())
				->createQueryBuilder('u')
				->select('u')
				->join('u.group', 'g')
				->where('g.slug != :superAdmin')
				->setParameter('superAdmin', 'group-super-admin')
				->orderBy('u.lastName', 'ASC')
				->addOrderBy('u.firstName', 'ASC')
				->getQuery()
				->getResult() ?? [];
	}

	/**
	 * @param string $id
	 * @param string|null $userEntityName
	 * @return IUser
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function getUserById(string $id, ?string $userEntityName = null): IUser
	{
		return $this->entityManager->getRepository($userEntityName ?? $this->getUserEntityName())
			->createQueryBuilder('u')
			->select('u')
			->where('u.id = :id')
			->setParameter('id', $id)
			->getQuery()
			->getSingleResult();
	}

	/**
	 * @param string $username
	 * @param string|null $userEntityName
	 * @return IUser
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function getUserByUserName(string $username, ?string $userEntityName = null): IUser
	{
		return $this->entityManager->getRepository($userEntityName ?? $this->getUserEntityName())
			->createQueryBuilder('u')
			->select('u')
			->where('u.username = :username')
			->setParameter('username', $username)
			->getQuery()
			->getSingleResult();
	}

	/**
	 * @param string $id
	 * @return UserGroup
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function getUserGroupById(string $id): UserGroup
	{
		return $this->entityManager->getRepository(UserGroup::class)
			->createQueryBuilder('ug')
			->select('ug')
			->where('ug.id = :id')
			->setParameter('id', $id)
			->getQuery()
			->getSingleResult();
	}

	/**
	 * @param string $slug
	 * @return UserGroup
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function getUserGroupBySlug(string $slug): UserGroup
	{
		return $this->entityManager->getRepository(UserGroup::class)
			->createQueryBuilder('ug')
			->select('ug')
			->where('ug.slug = :slug')
			->setParameter('slug', $slug)
			->getQuery()
			->getSingleResult();
	}

	/**
	 * @return UserGroup
	 * @throws UserGroupException
	 */
	public function getDefaultUserGroup(): UserGroup
	{
		static $defaultUserGroup;

		if ($defaultUserGroup === null) {
			try {
				$defaultUserGroup = $this->entityManager->getRepository(UserGroup::class)
					->createQueryBuilder('ug')
					->select('ug')
					->where('ug.default = :t')
					->setParameter('t', true)
					->getQuery()
					->getSingleResult();
			} catch (NoResultException | NonUniqueResultException $e) {
				throw new UserGroupException('Default user group doesn`t set.');
			}
		}

		return $defaultUserGroup;
	}

	/**
	 * @return UserGroup[]|ArrayCollection|PersistentCollection|Collection|array<UserGroup>
	 */
	public function getUserGroups(): array|ArrayCollection|PersistentCollection|Collection
	{
		static $groups;

		if ($groups === null) {
			$groups = $this->entityManager->getRepository(UserGroup::class)
					->createQueryBuilder('ug')
					->select('ug')
					->where('ug.superAdmin = :f')
					->setParameter('f', false)
					->orderBy('ug.name', 'ASC')
					->getQuery()
					->getResult() ?? [];
		}

		return $groups;
	}

	/**
	 * @return array<UserRole>|ArrayCollection|PersistentCollection|Collection
	 */
	public function getUserRoles(): array|ArrayCollection|PersistentCollection|Collection
	{
		static $roles;

		if ($roles === null) {
			$roles = $this->entityManager->getRepository(UserRole::class)
					->createQueryBuilder('ur')
					->select('ur')
					->orderBy('ur.name', 'ASC')
					->getQuery()
					->getResult() ?? [];
		}

		return $roles;
	}

	/**
	 * @param string $id
	 * @return UserRole
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function getRoleById(string $id): UserRole
	{
		return $this->entityManager->getRepository(UserRole::class)
			->createQueryBuilder('ur')
			->select('ur')
			->where('ur.id = :id')
			->setParameter('id', $id)
			->getQuery()
			->getSingleResult();
	}

	/**
	 * @param string $slug
	 * @return UserRole
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function getRoleBySlug(string $slug): UserRole
	{
		return $this->entityManager->getRepository(UserRole::class)
			->createQueryBuilder('ur')
			->select('ur')
			->where('ur.slug = :slug')
			->setParameter('slug', $slug)
			->getQuery()
			->getSingleResult();
	}

	/**
	 * @param UserRole $role
	 */
	public function removeRole(UserRole $role): void
	{
		$this->entityManager->remove($role)->flush();
	}

	/**
	 * @return array<UserRight>|ArrayCollection|PersistentCollection|Collection
	 */
	public function getUserRights(): array|ArrayCollection|PersistentCollection|Collection
	{
		static $rights;

		if ($rights === null) {
			$rights = $this->entityManager->getRepository(UserRight::class)
					->createQueryBuilder('ur')
					->select('ur')
					->orderBy('ur.name', 'ASC')
					->getQuery()
					->getResult() ?? [];
		}

		return $rights;
	}

	/**
	 * @param string $id
	 * @return UserRight
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function getRightById(string $id): UserRight
	{
		return $this->entityManager->getRepository(UserRight::class)
			->createQueryBuilder('ur')
			->select('ur')
			->where('ur.id = :id')
			->setParameter('id', $id)
			->getQuery()
			->getSingleResult();
	}

	/**
	 * @param string $slug
	 * @return UserRight
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function getRightBySlug(string $slug): UserRight
	{
		return $this->entityManager->getRepository(UserRight::class)
			->createQueryBuilder('ur')
			->select('ur')
			->where('ur.slug = :slug')
			->setParameter('slug', $slug)
			->getQuery()
			->getSingleResult();
	}

	/**
	 * @param UserRight $right
	 */
	public function removeRight(UserRight $right): void
	{
		$this->entityManager->remove($right)->flush();
	}

	/**
	 * @return string
	 */
	public function getUserEntityName(): string
	{
		/** @phpstan-ignore-next-line */
		return $this->params->entity ?? BaseUser::class;
	}

	/**
	 * @param string $name
	 * @param string|null $slug
	 * @return UserGroup
	 * @throws DatabaseException
	 */
	public function createUserGroup(string $name, ?string $slug = null): UserGroup
	{
		$userGroup = new UserGroup($name, $slug);

		if (count($this->getUserGroups()) === 0) {
			$userGroup->setDefault(true);
		}

		$this->entityManager->persist($userGroup)->getUnitOfWork()->commit($userGroup);

		return $userGroup;
	}

	/**
	 * @param string $userName
	 * @param string|null $password
	 * @param UserGroup|null $userGroup
	 * @return IUser
	 * @throws UserGroupException
	 */
	public function createUser(string $userName, ?string $password = null, ?UserGroup $userGroup = null): IUser
	{
		$entityType = $this->getUserEntityName();

		if ($userGroup === null) {
			$userGroup = $this->getDefaultUserGroup();
		}

		/** @var IUser $user */
		$user = new $entityType($userGroup, $userName, UserPassword::hash($password));
		$this->entityManager->persist($user)->getUnitOfWork()->commit($user);

		return $user;
	}

	/**
	 * @param string $login
	 * @param string $password
	 * @return IIdentity
	 * @throws IncorectPasswordException
	 * @throws IncorectUsernameException
	 * @throws UserException
	 */
	public function signIn(string $login, string $password): IIdentity
	{
		return $this->authenticate([$login, $password]);
	}

	/**
	 * @param array<string> $credentials
	 * @return IIdentity
	 * @throws IncorectPasswordException
	 * @throws IncorectUsernameException
	 * @throws UserException
	 */
	public function authenticate(array $credentials): IIdentity
	{
		[$username, $password] = $credentials;

		try {
			$user = $this->getUserByUserName($username);

			if ($user->isActive() === false || !UserPassword::verify($password, $user->getPassword())) {
				throw new IncorectPasswordException('Bad password!');
			}

			if (UserPassword::needsRehash($user->getPassword())) {
				$user->setPassword(UserPassword::hash($password));
				$this->entityManager->getUnitOfWork()->commit($user);
			}

			if (!$user instanceof BaseUser) {
				throw new UserException('User must be / or extend BaseUser!');
			}

			$date = DateTime::from('NOW');
			$user->setLoginDate($date);
			$user->setLoginIp($this->httpRequest->getRemoteAddress());
			$user->setLoginDevice($this->httpRequest->getHeader('User-Agent'));
			$user->getIcon(); //Check icon

			$this->entityManager->getUnitOfWork()->commit($user);

			$instance = new StorageIdentity($user->getId(), $user);

			$this->userStorage->setAuthenticated(true);
			$this->userStorage->setIdentity($instance);

			return $instance;
		} catch (NoResultException | NonUniqueResultException $e) {
			throw new IncorectUsernameException('User doesn`t exists.');
		} catch (EntityManagerException $e) {
			throw new UserException('Error update database data.');
		}
	}


	/** Check user storage and logout if storage was broken.
	 *
	 * @throws AbortException
	 * @internal
	 */
	public function validUserLogin(): void
	{
		try {
			if (($identity = $this->userStorage->getIdentity()) !== null) {
				/** @var BaseUser|null $user */
				$user = $this->entityManager->getRepository(BaseUser::class)->find($identity->getId());

				if ($user === null || !$user instanceof BaseUser) {
					throw new AbortException('Bad user instance.');
				}
			} elseif ($this->userStorage->isAuthenticated() === true) {
				throw new AbortException('Is authenticated');
			}
		} catch (\Throwable | EntityManagerException | ORMException $e) {
			$this->userStorage->setIdentity(null);
			if (isset($_SERVER['REQUEST_URI']) && !headers_sent()) {
				header('Location: ' . $_SERVER['REQUEST_URI']);
			} else {
				Debugger::log($e);
				throw new AbortException('Invalid user identity: ' . $e->getMessage());
			}
		}
	}

	/**
	 * @param User $systemUser
	 * @param string $privilege
	 * @return bool
	 * @throws AbortException
	 */
	public function checkAccess(User $systemUser, string $privilege): bool
	{
		try {
			$storageIdentity = $systemUser->getIdentity();
			$authorizer = $systemUser->getAuthorizator();

			if (
				$storageIdentity === null
				|| !$storageIdentity instanceof StorageIdentity
				|| $authorizer === null
				|| !$authorizer instanceof Authorizator
			) {
				return Authorizator::DENY;
			}

			try {
				return $authorizer->checkAccess($storageIdentity, $privilege, true);
			} catch (SuperUserAccess $e) {
				if ($privilege !== 'super-admin') {
					try {
						$this->getRightBySlug($privilege);
					} catch (NoResultException | NonUniqueResultException $e) {
						$right = new UserRight($privilege, $privilege);
						$this->entityManager->persist($right)->getUnitOfWork()->commit($right);
					}
				}

				return Authorizator::ALLOW;
			}
		} catch (ORMException | EntityManagerException | UserException $e) {
			Debugger::log($e);
			$systemUser->logout(true);
			throw new AbortException('User integrity error: ' . $e->getMessage());
		}
	}

}