<?php

namespace Servex\Core\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class HttpTransport implements TransportInterface
{
    private Client $client;
    private LoggerInterface $logger;
    private string $baseUrl;
    private array $subscribers = [];
    private bool $connected = false;

    public function __construct(string $baseUrl = 'http://localhost:3000', ?LoggerInterface $logger = null)
    {
        $this->baseUrl = $baseUrl;
        $this->client = new Client(['base_uri' => $baseUrl, 'timeout' => 5.0]);
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @inheritDoc
     */
    public function call(string $actionName, array $params = []): mixed
    {
        if (!$this->connected) {
            throw new \RuntimeException("Transport not connected");
        }

        try {
            $response = $this->client->post('/api/call', [
                'json' => [
                    'action' => $actionName,
                    'params' => $params,
                ],
            ]);
            
            $result = json_decode($response->getBody()->getContents(), true);
            return $result['data'] ?? null;
        } catch (GuzzleException $e) {
            $this->logger->error("Error calling action {$actionName}: " . $e->getMessage());
            throw new \RuntimeException("Error calling action: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function connect(): void
    {
        if ($this->connected) {
            return;
        }
        
        try {
            $response = $this->client->get('/api/health');
            if ($response->getStatusCode() === 200) {
                $this->connected = true;
                $this->logger->info("Connected to HTTP transport at {$this->baseUrl}");
            } else {
                throw new \RuntimeException("Failed to connect to HTTP transport");
            }
        } catch (GuzzleException $e) {
            $this->logger->error("Error connecting to HTTP transport: " . $e->getMessage());
            throw new \RuntimeException("Error connecting to HTTP transport: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function disconnect(): void
    {
        $this->connected = false;
        $this->logger->info("Disconnected from HTTP transport");
    }

    /**
     * @inheritDoc
     */
    public function broadcast(string $topic, mixed $data): void
    {
        if (!$this->connected) {
            throw new \RuntimeException("Transport not connected");
        }
        
        try {
            $this->client->post('/api/broadcast', [
                'json' => [
                    'topic' => $topic,
                    'data' => $data,
                ],
            ]);
            
            $this->logger->debug("Broadcasted message to topic: {$topic}");
        } catch (GuzzleException $e) {
            $this->logger->error("Error broadcasting to topic {$topic}: " . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function subscribe(string $topic, callable $handler): void
    {
        $this->subscribers[$topic][] = $handler;
        $this->logger->debug("Subscribed to topic: {$topic}");
        
        // In a real implementation, we would need to set up a webhook or long-polling
        // mechanism to receive messages from the server. For simplicity, we're just
        // storing the handlers locally.
    }
}