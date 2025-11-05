<?php

$configuration = [
    'secret' => 'F00',
    'csrf_protection' => true,
    'http_method_override' => true,
    'session' => [
        'handler_id' => null,
        'storage_factory_id' => 'session.storage.factory.mock_file',
        'cookie_secure' => 'auto',
        'cookie_samesite' => 'lax',
    ],
    'php_errors' => [
        'log' => true,
    ],
    'test' => true,
    'profiler' => ['enabled' => true, 'collect' => false],
    'handle_all_throwables' => true,
    'validation' => [
        'email_validation_mode' => 'html5',
    ],
    'uid' => [
        'default_uuid_version' => 7,
        'time_based_uuid_version' => 7,
    ],
];

$container->loadFromExtension('framework', $configuration);
