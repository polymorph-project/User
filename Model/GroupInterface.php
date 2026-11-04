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

/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupInterface
{
    /**
     * @param string $role
     */
    public function addRole($role): self;

    public function getId(): ?string;

    public function getName(): ?string;

    /**
     * @param string $role
     */
    public function hasRole($role): bool;

    public function getRoles(): array;

    /**
     * @param string $role
     */
    public function removeRole($role): self;

    /**
     * @param string $name
     */
    public function setName($name): self;

    public function setRoles(array $roles): self;
}
