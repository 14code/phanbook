<?php

namespace App\Test\Unit\Models\Services;

use Faker\Factory as Faker;
use App\Test\Module\UnitTest;
use Phanbook\Models\Users as Entity;
use Phanbook\Models\Services\Service\User;
use Phanbook\Models\Services\Exceptions\EntityNotFoundException;

class UserTest extends UnitTest
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    protected function _before()
    {
        parent::_before();

        $this->faker = Faker::create();
    }

    /** @test */
    public function shouldFindUserByEmail()
    {
        $userService = new User();

        $data = [
            'username'  => $this->faker->userName,
            'firstname' => $this->faker->name,
            'lastname'  => $this->faker->name,
            'email'     => $this->faker->email,
            'bio'       => $this->faker->paragraph,
            'birthdate' => $this->faker->date(),
            'passwd'    => $this->faker->password,
        ];

        $this->tester->haveInDatabase('users', $data);
        $this->assertInstanceOf(Entity::class, $userService->findFirstByEmail($data['email']));
    }

    /** @test */
    public function shouldFindUserByUsername()
    {
        $userService = new User();

        $data = [
            'username'  => $this->faker->userName,
            'firstname' => $this->faker->name,
            'lastname'  => $this->faker->name,
            'email'     => $this->faker->email,
            'bio'       => $this->faker->paragraph,
            'birthdate' => $this->faker->date(),
            'passwd'    => $this->faker->password,
        ];

        $this->tester->haveInDatabase('users', $data);
        $this->assertInstanceOf(Entity::class, $userService->findFirstByUsername($data['username']));
    }

    /** @test */
    public function shouldFindUserById()
    {
        $userService = new User();

        $data = [
            'username'  => $this->faker->userName,
            'firstname' => $this->faker->name,
            'lastname'  => $this->faker->name,
            'email'     => $this->faker->email,
            'bio'       => $this->faker->paragraph,
            'birthdate' => $this->faker->date(),
            'passwd'    => $this->faker->password,
        ];

        $id = $this->tester->haveInDatabase('users', $data);
        $this->assertInstanceOf(Entity::class, $userService->findFirstById($id));
    }

    /** @test */
    public function shouldGetUserByEmail()
    {
        $userService = new User();

        $data = [
            'username'  => $this->faker->userName,
            'firstname' => $this->faker->name,
            'lastname'  => $this->faker->name,
            'email'     => $this->faker->email,
            'bio'       => $this->faker->paragraph,
            'birthdate' => $this->faker->date(),
            'passwd'    => $this->faker->password,
        ];

        $this->tester->haveInDatabase('users', $data);
        $this->assertInstanceOf(Entity::class, $userService->getFirstByEmail($data['email']));
    }

    /** @test */
    public function shouldGetUserByUsername()
    {
        $userService = new User();

        $data = [
            'username'  => $this->faker->userName,
            'firstname' => $this->faker->name,
            'lastname'  => $this->faker->name,
            'email'     => $this->faker->email,
            'bio'       => $this->faker->paragraph,
            'birthdate' => $this->faker->date(),
            'passwd'    => $this->faker->password,
        ];

        $this->tester->haveInDatabase('users', $data);
        $this->assertInstanceOf(Entity::class, $userService->getFirstByUsername($data['username']));
    }

    /** @test */
    public function shouldGetUserById()
    {
        $userService = new User();

        $data = [
            'username'  => $this->faker->userName,
            'firstname' => $this->faker->name,
            'lastname'  => $this->faker->name,
            'email'     => $this->faker->email,
            'bio'       => $this->faker->paragraph,
            'birthdate' => $this->faker->date(),
            'passwd'    => $this->faker->password,
        ];

        $id = $this->tester->haveInDatabase('users', $data);
        $this->assertInstanceOf(Entity::class, $userService->getFirstById($id));
    }

    /**
     * @test
     * @dataProvider providerInvalidId
     * @param mixed $id
     */
    public function shouldThrowExpectedExceptionInCaseOfGetNonExistentEntityById($id)
    {
        $userService = new User();

        $this->tester->dontSeeInDatabase('users', ['id' => $id]);
        $this->tester->expectException(
            new EntityNotFoundException($id),
            function () use ($userService, $id) {
                $userService->getFirstById($id);
            }
        );
    }

    /**
     * @test
     * @dataProvider providerInvalidId
     * @param mixed $id
     */
    public function shouldThrowExpectedExceptionInCaseOfGetNonExistentEntityByEmail($id)
    {
        $userService = new User();

        $this->tester->dontSeeInDatabase('users', ['email' => $id]);
        $this->tester->expectException(
            new EntityNotFoundException($id, 'email'),
            function () use ($userService, $id) {
                $userService->getFirstByEmail($id);
            }
        );
    }

    /** @test */
    public function shouldThrowExpectedExceptionInCaseOfGetNonExistentEntityByUsername()
    {
        $userService = new User();

        $this->tester->dontSeeInDatabase('users', ['username' => 'username']);
        $this->tester->expectException(
            new EntityNotFoundException('username', 'username'),
            function () use ($userService) {
                $userService->getFirstByUsername('username');
            }
        );
    }

    /** @test */
    public function shouldDetectModerators()
    {
        $userService = new User();

        $data = [
            'username'  => $this->faker->userName,
            'firstname' => $this->faker->name,
            'lastname'  => $this->faker->name,
            'email'     => $this->faker->email,
            'bio'       => $this->faker->paragraph,
            'birthdate' => $this->faker->date(),
            'passwd'    => $this->faker->password,
            'moderator' => 'Y',
        ];

        $id = $this->tester->haveInDatabase('users', $data);
        $user = $userService->getFirstById($id);

        $this->assertTrue($userService->isModerator($user));

        $data = [
            'username'  => $this->faker->userName,
            'firstname' => $this->faker->name,
            'lastname'  => $this->faker->name,
            'email'     => $this->faker->email,
            'bio'       => $this->faker->paragraph,
            'birthdate' => $this->faker->date(),
            'passwd'    => $this->faker->password,
            'moderator' => 'N',
        ];

        $id = $this->tester->haveInDatabase('users', $data);
        $user = $userService->getFirstById($id);

        $this->assertFalse($userService->isModerator($user));
    }

    /**
     * @test
     * @dataProvider providerStatus
     * @param int $status
     * @param bool $expected
     */
    public function shouldDetectActivatedUsers($status, $expected)
    {
        $userService = new User();

        $data = [
            'username'  => $this->faker->userName,
            'firstname' => $this->faker->name,
            'lastname'  => $this->faker->name,
            'email'     => $this->faker->email,
            'bio'       => $this->faker->paragraph,
            'birthdate' => $this->faker->date(),
            'passwd'    => $this->faker->password,
            'status'    => $status,
        ];

        $id = $this->tester->haveInDatabase('users', $data);
        $user = $userService->getFirstById($id);

        $this->assertTrue($expected === $userService->isActiveMember($user));
    }

    public function providerStatus()
    {
        return [
            [Entity::STATUS_ACTIVE, true],
            [Entity::STATUS_DISABLED, false],
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_INACTIVE, false],
            [123, false],
            [-90, false],
            [0, false],
        ];
    }

    public function providerInvalidId()
    {
        return [
            [-1],
            [1000000000],
            [null],
            ['non-existent@email.here'],
            [''],
        ];
    }
}