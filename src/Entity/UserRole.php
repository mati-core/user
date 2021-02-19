<?php

declare(strict_types=1);

namespace MatiCore\User;

use Baraja\Doctrine\UUID\UuidIdentifier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Nette\SmartObject;
use Nette\Utils\Strings;

/**
 * Class UserRole
 * @package MatiCore\User
 * @ORM\Entity()
 * @ORM\Table(name="user__user_role")
 */
class UserRole
{

	use UuidIdentifier;
	use SmartObject;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @var UserRoleLang[]|Collection|ArrayCollection|PersistentCollection
	 * @ORM\OneToMany(targetEntity="\MatiCore\User\UserRoleLang", mappedBy="userRole")
	 */
	private $names;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=true)
	 */
	private $slug;

	/**
	 * @var UserGroup[]|Collection|ArrayCollection|PersistentCollection
	 * @ORM\ManyToMany(targetEntity="\MatiCore\User\UserGroup", mappedBy="roles")
	 * @ORM\JoinTable(name="user__user_group_role")
	 */
	private $groups;

	/**
	 * @var UserRight[]|Collection|ArrayCollection|PersistentCollection
	 * @ORM\ManyToMany(targetEntity="\MatiCore\User\UserRight", inversedBy="roles")
	 * @ORM\JoinTable(name="user__user_roles_right",
	 *     joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="right_id", referencedColumnName="id")}
	 * )
	 */
	private $rights;

	/**
	 * UserRole constructor.
	 * @param string $name
	 * @param string|null $slug
	 */
	public function __construct(string $name, string $slug = null)
	{
		$this->name = $name;
		$this->slug = Strings::webalize($slug ?? $name);
		$this->names = new ArrayCollection;
		$this->groups = new ArrayCollection;
		$this->rights = new ArrayCollection;
	}

	/**
	 * @param string|null $lang
	 * @return string
	 */
	public function getName(string $lang = null): string
	{
		//TODO: implement return lang
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
	 * @return UserGroup[]|array<UserGroup>|Collection|ArrayCollection|PersistentCollection
	 */
	public function getGroups(): array|Collection|ArrayCollection|PersistentCollection
	{
		return $this->groups;
	}

	/**
	 * @return UserRight[]|array<UserRight>|Collection|ArrayCollection|PersistentCollection
	 */
	public function getRights(): array|Collection|ArrayCollection|PersistentCollection
	{
		return $this->rights;
	}

	/**
	 * @param UserRight $right
	 */
	public function addRight(UserRight $right): void
	{
		$this->rights[] = $right;
	}

	/**
	 * @param UserRight $userRight
	 * @return bool
	 */
	public function isRight(UserRight $userRight): bool
	{
		foreach ($this->getRights() as $right) {
			if ($right->getId() === $userRight->getId()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param UserRight $right
	 */
	public function removeRight(UserRight $right): void
	{
		foreach ($this->getRights() as $key => $roleRight) {
			if ($roleRight->getId() === $right->getId()) {
				unset($this->rights[$key]);

				return;
			}
		}
	}

	/**
	 * @return UserRoleLang[]|array<UserRightLang>|Collection|ArrayCollection|PersistentCollection
	 */
	public function getNames(): array|Collection|ArrayCollection|PersistentCollection
	{
		return $this->names;
	}

	/**
	 * @param UserRoleLang $name
	 */
	public function addName(UserRoleLang $name): void
	{
		$this->names[] = $name;
	}

	/**
	 * @return string
	 */
	public function getSlug(): ?string
	{
		return $this->slug;
	}

	/**
	 * @param string $slug
	 */
	public function setSlug(?string $slug): void
	{
		$this->slug = $slug;
	}

}