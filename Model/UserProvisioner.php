<?php

declare(strict_types=1);

namespace HyTales\OneLinkLogin\Model;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;
use Random\RandomException;

class UserProvisioner
{
    /**
     * @param UserFactory $userFactory
     * @param UserResource $userResource
     * @param UserCollectionFactory $userCollectionFactory
     */
    public function __construct(
        private readonly UserFactory $userFactory,
        private readonly UserResource $userResource,
        private readonly UserCollectionFactory $userCollectionFactory
    ) {
    }

    /**
     * @param array{label: string, email: string, role_id: int} $account
     *
     * @return User
     * @throws AlreadyExistsException|RandomException
     */
    public function getOrCreateUser(array $account): User
    {
        $user = $this->getExistingUser($account['email']);

        if ($user === null) {
            return $this->createUser($account);
        }

        $this->syncRole($user, $account);

        return $user;
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    private function getExistingUser(string $email): ?User
    {
        $user = $this->userCollectionFactory->create()
            ->addFieldToFilter('email', $email)
            ->setPageSize(1)
            ->getFirstItem();

        return $user->getId() ? $user : null;
    }

    /**
     * @param User $user
     * @param array{label: string, email: string, role_id: int} $account
     *
     * @return void
     * @throws AlreadyExistsException
     */
    private function syncRole(User $user, array $account): void
    {
        $currentRoleId = (int) $user->getRole()->getId();
        $configuredRoleId = (int) $account['role_id'];

        if ($currentRoleId !== $configuredRoleId) {
            $user->setRoleId($configuredRoleId);
            $this->userResource->save($user);
        }
    }

    /**
     * @param array{label: string, email: string, role_id: int} $account
     *
     * @return User
     * @throws RandomException|AlreadyExistsException
     */
    private function createUser(array $account): User
    {
        $user = $this->userFactory->create();

        $user->setFirstname($account['label'] ?: 'One-Link')
            ->setLastname('Login')
            ->setUsername($account['email'])
            ->setEmail($account['email'])
            ->setPassword(bin2hex(random_bytes(16)))
            ->setIsActive(1)
            ->setRoleId((int) $account['role_id']);

        $this->userResource->save($user);
        $user->isObjectNew(false);

        return $user;
    }
}
