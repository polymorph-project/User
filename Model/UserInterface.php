<?php

/*
 * This file is part of the Polymorph.
 *
 * (c) Mlanawo Mbechezi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Polymorph\Component\User\Model;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    /**
     * Sets the username.
     *
     * @return self
     */
    public function setUsername(string $username): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Gets the canonical username in search and sort queries.
     *
     * @return string|null
     */
    public function getSlug(): ?string;

    /**
     * Sets the canonical username.
     *
     * @return self
     */
    public function setSlug(string $slug): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Gets email.
     *
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * Gets the plain password.
     *
     * @return string|null
     */
    public function getPlainPassword(): ?string;

    /**
     * Sets the plain password.
     *
     * @return self
     */
    public function setPlainPassword(string $password): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Sets the hashed password.
     *
     * @return self
     */
    public function setPassword(string $password): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Tells if the the given user has the super admin role.
     */
    public function isSuperAdmin(): bool;

    /**
     * @return self
     */
    public function setEnabled(bool $boolean): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Sets the locking status of the user.
     *
     * @return self
     */
    public function setLocked(bool $boolean): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Sets the super admin status.
     *
     * @return self
     */
    public function setSuperAdmin(bool $boolean): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Gets the confirmation token.
     *
     * @return string|null
     */
    public function getConfirmationToken(): ?string;

    /**
     * Sets the confirmation token.
     *
     * @return self
     */
    public function setConfirmationToken(string $confirmationToken): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @return self
     */
    public function setPasswordRequestedAt(?\DateTimeInterface $date = null): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Checks whether the password reset request has expired.
     *
     * @param int $ttl Requests older than this many seconds will be considered expired
     *
     * @return bool true if the user's password request is non expired, false otherwise
     */
    public function isPasswordRequestNonExpired($ttl): bool;

    /**
     * Sets the last login time.
     *
     * @param \DateTime $time
     *
     * @return self
     */
    public function setLastLogin(?\DateTimeInterface $time = null): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     * $securityContext->isGranted('ROLE_USER');
     */
    public function hasRole(string $role): bool;

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @return self
     */
    public function setRoles(array $roles): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Adds a role to the user.
     *
     * @return self
     */
    public function addRole(string $role): \Polymorph\Component\User\Model\UserInterface;

    /**
     * Removes a role to the user.
     *
     * @return self
     */
    public function removeRole(string $role): \Polymorph\Component\User\Model\UserInterface;
}
