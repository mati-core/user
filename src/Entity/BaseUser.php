<?php

declare(strict_types=1);


namespace MatiCore\User;

use Baraja\Doctrine\UUID\UuidIdentifier;
use Doctrine\ORM\Mapping as ORM;
use Nette\Security\IIdentity;
use Nette\SmartObject;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="user__user",
 *     indexes={
 *     		@ORM\Index(name="user__discriminator", columns={"discriminator"}),
 *       	@ORM\Index(name="user__id_discriminator", columns={"id", "discriminator"})
 *     }
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 */
class BaseUser implements IIdentity, IUser
{

	public const GROUP_SUPER_ADMIN = 'group-super-admin';
	public const GROUP_ADMIN = 'group-admin';
	public const ROLE_ADMIN = 'role-admin';
	public const RIGHT_CMS = 'cms';

	use UuidIdentifier;
	use SmartObject;

	/**
	 * @var UserGroup
	 * @ORM\ManyToOne(targetEntity="\MatiCore\User\UserGroup")
	 * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
	 */
	private $group;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=true)
	 */
	private $username;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $password;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $firstName;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $lastName;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $namePrefix;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $nameSuffix;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $email;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $phone;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $iconPath;

	/**
	 * Internal
	 *
	 * @var \DateTime|null
	 * @ORM\Column(type="datetime", nullable=true, name="internal__gravatar_last_check")
	 */
	private $gravatarLastCheck;

	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $registerIp;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $registerDate;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $loginIp;

	/**
	 * @var \DateTime|null
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $loginDate;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $loginDevice;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 */
	private $createDate;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	private $active = true;

	/**
	 * @var string[]|null
	 */
	private $roles;

	/**
	 * BaseUser constructor.
	 * @param UserGroup $group
	 * @param string $username
	 * @param string $password
	 */
	public function __construct(UserGroup $group, string $username, string $password)
	{
		$this->group = $group;
		$this->username = $username;
		$this->password = $password;

		$this->createDate = DateTime::from('NOW');
	}

	/**
	 * @return UserGroup
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * @param UserGroup $group
	 */
	public function setGroup(UserGroup $group): void
	{
		$this->group = $group;
	}

	/**
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	/**
	 * @return string|null
	 */
	public function getFirstName(): ?string
	{
		return $this->firstName;
	}

	/**
	 * @param string|null $firstName
	 */
	public function setFirstName(?string $firstName): void
	{
		$this->firstName = $firstName;
	}

	/**
	 * @return string|null
	 */
	public function getLastName(): ?string
	{
		return $this->lastName;
	}

	/**
	 * @param string|null $lastName
	 */
	public function setLastName(?string $lastName): void
	{
		$this->lastName = $lastName;
	}

	/**
	 * @return string|null
	 */
	public function getNamePrefix(): ?string
	{
		return $this->namePrefix;
	}

	/**
	 * @param string|null $namePrefix
	 */
	public function setNamePrefix(?string $namePrefix): void
	{
		$this->namePrefix = $namePrefix;
	}

	/**
	 * @return string|null
	 */
	public function getNameSuffix(): ?string
	{
		return $this->nameSuffix;
	}

	/**
	 * @param string|null $nameSuffix
	 */
	public function setNameSuffix(?string $nameSuffix): void
	{
		$this->nameSuffix = $nameSuffix;
	}

	/**
	 * @return string
	 */
	public function getName(bool $reverse = false): string
	{
		if ($reverse === true) {
			return $this->getReversedFullName();
		}

		return $this->getFullName(false);
	}

	/**
	 * @param bool $withExtension
	 * @return string
	 */
	public function getFullName(bool $withExtension = true): string
	{
		if ($this->getFirstName() === null && $this->getLastName() === null) {
			return $this->getUsername();
		}

		$name = $this->getFirstName() ?? '';
		if ($this->getLastName() !== null) {
			if ($name === '') {
				$name .= ' ';
			}

			$name .= $this->getLastName();
		}

		if ($withExtension === true) {
			if ($this->getNamePrefix() !== null) {
				$name = $this->getNamePrefix() . ' ' . $name;
			}

			if ($this->getNameSuffix() !== null) {
				$name .= ', ' . $this->getNameSuffix();
			}
		}

		return $name;
	}

	/**
	 * @return string
	 */
	public function getReversedFullName(): string
	{
		if ($this->getLastName() !== null && $this->getFirstName() !== null) {
			return $this->getLastName() . ', ' . $this->getFirstName();
		}

		if ($this->getLastName() !== null) {
			return $this->getLastName();
		}

		if ($this->getFirstName() !== null) {
			return $this->getFirstName();
		}

		return $this->getUsername();
	}

	/**
	 * @return string|null
	 */
	public function getEmail(): ?string
	{
		return $this->email;
	}

	/**
	 * @param string|null $email
	 */
	public function setEmail(?string $email): void
	{
		$this->email = $email;
	}

	/**
	 * @return string|null
	 */
	public function getPhone(): ?string
	{
		return $this->phone;
	}

	/**
	 * @param string|null $phone
	 */
	public function setPhone(?string $phone): void
	{
		$this->phone = $phone;
	}

	/**
	 * @return string
	 */
	public function getRegisterIp(): string
	{
		return $this->registerIp;
	}

	/**
	 * @param string $registerIp
	 */
	public function setRegisterIp(string $registerIp): void
	{
		$this->registerIp = $registerIp;
	}

	/**
	 * @return \DateTime
	 */
	public function getRegisterDate(): \DateTime
	{
		return $this->registerDate;
	}

	/**
	 * @param \DateTime $registerDate
	 */
	public function setRegisterDate(\DateTime $registerDate): void
	{
		$this->registerDate = $registerDate;
	}

	/**
	 * @return string|null
	 */
	public function getIconPath(): ?string
	{
		return $this->iconPath;
	}

	/**
	 * @param string|null $iconPath
	 */
	public function setIconPath(?string $iconPath): void
	{
		$this->iconPath = $iconPath;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getGravatarLastCheck(): ?\DateTime
	{
		return $this->gravatarLastCheck;
	}

	/**
	 * @param \DateTime|null $gravatarLastCheck
	 */
	public function setGravatarLastCheck(?\DateTime $gravatarLastCheck): void
	{
		$this->gravatarLastCheck = $gravatarLastCheck;
	}

	/**
	 * Return path to user icon.
	 *
	 * @param string|null $defaultUrl
	 * @return string
	 */
	public function getIcon(?string $defaultUrl = null): string
	{
		if ($this->iconPath !== null && $this->iconPath !== 'gravatar') {
			if (
				$this->iconPath === '1'
				|| $this->iconPath === '2'
				|| $this->iconPath === '3'
				|| $this->iconPath === '4'
				|| $this->iconPath === '5'
			) {
				return '/assets/img/user/avatar/avatar_' . $this->iconPath . '.png';
			}

			return $this->iconPath;
		}

		if ($defaultUrl === '') {
			$defaultUrl = null;
		}

		$urlPart = 'http://www.gravatar.com/avatar/' . md5($this->getEmail() ?? $this->getUsername());
		$gravatarPath = $urlPart . '?d=monsterid&s=64';

		$checkNow = $this->gravatarLastCheck === null || strtotime('-1 week') < $this->gravatarLastCheck->getTimestamp();

		if ($checkNow === true) {
			if (strpos(@get_headers($urlPart . '?d=404')[0], '200') !== false) {
				$this->iconPath = $gravatarPath;
			}

			$this->gravatarLastCheck = DateTime::from('now');
		}

		if ($this->iconPath === null) {
			return '/assets/img/user' . ($defaultUrl ?? '/avatar/avatar_1.png');
		}

		return $gravatarPath;
	}

	/**
	 * @return string
	 * @throws \Exception
	 * @internal
	 */
	public function getGravatarIcon(): string
	{
		$urlPart = 'http://www.gravatar.com/avatar/' . md5($this->getEmail() ?? $this->getUsername());
		$gravatarPath = $urlPart . '?d=monsterid&s=64';

		$checkNow = $this->gravatarLastCheck === null || strtotime('-1 week') < $this->gravatarLastCheck->getTimestamp();

		if ($checkNow === true) {
			if (strpos(@get_headers($urlPart . '?d=404')[0], '200') !== false) {
				$this->iconPath = $gravatarPath;
			}

			$this->gravatarLastCheck = DateTime::from('now');
		}

		if ($this->iconPath === null) {
			return '/assets/img/user/avatar/gravatar.png';
		}

		return $gravatarPath;
	}

	/**
	 * @return string|null
	 */
	public function getLoginIp(): ?string
	{
		return $this->loginIp;
	}

	/**
	 * @param string|null $loginIp
	 */
	public function setLoginIp(?string $loginIp): void
	{
		$this->loginIp = $loginIp;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getLoginDate(): ?\DateTime
	{
		return $this->loginDate;
	}

	/**
	 * @param \DateTime|null $loginDate
	 */
	public function setLoginDate(?\DateTime $loginDate): void
	{
		$this->loginDate = $loginDate;
	}

	/**
	 * @return string|null
	 */
	public function getLoginDevice(): ?string
	{
		return $this->loginDevice;
	}

	/**
	 * @param string|null $loginDevice
	 */
	public function setLoginDevice(?string $loginDevice): void
	{
		$this->loginDevice = $loginDevice;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreateDate(): \DateTime
	{
		return $this->createDate;
	}

	/**
	 * @param \DateTime $createDate
	 */
	public function setCreateDate(\DateTime $createDate): void
	{
		$this->createDate = $createDate;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->active;
	}

	/**
	 * @param bool $active
	 */
	public function setActive(bool $active): void
	{
		$this->active = $active;
	}

	/**
	 * @return string[]
	 */
	public function getRoles(): array
	{
		return $this->roles ?? throw new UserException('User roles doesn\'t loaded. Use $userManager->loadRoles(IUser $user)');
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->getFullName(false);
	}

}