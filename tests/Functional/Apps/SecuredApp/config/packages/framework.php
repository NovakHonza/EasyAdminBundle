<?php

use EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\Apps\SecuredApp\Kernel;

$configuration = [
    'secret' => 'test_secret',
    'test' => true,
    'session' => [
        'storage_factory_id' => 'session.storage.factory.mock_file',
    ],
    'router' => [
        'utf8' => true,
    ],
    'http_method_override' => false,
    'php_errors' => [
        'log' => true,
    ],
];

if (Kernel::MAJOR_VERSION >= 6) {
    $configuration['handle_all_throwables'] = true;
}

$container->loadFromExtension('framework', $configuration);
