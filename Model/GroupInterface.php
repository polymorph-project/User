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
     *
     * @return self
     */
    public function addRole($role): \Polymorph\Component\User\Model\GroupInterface;

    /**
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role): bool;

    /**
     * @return array
     */
    public function getRoles(): array;

    /**
     * @param string $role
     *
     * @return self
     */
    public function removeRole($role): \Polymorph\Component\User\Model\GroupInterface;

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name): \Polymorph\Component\User\Model\GroupInterface;

    /**
     * @return self
     */
    public function setRoles(array $roles): \Polymorph\Component\User\Model\GroupInterface;
}
