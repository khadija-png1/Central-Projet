<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\NotificationSubscriber;
use App\Service\NotificationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class NotificationSubscriberTest extends TestCase
{
    public function testOnKernelRequestAddsNotificationsGlobals()
    {
        $notifications = ['notif1', 'notif2'];

        $notificationService = $this->createMock(NotificationService::class);
        $notificationService->method('getNotifications')->willReturn($notifications);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->exactly(2))
            ->method('addGlobal')
            ->withConsecutive(
                ['notifications', $notifications],
                ['notification_count', count($notifications)]
            );

        // Mock d'un objet Request (retourné par RequestStack)
        $request = $this->createMock(Request::class);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($request);

        $subscriber = new NotificationSubscriber($notificationService, $twig, $requestStack);

        // Mock de RequestEvent
        $event = $this->getMockBuilder(RequestEvent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isMainRequest', 'getRequest'])
            ->getMock();

        $event->method('isMainRequest')->willReturn(true);
        $event->method('getRequest')->willReturn($request);

        $subscriber->onKernelRequest($event);
    }

    public function testOnKernelRequestDoesNothingIfNotMainRequest()
    {
        $notificationService = $this->createMock(NotificationService::class);
        $twig = $this->createMock(Environment::class);
        $requestStack = $this->createMock(RequestStack::class);

        $subscriber = new NotificationSubscriber($notificationService, $twig, $requestStack);

        $event = $this->getMockBuilder(RequestEvent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isMainRequest'])
            ->getMock();

        $event->method('isMainRequest')->willReturn(false);

        // La méthode onKernelRequest ne doit rien faire ici, donc on vérifie pas de side effects
        $subscriber->onKernelRequest($event);

        $this->assertTrue(true); // juste pour valider qu'il ne plante pas
    }

    public function testOnKernelRequestMarksAsReadWhenAjaxPost()
    {
        $notificationService = $this->createMock(NotificationService::class);
        $notificationService->method('getNotifications')->willReturn([]);
        $notificationService->expects($this->once())
            ->method('markAsRead')
            ->with(123);
        $notificationService->method('countUnreadNotifications')->willReturn(5);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->exactly(2))
            ->method('addGlobal')
            ->withConsecutive(
                ['notifications', []],
                ['notification_count', 5]
            );

        $request = $this->createMock(Request::class);
        $request->method('isXmlHttpRequest')->willReturn(true);
        $request->method('getMethod')->willReturn('POST');
        $request->method('get')->with('id')->willReturn(123);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($request);

        $subscriber = new NotificationSubscriber($notificationService, $twig, $requestStack);

        $event = $this->getMockBuilder(RequestEvent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isMainRequest', 'getRequest'])
            ->getMock();

        $event->method('isMainRequest')->willReturn(true);
        $event->method('getRequest')->willReturn($request);

        $subscriber->onKernelRequest($event);
    }
}
