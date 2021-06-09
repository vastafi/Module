<?php


namespace App\EventListener;


use App\Entity\Order;
use Doctrine\ORM\Event\LifecycleEventArgs;

class OrderChangeNotifier
{
    public function postUpdate(LifecycleEventArgs $event): void
    {
        $order = $event->getEntity();
        $em = $event->getEntityManager();
        if ($order instanceof Order) {
            $changeSet = $em->getUnitOfWork()->getEntityChangeSet($order);
            dd($changeSet);
        }
    }

}