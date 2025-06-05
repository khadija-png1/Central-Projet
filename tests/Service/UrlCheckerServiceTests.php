<?php
// tests/Service/UrlCheckerServiceTest.php
namespace App\Tests\Service;

use App\Service\UrlCheckerService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UrlCheckerServiceTests extends TestCase
{
    public function testCheckUrlAddsHttpIfMissingAndReturnsSuccess()
    {
        $url = 'example.com';

        // Mock de la réponse HTTP
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);

        // Mock du client HTTP
        $clientMock = $this->createMock(HttpClientInterface::class);
        // On s'attend à ce que 'request' soit appelé avec 'GET' et 'http://example.com'
        $clientMock->expects($this->once())
            ->method('request')
            ->with('GET', 'http://' . $url, ['timeout' => 5])
            ->willReturn($responseMock);

        $service = new UrlCheckerService($clientMock);

        $result = $service->checkUrl($url);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['status_code']);
        $this->assertEquals('http://' . $url, $result['url']);
        $this->assertNull($result['error']);
    }

    public function testCheckUrlReturnsFailureOnException()
    {
        $url = 'http://example.com';

        // Mock du client HTTP qui lance une exception
        $clientMock = $this->createMock(HttpClientInterface::class);
        $clientMock->method('request')
            ->willThrowException(new \Exception('Timeout'));

        $service = new UrlCheckerService($clientMock);

        $result = $service->checkUrl($url);

        $this->assertFalse($result['success']);
        $this->assertNull($result['status_code']);
        $this->assertEquals($url, $result['url']);
        $this->assertEquals('Timeout', $result['error']);
    }

    public function testCheckUrlReturnsFailureWhenStatusCodeIs400OrMore()
    {
        $url = 'https://example.com';

        // Mock de la réponse HTTP avec status 404
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(404);

        // Mock du client HTTP
        $clientMock = $this->createMock(HttpClientInterface::class);
        $clientMock->method('request')->willReturn($responseMock);

        $service = new UrlCheckerService($clientMock);

        $result = $service->checkUrl($url);

        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['status_code']);
        $this->assertEquals($url, $result['url']);
        $this->assertNull($result['error']);
    }
}
