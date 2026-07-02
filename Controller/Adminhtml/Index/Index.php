<?php

declare(strict_types=1);

namespace HyTales\OneLinkLogin\Controller\Adminhtml\Index;

use HyTales\OneLinkLogin\Model\Config;
use HyTales\OneLinkLogin\Model\UserProvisioner;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Backend\Model\UrlInterface as BackendUrl;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NotFoundException;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Random\RandomException;

class Index implements ActionInterface, HttpGetActionInterface
{
    /**
     * @param RequestInterface $request
     * @param AdminSession $adminSession
     * @param BackendUrl $backendUrl
     * @param UserProvisioner $userProvisioner
     * @param TfaSessionInterface $tfaSession
     * @param State $state
     * @param Config $config
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly AdminSession $adminSession,
        private readonly BackendUrl $backendUrl,
        private readonly UserProvisioner $userProvisioner,
        private readonly TfaSessionInterface $tfaSession,
        private readonly State $state,
        private readonly Config $config,
        private readonly ResultFactory $resultFactory
    ) {
    }

    /**
     * @return Redirect
     * @throws AlreadyExistsException
     * @throws NotFoundException
     * @throws RandomException
     */
    public function execute(): Redirect
    {
        if ($this->state->getMode() !== State::MODE_DEVELOPER || !$this->config->isEnabled()) {
            throw new NotFoundException(__('Page not found.'));
        }

        $account = $this->config->getAccountByEmail((string) $this->request->getParam('email'));

        if ($account === null) {
            throw new NotFoundException(__('Page not found.'));
        }

        $user = $this->userProvisioner->getOrCreateUser($account);

        $this->adminSession->setUser($user);
        $this->adminSession->processLogin();
        $this->tfaSession->grantAccess();

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->backendUrl->getStartupPageUrl());

        return $resultRedirect;
    }
}
