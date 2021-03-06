<?php

namespace App\EventSubscriber;

use App\Services\UserActionLogger;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class UserActionSubscriber implements EventSubscriber
{
    /**
     * @var UserActionLogger
     */
    private $logger;

    public function __construct(UserActionLogger $logger)
    {
        $this->logger = $logger;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->logger->userAction(\get_class($args->getObject()), 'created');
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->logger->userAction(\get_class($args->getObject()), 'updated');
    }
}
