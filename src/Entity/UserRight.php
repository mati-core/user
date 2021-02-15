<?php

declare(strict_types=1);

namespace MatiCore\User;

use Baraja\Doctrine\UUID\UuidIdentifier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Nette\SmartObject;

/**
 * Class UserRight
 * @package MatiCore\User
 * @ORM\Entity()
 * @ORM\Table(name="user__user_right")
 */
class UserRight
{

	use UuidIdentifier;
	use SmartObject;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=true)
	 */
	private $slug;

	/**
	 * @var UserRightLang[]|ArrayCollection|Collection|PersistentCollection
	 * @ORM\OneToMany(targetEntity="\MatiCore\User\UserRightLang", mappedBy="right")
	 */
	private $names;

	/**
	 * @var UserRole[]|ArrayCollection|Collection|PersistentCollection
	 * @ORM\ManyToMany(targetEntity="\MatiCore\User\UserRole", mappedBy="rights")
	 */
	private $roles;

	/**
	 * UserRight constructor.
	 * @param string $name
	 * @param string $slug
	 */
	public function __construct(string $name, string $slug)
	{
		$this->name = $name;
		$this->slug = $slug;
		$this->roles = new ArrayCollection;
	}

	/**
	 * @param string|null $lang
	 * @return string
	 */
	public function getName(string $lang = null): string
	{
		//TODO: return lang name
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @return UserRole[]|array<UserRole>|Collection|ArrayCollection|PersistentCollection
	 */
	public function getRoles(): array|Collection|ArrayCollection|PersistentCollection
	{
		return $this->roles;
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return $this->slug;
	}

	/**
	 * @param string $slug
	 */
	public function setSlug(string $slug): void
	{
		$this->slug = $slug;
	}

	/**
	 * @return UserRightLang[]|array<UserRightLang>|Collection|ArrayCollection|PersistentCollection
	 */
	public function getNames(): array|Collection|ArrayCollection|PersistentCollection
	{
		return $this->names;
	}

	/**
	 * @param UserRightLang $name
	 */
	public function addName(UserRightLang $name): void
	{
		$this->names[] = $name;
	}

}