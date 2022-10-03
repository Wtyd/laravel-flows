<?php

return [
    'environments' => [
        'local' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => [env('QUEUE_NAME', 'default')],
                'balance' => 'simple',
                'minProcesses' => env('QUEUE_MIN_PROCESSES', 1),
                'maxProcesses' => env('QUEUE_MAX_PROCESSES', 10),
                'tries' => env('QUEUE_TRIES', 3),
            ],
        ],
    ]
];
