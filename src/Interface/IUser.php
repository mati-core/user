<?php

declare(strict_types=1);

namespace MatiCore\User;

/**
 * Interface IUser
 * @package MatiCore\User
 */
interface IUser
{

	/**
	 * @return string
	 */
	public function getUsername(): string;

	/**
	 * @return string
	 */
	public function getPassword(): string;

	/**
	 * @param string $password
	 */
	public function setPassword(string $password): void;

	/**
	 * @return string|null
	 */
	public function getFirstName(): ?string;

	/**
	 * @return string|null
	 */
	public function getLastName(): ?string;

	/**
	 * @return string|null
	 */
	public function getEmail(): ?string;

	/**
	 * @return UserGroup
	 */
	public function getGroup(): UserGroup;

	/**
	 * @return string|null
	 */
	public function getPhone(): ?string;

	/**
	 * @return string|null
	 */
	public function getIconPath(): ?string;

	/**
	 * @return bool
	 */
	public function isActive(): bool;

}