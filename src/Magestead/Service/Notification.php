<?php namespace Magestead\Service;

use Joli\JoliNotif\Notification as Notify;
use Joli\JoliNotif\NotifierFactory;

class Notification
{
    public static function send($message)
    {
        $notifier = NotifierFactory::create();
        $basePath = dirname( __FILE__ ) . '/../../../';
        $notification =
            (new Notify())
                ->setTitle('Magestead')
                ->setBody($message)
                ->setIcon($basePath .'assets/magentologo.png')
        ;

        $notifier->send($notification);
    }
}