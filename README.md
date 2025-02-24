Servex - A Microservice-Based PHP Framework
=================================================

Overview
--------

`ildrm/servex` emerges as a visionary microservice-based PHP framework, drawing inspiration from the acclaimed [Moleculer](https://en.wikipedia.org/wiki/Moleculer) framework, meticulously engineered to architect scalable, event-driven, and distributed applications. Constructed on the robust foundation of PHP 8.1+, it adheres with precision to the [PSR standards](https://en.wikipedia.org/wiki/PHP_Standard_Recommendation)—PSR-4 for autoloading, PSR-11 for containers, PSR-14 for event dispatching, and PSR-16 for simple caching—ensuring unparalleled interoperability and maintainability. Designed as a paragon for developers, system architects, software engineers, and IT business leaders, `ildrm/servex` empowers the creation of loosely coupled microservices that communicate asynchronously through events and messages, epitomizing modern [software architecture](https://en.wikipedia.org/wiki/Software_architecture).

This framework orchestrates a modular masterpiece, featuring a central `EventBus` as the nexus of event management, a sophisticated `ServiceManager` for service orchestration, and robust support for caching, database operations, authentication, and transport layers. It stands as an exemplar for large-scale, distributed systems, embodying the principles of [SOLID](https://en.wikipedia.org/wiki/SOLID) and [design patterns](https://en.wikipedia.org/wiki/Design_patterns) like [publish-subscribe](https://en.wikipedia.org/wiki/Publish%E2%80%93subscribe_pattern) and [dependency injection](https://en.wikipedia.org/wiki/Dependency_injection).

Architecture
------------

`ildrm/servex` is a triumph of [microservices architecture](https://en.wikipedia.org/wiki/Microservices), harnessing an event-driven paradigm that epitomizes the elegance of [event-driven architecture](https://en.wikipedia.org/wiki/Event-driven_architecture). Below, we unveil its conceptual design through meticulously crafted SVG diagrams, followed by a detailed exposition of its components and their interactions, a masterpiece for architects and engineers seeking to sculpt distributed systems:

### Internal Architecture Diagram

ServiceManager EventBus UserService CacheManager Register Emit Cache

This diagram encapsulates the internal symphony of `ildrm/servex`, where `ServiceManager` orchestrates service registration, `EventBus` conducts event-driven interactions, `UserService` performs user operations, and `CacheManager` optimizes performance with Redis caching, adhering to [design by contract](https://en.wikipedia.org/wiki/Design_by_contract) principles.

### External Service Integration Diagram

UserService EventBus OrderService MessageBroker Emit Broadcast Subscribe Publish

This masterpiece illustrates external interactions, where `UserService` emits events to `EventBus`, which broadcasts to `OrderService` and publishes to an external `MessageBroker`, ensuring [scalability](https://en.wikipedia.org/wiki/Scalability) and [fault tolerance](https://en.wikipedia.org/wiki/Fault_tolerance) for distributed systems.

The architecture adheres to [domain-driven design](https://en.wikipedia.org/wiki/Domain-driven_design) (DDD), segregating concerns into bounded contexts—services like `UserService` and `OrderService` encapsulate business logic, while infrastructural components like `CacheManager` and `DatabaseManager` manage persistence and performance, orchestrated by `ServiceManager` and `EventBus`.

Features
--------

`ildrm/servex` is a tour de force of software engineering, offering a constellation of features that resonate with the aspirations of developers, architects, engineers, and IT visionaries:

*   **Event-Driven Communication**: A virtuoso performance in asynchronous event handling, powered by `EventBus` and seamlessly integrated with industry-leading message queues like RabbitMQ, Kafka, ActiveMQ, NATS, Redis RQ, AMQ, and HiveMQ, embodying the [publish-subscribe pattern](https://en.wikipedia.org/wiki/Publish%E2%80%93subscribe_pattern).
*   **Dependency Injection**: Implements a PSR-11 compliant `Container`, a paragon of [dependency injection](https://en.wikipedia.org/wiki/Dependency_injection), ensuring [loose coupling](https://en.wikipedia.org/wiki/Loose_coupling) and facilitating [unit testing](https://en.wikipedia.org/wiki/Unit\_testing).
*   **Database Integration**: Delivers a symphony of persistence with robust MySQL support via `Doctrine ORM` and PDO, augmented by Redis caching with `Predis`, optimizing performance per [caching patterns](https://en.wikipedia.org/wiki/Caching_patterns).
*   **Authentication & Security**: Orchestrates enterprise-grade security with JWT-based authentication via `AuthManager` and `AuthMiddleware`, extensible for integration with Keycloak, Auth0, and OpenID Connect, adhering to [security by design](https://en.wikipedia.org/wiki/Security_by_design) principles.
*   **Scalable Design**: A masterpiece of [scalability](https://en.wikipedia.org/wiki/Scalability), supporting microservices with `HttpTransport` for synchronous calls and message brokers for asynchronous communication, aligning with [distributed systems](https://en.wikipedia.org/wiki/Distributed_systems) paradigms.
*   **GraphQL Support**: Offers extensible integration with GraphQL via `webonyx/graphql-php`, enabling declarative data fetching and flexible API querying, a triumph for [API design](https://en.wikipedia.org/wiki/API).
*   **Testing Framework**: Enshrines [test-driven development](https://en.wikipedia.org/wiki/Test-driven_development) with comprehensive unit tests using PHPUnit, bolstered by PHPStan for static analysis, ensuring code quality per [code review](https://en.wikipedia.org/wiki/Code_review) standards.
*   **PSR Compliance**: Strictly adheres to [PSR standards](https://en.wikipedia.org/wiki/PHP_Standard_Recommendation) (PSR-4, PSR-11, PSR-14, PSR-16), guaranteeing interoperability and consistency, a hallmark of [software design patterns](https://en.wikipedia.org/wiki/Software_design_pattern).

Integration with Message Queues
-------------------------------

`ildrm/servex` stands as a luminary in the realm of [message-oriented middleware](https://en.wikipedia.org/wiki/Message-oriented_middleware), engineered to harmonize seamlessly with an ensemble of industry-standard message queue systems, enabling asynchronous communication critical for microservices. Below, we unveil a magisterial breakdown of integration strategies, each a testament to [asynchronous I/O](https://en.wikipedia.org/wiki/Asynchronous_I/O) excellence:

- Message Queue
- Integration Approach
- Considerations

**Kafka**

Harness `rdkafka/kafka-php` or `longman/kafka` to orchestrate event publishing and consumption via `EventBus`, configuring topics for prodigious throughput, adhering to the [publish-subscribe pattern](https://en.wikipedia.org/wiki/Publish%E2%80%93subscribe_pattern). Extend `EventBus` to leverage Kafka’s distributed log architecture, ensuring [fault tolerance](https://en.wikipedia.org/wiki/Fault_tolerance) and scalability.

Demands a Kafka cluster, offering unparalleled performance for large-scale applications, yet necessitates meticulous PHP configuration, aligning with [big data](https://en.wikipedia.org/wiki/Big_data) paradigms.

**RabbitMQ**

Integrate with `php-amqplib/php-amqplib` to publish events to RabbitMQ queues and exchanges, and consume them with precision. Extend `EventBus` to master AMQP protocols, enabling sophisticated [complex event processing](https://en.wikipedia.org/wiki/Complex_event_processing) and routing, embodying [enterprise messaging systems](https://en.wikipedia.org/wiki/Enterprise_messaging_system) excellence.

Lightweight, reliable, and adept at complex routing, ideal for microservices with diverse message patterns, requiring an AMQP server for deployment, per [message queue](https://en.wikipedia.org/wiki/Message_queue) best practices.

**ActiveMQ**

Employ `stomp-php/stomp-php` to connect to ActiveMQ via the STOMP protocol, extending `EventBus` to publish and subscribe to queues or topics, ensuring compatibility with [Java Message Service](https://en.wikipedia.org/wiki/Java_Message_Service) (JMS) systems, a testament to [interoperability](https://en.wikipedia.org/wiki/Interoperability).

Supports JMS and STOMP, yet PHP support remains less prevalent, demanding meticulous library selection and rigorous [software testing](https://en.wikipedia.org/wiki/Software_testing) for legacy integrations.

**NATS**

Integrate with `natsserver/php-nats` to harness NATS for lightweight, high-performance messaging, extending `EventBus` for subjects, optimized for [real-time computing](https://en.wikipedia.org/wiki/Real-time_computing) scenarios, leveraging [pub/sub](https://en.wikipedia.org/wiki/Pub/sub) patterns.

Exemplifies simplicity and real-time capabilities, suitable for IoT and streaming applications, yet less feature-rich for intricate routing, requiring careful architectural consideration.

**Redis RQ**

Leverage `predis/predis` to implement Redis-based queues, extending `EventBus` to utilize Redis lists or sorted sets for queuing, augmented by pub/sub for events, adhering to [in-memory data grid](https://en.wikipedia.org/wiki/In-memory_data_grid) principles.

Effortlessly integrates with existing Redis setups, yet less robust for complex patterns compared to dedicated brokers, ideal for prototyping or lightweight systems.

**AMQ (Apache Qpid)**

Utilize `php-amqplib/php-amqplib` or STOMP libraries to connect to AMQ, extending `EventBus` for AMQP or STOMP-based communication, ensuring [message durability](https://en.wikipedia.org/wiki/Message_durability) and reliability.

Resembles RabbitMQ in functionality but is less common in PHP ecosystems, necessitating thorough testing for compatibility, suitable for AMQP-centric environments.

**HiveMQ**

Integrate with `php-mqtt/php-mqtt` for MQTT, extending `EventBus` to publish and subscribe to MQTT topics, optimized for [Internet of Things](https://en.wikipedia.org/wiki/Internet_of_things) (IoT) and real-time applications, following the [MQTT protocol](https://en.wikipedia.org/wiki/MQTT).

Excels in IoT and real-time data streams, yet less aligned with traditional microservice patterns due to MQTT’s pub/sub nature, requiring specialized architectural design.

| Message Queue   | Integration Approach                                                                 | Considerations                                      |
|-----------------|-------------------------------------------------------------------------------------|----------------------------------------------------|
| **Kafka**       | Use `rdkafka/kafka-php` or `longman/kafka` for `EventBus` event handling, leveraging Kafka’s distributed logs for [fault tolerance](https://en.wikipedia.org/wiki/Fault_tolerance) and scalability. | Requires Kafka cluster, high performance for large-scale apps, but complex PHP setup per [big data](https://en.wikipedia.org/wiki/Big_data) needs. |
| **RabbitMQ**    | Integrate with `php-amqplib/php-amqplib` for AMQP queues/exchanges via `EventBus`, enabling [complex event processing](https://en.wikipedia.org/wiki/Complex_event_processing). | Lightweight, reliable, supports complex routing, needs AMQP server per [message queue](https://en.wikipedia.org/wiki/Message_queue). |
| **ActiveMQ**    | Use `stomp-php/stomp-php` for STOMP, extending `EventBus` for JMS compatibility.    | Supports JMS/STOMP, less PHP support, needs rigorous [software testing](https://en.wikipedia.org/wiki/Software_testing) for legacy systems. |
| **NATS**        | Integrate with `natsserver/php-nats` for lightweight pub/sub via `EventBus`, optimized for [real-time computing](https://en.wikipedia.org/wiki/Real-time_computing). | Simple, real-time for IoT/streaming, but limited for complex routing. |
| **Redis RQ**    | Leverage `predis/predis` for Redis queues/lists, extending `EventBus` with pub/sub, per [in-memory data grid](https://en.wikipedia.org/wiki/In-memory_data_grid) principles. | Easy with Redis, but less robust for complex patterns, ideal for prototyping. |
| **AMQ (Apache Qpid)** | Use `php-amqplib/php-amqplib` or STOMP for AMQP, ensuring [message durability](https://en.wikipedia.org/wiki/Message_durability). | Similar to RabbitMQ, less PHP adoption, requires thorough testing for AMQP compatibility. |
| **HiveMQ**      | Integrate with `php-mqtt/php-mqtt` for MQTT topics via `EventBus`, optimized for [Internet of Things](https://en.wikipedia.org/wiki/Internet_of_things). | Ideal for IoT/real-time, but less suited for traditional microservices due to [MQTT protocol](https://en.wikipedia.org/wiki/MQTT) pub/sub. |

**Implementation Mastery:** To integrate with any message queue, elevate the `EventBus` class in `src/Core/EventBus.php` with methods for publishing to and subscribing from the chosen broker. Employ corresponding PHP libraries—such as `php-amqplib` for RabbitMQ or `rdkafka` for Kafka—and configure connection settings in `.env` or `config/config.php`, adhering to [software design patterns](https://en.wikipedia.org/wiki/Software_design_pattern) like [Factory Pattern](https://en.wikipedia.org/wiki/Factory\_method\_pattern) for broker instantiation.

**Architectural Insight:** Ensure the message broker is operational and accessible—whether via Docker, cloud services, or on-premises infrastructure—before integration. Rigorously test for latency, throughput, and reliability, aligning with [software performance engineering](https://en.wikipedia.org/wiki/Software_performance_engineering) and [non-functional requirements](https://en.wikipedia.org/wiki/Non-functional_requirement) for your microservice ecosystem.

GraphQL Integration
-------------------

`ildrm/servex` unveils a transformative capability to embrace [GraphQL](https://en.wikipedia.org/wiki/GraphQL), a query language for APIs that redefines data fetching with declarative precision, empowering developers and architects to sculpt flexible, client-driven interfaces. This integration, a beacon of [API design](https://en.wikipedia.org/wiki/API) brilliance, is achieved as follows, a testament to [domain-specific language](https://en.wikipedia.org/wiki/Domain-specific\_language) mastery:

1.  **Install a GraphQL Library**: Enlist the prowess of `webonyx/graphql-php` or `overblog/graphiql-bundle` for PHP GraphQL support. Augment `composer.json` with:
    
        composer require webonyx/graphql-php
    
2.  **Define Schema**: Craft a GraphQL schema (e.g., in `src/Schema/UserSchema.php`) for services like `UserService`, a masterpiece of [schema design](https://en.wikipedia.org/wiki/Database\_schema) and [object-oriented programming](https://en.wikipedia.org/wiki/Object-oriented\_programming):
    
         'User',
            'fields' => [
                'id' => Type::int(),
                'name' => Type::string(),
            ]
        ]);
    
3.  **Integrate with HttpTransport**: Elevate `HttpTransport` to orchestrate GraphQL queries, routing them to `ServiceManager` for service invocations, a symphony of [RESTful architecture](https://en.wikipedia.org/wiki/Representational\_state\_transfer) and GraphQL harmony:
    
        toArray();
                }
                // Existing HTTP logic, adhering to [HTTP protocol](https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol)
                $response = $this->client->post($endpoint, [
                    'json' => ['method' => $method, 'params' => $params],
                ]);
                return json_decode($response->getBody()->getContents(), true);
            }
        }
    
4.  **Test and Deploy**: Validate GraphQL endpoints with PHPUnit, ensuring they return expected data from services like `UserService`, following [unit testing](https://en.wikipedia.org/wiki/Unit_testing) rigor. Deploy via Docker or a web server, leveraging [continuous deployment](https://en.wikipedia.org/wiki/Continuous_deployment) for production, a pinnacle of [DevOps](https://en.wikipedia.org/wiki/DevOps) excellence.

**Architectural Brilliance:** GraphQL integration enhances flexibility for client queries, but demands meticulous schema design and performance optimization for large-scale microservices, adhering to [performance tuning](https://en.wikipedia.org/wiki/Performance\_tuning) and [load balancing](https://en.wikipedia.org/wiki/Load\_balancing\_(computing)) principles.

**Security Imperative:** Fortify GraphQL endpoints with `AuthMiddleware` to thwart unauthorized access, especially for sensitive data like user information, aligning with [security by design](https://en.wikipedia.org/wiki/Security_by_design) and [OWASP](https://en.wikipedia.org/wiki/OWASP) best practices.

Authentication Integration (Keycloak, Auth0, OpenID)
----------------------------------------------------

`ildrm/servex` emerges as a guardian of enterprise-grade security, offering seamless integration with preeminent authentication systems—Keycloak, Auth0, and OpenID Connect—redefining [identity management](https://en.wikipedia.org/wiki/Identity\_management) for microservices. This integration, a pinnacle of [single sign-on](https://en.wikipedia.org/wiki/Single\_sign-on) (SSO) and [OAuth 2.0](https://en.wikipedia.org/wiki/OAuth) sophistication, is detailed below:

- Authentication System
- Integration Approach
- Considerations

**Keycloak**

Enlist `keycloak/keycloak-php-client` or `league/oauth2-client` to orchestrate integration with Keycloak, extending `AuthManager` to wield OpenID Connect tokens, validating them via Keycloak’s token endpoint with surgical precision. Configure `AuthMiddleware` to scrutinize Keycloak JWTs, ensuring [role-based access control](https://en.wikipedia.org/wiki/Role-based\_access\_control) (RBAC) enforcement.

Demands a Keycloak server, offering advanced RBAC and SSO capabilities, yet introduces complexity for configuration and maintenance, a challenge for [enterprise architecture](https://en.wikipedia.org/wiki/Enterprise\_architecture).

**Auth0**

Integrate with `auth0/auth0-php` to master Auth0 JWTs, extending `AuthManager` to validate tokens via Auth0’s API with elegance, and fortify `AuthMiddleware` for authentication checks, adhering to [OAuth 2.0](https://en.wikipedia.org/wiki/OAuth) protocols.

Cloud-based and facile to configure, yet mandates an Auth0 account and may incur costs for enterprise-scale usage, aligning with [SaaS](https://en.wikipedia.org/wiki/Software\_as\_a\_service) paradigms.

**OpenID Connect**

Harness `thephpleague/oauth2-client` with an OpenID provider, extending `AuthManager` to validate OpenID tokens with artistry, mapping claims to user roles, and securing endpoints with `AuthMiddleware`, embracing [federated identity](https://en.wikipedia.org/wiki/Federated\_identity) standards.

Highly interoperable, yet requires meticulous configuration of the OpenID provider and token validation logic, a challenge for [identity federation](https://en.wikipedia.org/wiki/Identity\_federation) architects.

| Authentication System | Integration Approach                                                                 | Considerations                                      |
|-----------------------|-------------------------------------------------------------------------------------|----------------------------------------------------|
| **Keycloak**          | Use `keycloak/keycloak-php-client` or `league/oauth2-client` for OpenID Connect, extending `AuthManager` for RBAC via JWTs. | Requires Keycloak server, advanced RBAC/SSO, but complex for [enterprise architecture](https://en.wikipedia.org/wiki/Enterprise_architecture). |
| **Auth0**             | Integrate with `auth0/auth0-php` for JWT validation, extending `AuthManager` per [OAuth 2.0](https://en.wikipedia.org/wiki/OAuth). | Cloud-based, easy setup, but costs for enterprise use, aligns with [SaaS](https://en.wikipedia.org/wiki/Software_as_a_service). |
| **OpenID Connect**    | Use `thephpleague/oauth2-client` with an OpenID provider, extending `AuthManager` for [federated identity](https://en.wikipedia.org/wiki/Federated_identity). | Highly interoperable, but complex configuration, challenging for [identity federation](https://en.wikipedia.org/wiki/Identity_federation) architects. |

**Engineering Mastery:** Refine `AuthManager.php` and `AuthMiddleware.php` with methods for token validation, configuring settings in `.env` (e.g., `KEYCLOAK_URL`, `AUTH0_DOMAIN`, `OPENID_ISSUER`), and adhere to [security patterns](https://en.wikipedia.org/wiki/Security\_pattern) like [token-based authentication](https://en.wikipedia.org/wiki/Token-based\_authentication).

**Security Vanguard:** Safeguard client secrets and tokens with cryptographic rigor, and rigorously test authentication flows for vulnerabilities—such as token tampering or expiration—ensuring compliance with [OWASP Top Ten](https://en.wikipedia.org/wiki/OWASP\_Top\_Ten) and [security by design](https://en.wikipedia.org/wiki/Security\_by\_design) doctrines.

Servex as a Message Broker
--------------------------------

While `ildrm/servex` primarily reigns as a framework for microservices, its `EventBus` ascends as a virtuoso, capable of embodying a lightweight message broker for small-scale applications or serving as a proxy to external message brokers. This orchestration, a marvel of [message-oriented middleware](https://en.wikipedia.org/wiki/Message-oriented\_middleware), unfolds as follows, a beacon for architects of [distributed systems](https://en.wikipedia.org/wiki/Distributed\_computing):

1.  **Role as Message Broker**: Elevate `EventBus` in `src/Core/EventBus.php` to orchestrate basic pub/sub functionality, leveraging in-memory queues or Redis (via `Predis`) with virtuosity. For instance, harness Redis pub/sub or lists for queuing events, a paradigm of [in-memory data grid](https://en.wikipedia.org/wiki/In-memory\_data\_grid) elegance:
    
        dispatcher = new EventDispatcher();
                $this->redis = $redis;
            }
        
            public function emit(string $eventName, array $data = []): void
            {
                $event = new class extends \Symfony\Contracts\EventDispatcher\Event {
                    public array $data;
                };
                $event->data = $data;
                $this->dispatcher->dispatch($event, $eventName);
        
                // Publish to Redis (if configured), a pinnacle of [publish-subscribe pattern](https://en.wikipedia.org/wiki/Publish%E2%80%93subscribe_pattern)
                if ($this->redis) {
                    $this->redis->publish($eventName, json_encode($data));
                }
            }
        
            public function on(string $eventName, callable $callback): void
            {
                $this->dispatcher->addListener($eventName, function ($event) use ($callback) {
                    $callback($event->data);
                });
        
                // Subscribe to Redis (if configured), embodying [asynchronous programming](https://en.wikipedia.org/wiki/Asynchronous_programming)
                if ($this->redis) {
                    $this->redis->subscribe([$eventName], function ($message) use ($callback) {
                        $callback(json_decode($message, true));
                    });
                }
            }
        }
    
2.  **Service Interaction**: Services like `UserService` and `OrderService` engage with `EventBus` in a ballet of communication, as follows, a symphony of [service-oriented architecture](https://en.wikipedia.org/wiki/Service-oriented\_architecture):
    *   **Publishing Events**: A service, such as `UserService`, invokes `EventBus::emit` to publish an event, a pinnacle of [event sourcing](https://en.wikipedia.org/wiki/Event\_sourcing):
        
            $eventBus->emit('user.created', ['id' => 1, 'name' => 'Test User']);
        
    *   **Subscribing to Events**: Another service, such as `OrderService`, subscribes to events via `EventBus::on`, a masterpiece of [event-driven programming](https://en.wikipedia.org/wiki/Event-driven\_programming):
        
            $eventBus->on('user.created', function (array $data) {
                                        echo "Processing order for user: " . $data['name'] . PHP_EOL;
                                    });
        
3.  **Limitations**: Deploying `EventBus` as a message broker excels for small-scale or prototyping endeavors, yet for production environments, it’s imperative to enlist dedicated brokers—such as RabbitMQ or Kafka—due to their advanced features, including durability, scalability, and complex routing, aligning with [enterprise messaging systems](https://en.wikipedia.org/wiki/Enterprise\_messaging\_system) and [message queue](https://en.wikipedia.org/wiki/Message\_queue) standards.

**Architectural Virtuosity:** For colossal applications, configure `EventBus` to delegate to an external message broker, employing libraries like `php-amqplib` or `rdkafka`, and orchestrate queue persistence and failover externally, a pinnacle of [distributed system design](https://en.wikipedia.org/wiki/Distributed\_systems).

**Critical Caution:** Refrain from relying solely on `EventBus` as a primary message broker for mission-critical systems without durability and scalability features; leverage dedicated brokers to uphold [high availability](https://en.wikipedia.org/wiki/High\_availability) and [fault tolerance](https://en.wikipedia.org/wiki/Fault\_tolerance) in production environments.

Installation
------------

To deploy `ildrm/servex`—a magnum opus of PHP microservices—requires PHP 8.1 or higher, Composer, and a harmonized environment for Redis and MySQL. Embark on this journey with the following steps, a beacon of [software deployment](https://en.wikipedia.org/wiki/Software\_deployment) excellence:

1.  Clone the repository or procure the source code, a testament to [version control](https://en.wikipedia.org/wiki/Version\_control):
    
        git clone https://github.com/ildrm/servex-php.git
    
2.  Navigate to the project directory, aligning with [directory structure](https://en.wikipedia.org/wiki/Directory\_structure) best practices:
    
        cd servex
    
3.  Install dependencies via Composer, ensuring [dependency management](https://en.wikipedia.org/wiki/Dependency\_management) rigor:
    
        composer install
    
4.  Ensure Redis and MySQL are operational—locally or within Docker—per [infrastructure as code](https://en.wikipedia.org/wiki/Infrastructure\_as\_code) principles (see "Dependencies" below).
5.  Duplicate and refine the environment file, a pinnacle of [configuration management](https://en.wikipedia.org/wiki/Configuration\_management):
    
        cp .env.example .env
    
    Refine `.env` to orchestrate your database, Redis, and authentication credentials, adhering to [environment variable](https://en.wikipedia.org/wiki/Environment\_variable) security:
    
        DB_HOST=localhost
        DB_NAME=servex_db
        DB_USER=root
        DB_PASS=
        
        REDIS_HOST=localhost
        REDIS_PORT=6379
        REDIS_PASSWORD=
        JWT_SECRET=your-secret-key-please-change-it
        
        # Optional for message brokers
        RABBITMQ_HOST=localhost
        RABBITMQ_PORT=5672
        KAFKA_BOOTSTRAP_SERVERS=localhost:9092
    
6.  Establish the database and tables (see "Database Setup" below), a cornerstone of [data modeling](https://en.wikipedia.org/wiki/Data\_modeling).
7.  (Optional) Leverage Docker for a containerized utopia, aligning with [container orchestration](https://en.wikipedia.org/wiki/Container\_orchestration):
    
        docker-compose up -d
    

Usage
-----

To wield `ildrm/servex`—a masterpiece of microservice orchestration—initialize the framework via `Bootstrap` and engage services through `ServiceManager`. Behold this exemplar, weaving event-driven communication and message broker integration into a tapestry of [service-oriented architecture](https://en.wikipedia.org/wiki/Service-oriented\_architecture):

    load();
    
    $configPath = __DIR__ . '/config/config.php';
    $bootstrap = new Bootstrap($configPath);
    $bootstrap->init();
    
    $serviceManager = $bootstrap->getServiceManager();
    
Create a new user, a paragon of [business logic](https://en.wikipedia.org/wiki/Business_logic)

    $result = $serviceManager->call('user', 'createUser', ['Ali']);
    var_dump($result);
    
Retrieve a user, a symphony of [data access](https://en.wikipedia.org/wiki/Data_access)

    $user = $serviceManager->call('user', 'getUser', [1]);
    var_dump($user);
    
Emit an event with EventBus, a pinnacle of [event sourcing](https://en.wikipedia.org/wiki/Event_sourcing)

    $eventBus = $serviceManager->getEventBus();
    $eventBus->on('user.created', function (array $data) {
        echo "User created: " . $data['name'] . PHP_EOL;
        // Optionally publish to a message broker (e.g., RabbitMQ), adhering to [asynchronous programming](https://en.wikipedia.org/wiki/Asynchronous_programming)
        $this->publishToBroker('user.created', $data); // Custom method, a model of [abstraction](https://en.wikipedia.org/wiki/Abstraction_(computer_science))
    });
    $eventBus->emit('user.created', ['id' => $result['id'], 'name' => 'Ali']);
    
Example: Call a service via HTTP transport, a marvel of [RESTful architecture](https://en.wikipedia.org/wiki/Representational_state_transfer)

    $transport = new \Servex\Core\Transport\HttpTransport();
    $result = $transport->call('http://order-service/api', 'createOrder', ['userId' => $result['id']]);
    var_dump($result);

**Architectural Genius:** For microservice deployment, orchestrate each service in a discrete process or container, leveraging Docker or Kubernetes. Configure `EventBus` to integrate with message brokers for [scalability](https://en.wikipedia.org/wiki/Scalability) and reliability, a triumph of [distributed system design](https://en.wikipedia.org/wiki/Distributed\_systems).

Dependencies
------------

`ildrm/servex` depends on a constellation of technologies, each a pillar of [software engineering](https://en.wikipedia.org/wiki/Software\_engineering), ensuring robust performance and interoperability:

*   **PHP**: Version 8.1 or higher (tested with 8.3), a powerhouse of [object-oriented programming](https://en.wikipedia.org/wiki/Object-oriented\_programming) and [functional programming](https://en.wikipedia.org/wiki/Functional\_programming).
*   **Redis**: Essential for caching, deploy locally with:
    
        sudo apt-get install redis-server
        redis-server
    
    Or in Docker, a paradigm of [infrastructure as code](https://en.wikipedia.org/wiki/Infrastructure\_as\_code):
    
        docker run -d --name redis -p 6379:6379 redis
    
*   **MySQL**: Crucial for database operations, install and run locally with:
    
        sudo apt-get install mysql-server
        mysql -u root -p
    
    Or via Docker, embodying [containerization](https://en.wikipedia.org/wiki/OS-level\_virtualization):
    
        docker run -d --name mysql -e MYSQL_ROOT_PASSWORD=root -p 3306:3306 mysql
    
*   **Composer Packages**:
    *   `predis/predis`: For Redis caching (version ^2.2), a virtuoso of [in-memory data grid](https://en.wikipedia.org/wiki/In-memory\_data\_grid) operations.
    *   `doctrine/orm`: For database ORM (version ^2.14), a titan of [object-relational mapping](https://en.wikipedia.org/wiki/Object-relational\_mapping).
    *   `laminas/laminas-diactoros`: For PSR-7 HTTP messages (version ^2.11), a beacon of [PSR standards](https://en.wikipedia.org/wiki/PHP\_Standard\_Recommendation).
    *   `symfony/event-dispatcher`: For event handling (version ^6.0), a masterpiece of [event-driven programming](https://en.wikipedia.org/wiki/Event-driven\_programming).
    *   `firebase/php-jwt`: For JWT authentication (version ^6.11), a guardian of [token-based authentication](https://en.wikipedia.org/wiki/Token-based\_authentication).
    *   `cache/adapter-predis`: For PSR-16 Redis caching (version ^1.1, optional), enhancing [caching patterns](https://en.wikipedia.org/wiki/Caching\_patterns).
    *   Other PSR-compliant packages for containers, logging, and caching, ensuring [code quality](https://en.wikipedia.org/wiki/Code\_quality).
*   **Optional Message Broker Libraries**:
    *   `php-amqplib/php-amqplib`: For RabbitMQ integration, a titan of [message queue](https://en.wikipedia.org/wiki/Message\_queue) systems.
    *   `rdkafka/kafka-php`: For Apache Kafka integration, a maestro of [big data](https://en.wikipedia.org/wiki/Big\_data) messaging.
    *   `stomp-php/stomp-php`: For ActiveMQ integration, a sentinel of [JMS](https://en.wikipedia.org/wiki/Java\_Message\_Service).
    *   `natsserver/php-nats`: For NATS integration, a virtuoso of [real-time computing](https://en.wikipedia.org/wiki/Real-time\_computing).
    *   `php-mqtt/php-mqtt`: For HiveMQ (MQTT) integration, a pioneer of [Internet of Things](https://en.wikipedia.org/wiki/Internet\_of\_things) messaging.

Database Setup
--------------

To unleash `ildrm/servex` and its symphony of tests, orchestrate the database and tables with precision, a cornerstone of [data modeling](https://en.wikipedia.org/wiki/Data\_modeling) and [database design](https://en.wikipedia.org/wiki/Database\_design):

Create the database, a monument of [relational database](https://en.wikipedia.org/wiki/Relational_database)

    CREATE DATABASE servex_db;
    
Use the database, aligning with [schema management](https://en.wikipedia.org/wiki/Database_schema)

    USE servex_db;
    
Create the users table, a paragon of [normalization](https://en.wikipedia.org/wiki/Database_normalization)

    CREATE TABLE users (
        id INT PRIMARY KEY,
        name VARCHAR(255) NOT NULL
    );
    
Insert a sample record for testing, embodying [data seeding](https://en.wikipedia.org/wiki/Data_seeding)

    INSERT INTO users (id, name) VALUES (1, 'Test User');

Ensure your `.env` or `config/config.php` resonates with your database settings, leveraging [environment variables](https://en.wikipedia.org/wiki/Environment\_variable) for security. For testing, deploy a test database to preserve production integrity, adhering to [data isolation](https://en.wikipedia.org/wiki/Data\_isolation) principles.

Testing
-------

Conduct a virtuosic performance of unit tests with PHPUnit, ensuring `ildrm/servex`’s components operate with flawless precision, a testament to [test-driven development](https://en.wikipedia.org/wiki/Test-driven\_development) and [behavior-driven development](https://en.wikipedia.org/wiki/Behavior-driven\_development):

    vendor/bin/phpunit

Before this symphony commences, ensure:

*   All dependencies are installed via `composer install`, a pinnacle of [dependency management](https://en.wikipedia.org/wiki/Dependency\_management).
*   Redis and MySQL are operational and configured correctly, aligning with [infrastructure as code](https://en.wikipedia.org/wiki/Infrastructure\_as\_code).
*   The database and tables are established as described, embodying [database testing](https://en.wikipedia.org/wiki/Database\_testing).
*   Message brokers (if utilized) are accessible and configured in `.env`, ensuring [integration testing](https://en.wikipedia.org/wiki/Integration\_testing) rigor.

Tests envelop core components—`ServiceManager`, `EventBus`, `UserService`, `CacheManager`, and `DatabaseManager`. Employ the `--verbose` flag for a detailed overture:

    vendor/bin/phpunit --verbose

**Testing Oracle:** Should tests falter due to database, Redis, or message broker dissonance, scrutinize your `.env` configuration and verify service operability, adhering to [test automation](https://en.wikipedia.org/wiki/Test\_automation) best practices.

Contributing
------------

We extend an invitation to luminaries—developers, architects, engineers, and IT visionaries—to enrich `ildrm/servex`, a masterpiece of open-source collaboration. Embark on this odyssey with the following steps, a paragon of [collaborative development](https://en.wikipedia.org/wiki/Collaborative\_development):

1.  Fork the repository on GitHub, a bastion of [version control](https://en.wikipedia.org/wiki/Version\_control): [github.com/ildrm/servex](https://github.com/ildrm/servex-php).
2.  Clone your fork locally, aligning with [distributed version control](https://en.wikipedia.org/wiki/Distributed\_version\_control):
    
        git clone https://github.com/ildrm/servex-php.git
    
3.  Install dependencies with Composer, ensuring [dependency management](https://en.wikipedia.org/wiki/Dependency\_management) precision:
    
        composer install
    
4.  Forge a new branch for your feature or bug fix, a model of [branching strategy](https://en.wikipedia.org/wiki/Branching\_(version\_control)):
    
        git checkout -b feature/your-feature
    
5.  Craft your changes, execute tests, and elevate code quality with PHPStan, a titan of [static analysis](https://en.wikipedia.org/wiki/Static\_program\_analysis):
    
        vendor/bin/phpstan analyze
    
6.  Commit your opus and propel it to your fork, adhering to [commit messaging](https://en.wikipedia.org/wiki/Commit\_(version\_control)) standards:
    
        git commit -m "Add your commit message"
        git push origin feature/your-feature
    
7.  Submit a pull request to the main repository, articulating your changes, testing outcomes, and relevant issues, a pinnacle of [code review](https://en.wikipedia.org/wiki/Code\_review).

Adhere to the PSR-12 coding standards, a cornerstone of [code quality](https://en.wikipedia.org/wiki/Code\_quality), include tests for new features, and document your contributions in code and README, leveraging GitHub Actions for [continuous integration](https://en.wikipedia.org/wiki/Continuous\_integration) and [continuous deployment](https://en.wikipedia.org/wiki/Continuous\_deployment) to guarantee compatibility with PHP 8.1+.

License
-------

`ildrm/servex` is enshrined under the [MIT License](https://opensource.org/licenses/MIT), a beacon of open-source ethos. Consult the `LICENSE` file in the repository for the complete text, aligning with [open-source software](https://en.wikipedia.org/wiki/Open-source\_software) principles.

Support and Contact
-------------------

For queries, challenges, or enlightenment, engage with our community:

*   Illuminate an issue on GitHub, our citadel of collaboration: [github.com/ildrm/servex/issues](https://github.com/ildrm/servex-php/issues).
*   Commune with the maintainers via email at [ildrm@hotmail.com](mailto:ildrm@hotmail.com?subject=Issue%20Report%20for%20ildrm/servex%20Framework&body=Hello%2C%0D%0A%0D%0AI%20am%20reporting%20an%20issue%20with%20the%20ildrm/servex%20framework.%20Here%20are%20the%20details%3A%0D%0A%0D%0A-%20**Issue%20Description**%3A%20[Briefly%20describe%20the%20problem,%20e.g.,%20"CacheManager%20fails%20to%20connect%20to%20Redis."]%0D%0A-%20**Steps%20to%20Reproduce**%3A%20[List%20steps,%20e.g.,%20"1.%20Run%20CacheManagerTest.php,%202.%20Attempt%20to%20set%20a%20cache%20value,%203.%20Observe%20connection%20error."]%0D%0A-%20**Expected%20Behavior**%3A%20[What%20should%20happen,%20e.g.,%20"Cache%20should%20store%20and%20retrieve%20values%20successfully."]%0D%0A-%20**Actual%20Behavior**%3A%20[What%20actually%20happens,%20e.g.,%20"Connection%20refused%20error%20is%20thrown."]%0D%0A-%20**Environment**%3A%20[PHP%20version,%20OS,%20dependencies,%20e.g.,%20"PHP%208.3,%20Ubuntu%2022.04,%20Composer%202.5."]%0D%0A-%20**Additional%20Notes**%3A%20[Any%20other%20relevant%20information,%20e.g.,%20"This%20occurs%20consistently%20in%20Docker."]%0D%0A%0D%0APlease%20let%20me%20know%20if%20you%20need%20further%20information%20or%20logs.%0D%0A%0D%0A[Your%20Name/Email]).
*   Join our intellectual conclave on Discord (link forthcoming).

Your insights and contributions are the lifeblood of `ildrm/servex`’s ascent as a preeminent PHP microservice framework, a triumph for the global software engineering community!

Versioning
----------

We adhere to the gospel of [Semantic Versioning](https://semver.org/) (SemVer), a luminary of [versioning](https://en.wikipedia.org/wiki/Software\_versioning) rigor. Peruse `composer.json` and Git tags for version milestones (e.g., `1.0.0`), where major versions herald breaking changes, minor versions unveil features, and patch versions mend defects, ensuring [backward compatibility](https://en.wikipedia.org/wiki/Backward\_compatibility) and [forward compatibility](https://en.wikipedia.org/wiki/Forward\_compatibility).

Project Roadmap
---------------

The trajectory of `ildrm/servex` unfolds as a visionary roadmap, a beacon for future innovation, resonating with the aspirations of developers, architects, engineers, and IT luminaries:

*   Integrating native support for additional message brokers (e.g., Kafka, NATS) within `EventBus`, amplifying [asynchronous programming](https://en.wikipedia.org/wiki/Asynchronous\_programming) prowess.
*   Elevating `HttpTransport` with RESTful APIs, gRPC, and GraphQL as core features, a triumph of [API design](https://en.wikipedia.org/wiki/API).
*   Crafting a comprehensive API reference and tutorials for microservice development, including message broker integration and authentication setups, embodying [technical documentation](https://en.wikipedia.org/wiki/Technical\_documentation) excellence.
*   Introducing more service exemplars (e.g., `OrderService`, `PaymentService`) with exhaustive documentation, a paradigm of [domain-driven design](https://en.wikipedia.org/wiki/Domain-driven\_design).
*   Enhancing performance with Swoole or RoadRunner for real-time applications, a masterpiece of [performance optimization](https://en.wikipedia.org/wiki/Performance\_optimization).
*   Expanding security features with native Keycloak, Auth0, and OpenID Connect integrations, a fortress of [security by design](https://en.wikipedia.org/wiki/Security\_by\_design).
