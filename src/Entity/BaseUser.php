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
	 * @var string
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
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email): void
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

}