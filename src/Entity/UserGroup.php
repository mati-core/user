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
 * Class UserGroup
 * @package MatiCore\User
 * @ORM\Entity()
 * @ORM\Table(name="user__user_goup", indexes={
@ORM\Index(name="index_slug", columns={"slug"}),
@ORM\Index(name="index_default", columns={"is_default"})
})
 */
class UserGroup
{

	use UuidIdentifier;
	use SmartObject;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=true)
	 */
	private $name;

	/**
	 * @var UserGroupLang[]|Collection|ArrayCollection|PersistentCollection
	 * @ORM\OneToMany(targetEntity="\MatiCore\User\UserGroupLang", mappedBy="userGroup",
	 *     cascade={"remove","persist"})
	 */
	private $names;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=true)
	 */
	private $slug;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	private $superAdmin = false;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean", name="is_default")
	 */
	private $default = false;

	/**
	 * @var UserRole[]|Collection|ArrayCollection|PersistentCollection
	 * @ORM\ManyToMany(targetEntity="\MatiCore\User\UserRole", inversedBy="groups")
	 * @ORM\JoinTable(
	 *     name="user__user_group_role",
	 *     joinColumns={@ORM\JoinColumn(name="user_group_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
	 * )
	 */
	private $roles;

	/**
	 * @var string[]|null
	 */
	private $rights;

	/**
	 * UserGroup constructor.
	 * @param string $name
	 * @param string|null $slug
	 */
	public function __construct(string $name, string $slug = null)
	{
		$this->name = $name;
		$this->slug = Strings::webalize($slug ?? $name);
		$this->names = new ArrayCollection;
		$this->roles = new ArrayCollection;
	}

	/**
	 * @param string|null $lang
	 * @return string
	 */
	public function getName(?string $lang = null): string
	{
		//todo get lang
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
	 * @return array<UserGroupLang>|UserGroupLang[]|Collection|ArrayCollection|PersistentCollection
	 */
	public function getNames(): array|Collection|ArrayCollection|PersistentCollection
	{
		return $this->names;
	}

	/**
	 * @param UserGroupLang $name
	 */
	public function addName(UserGroupLang $name): void
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

	/**
	 * @return bool
	 */
	public function isSuperAdmin(): bool
	{
		return $this->superAdmin;
	}

	/**
	 * @param bool $superAdmin
	 */
	public function setSuperAdmin(bool $superAdmin): void
	{
		$this->superAdmin = $superAdmin;
	}

	/**
	 * @return bool
	 */
	public function isDefault(): bool
	{
		return $this->default;
	}

	/**
	 * @param bool $default
	 */
	public function setDefault(bool $default): void
	{
		$this->default = $default;
	}

	/**
	 * @return UserRole[]|array<UserRole>|PersistentCollection|ArrayCollection|Collection
	 */
	public function getRoles(): array|PersistentCollection|ArrayCollection|Collection
	{
		return $this->roles;
	}

	/**
	 * @param UserRole $role
	 */
	public function addRole(UserRole $role): void
	{
		$this->roles[] = $role;
	}

	/**
	 * @param UserRole $role
	 */
	public function removeRole(UserRole $role): void
	{
		foreach ($this->getRoles() as $key => $groupRole) {
			if ($groupRole->getId() === $role->getId()) {
				unset($this->roles[$key]);

				return;
			}
		}
	}

	/**
	 * @param UserRole $role
	 * @return bool
	 */
	public function isRole(UserRole $role): bool
	{
		foreach ($this->getRoles() as $key => $groupRole) {
			if ($groupRole->getId() === $role->getId()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return string[]
	 */
	public function getRights(): array
	{
		if ($this->rights === null) {
			$this->loadRights();
		}

		return $this->rights;
	}

	private function loadRights(): void
	{
		$this->rights = [];

		foreach ($this->getRoles() as $role) {
			foreach ($role->getRights() as $right) {
				$this->rights[] = $right->getSlug();
			}
		}
	}

}