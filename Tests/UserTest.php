<?php

/*
 * This file is part of the Polymorph.
 *
 * (c) Mlanawo Mbechezi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Polymorph\Component\User\Tests;

use PHPUnit\Framework\TestCase;
use Polymorph\Component\User\Model\Email;
use Polymorph\Component\User\Model\User;

class UserTest extends TestCase
{
    const EMAIL = 'demo@polymorph.com';

    public function testSetAutomaticallyMain()
    {
        $user = new UserEntity();
        $user->addEmail(new EmailEntity(self::EMAIL));

        $this->assertSame($user->getEmail(), self::EMAIL);
    }

    public function testMain()
    {
        $email = new EmailEntity(self::EMAIL);
        $email->setMain(true);

        $user = new UserEntity();
        $user->addEmail(new EmailEntity('demo@polymorph.io'));
        $user->addEmail($email);

        $this->assertSame($user->getEmail(), self::EMAIL);
    }
}

class EmailEntity extends Email
{
}
class UserEntity extends User
{
}
