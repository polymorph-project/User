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
     */
    public function setUsername(string $username): self;

    /**
     * Gets the canonical username in search and sort queries.
     */
    public function getSlug(): ?string;

    /**
     * Sets the canonical username.
     */
    public function setSlug(string $slug): self;

    /**
     * Gets email.
     */
    public function getEmail(): ?string;

    /**
     * Gets the plain password.
     */
    public function getPlainPassword(): ?string;

    /**
     * Sets the plain password.
     */
    public function setPlainPassword(string $password): self;

    /**
     * Sets the hashed password.
     */
    public function setPassword(string $password): self;

    /**
     * Tells if the the given user has the super admin role.
     */
    public function isSuperAdmin(): bool;

    public function setEnabled(bool $boolean): self;

    /**
     * Sets the locking status of the user.
     */
    public function setLocked(bool $boolean): self;

    /**
     * Sets the super admin status.
     */
    public function setSuperAdmin(bool $boolean): self;

    /**
     * Gets the confirmation token.
     */
    public function getConfirmationToken(): ?string;

    /**
     * Sets the confirmation token.
     */
    public function setConfirmationToken(string $confirmationToken): self;

    /**
     * Sets the timestamp that the user requested a password reset.
     */
    public function setPasswordRequestedAt(?\DateTimeInterface $date = null): self;

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
     */
    public function setLastLogin(?\DateTimeInterface $time = null): self;

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
     */
    public function setRoles(array $roles): self;

    /**
     * Adds a role to the user.
     */
    public function addRole(string $role): self;

    /**
     * Removes a role to the user.
     */
    public function removeRole(string $role): self;
}
