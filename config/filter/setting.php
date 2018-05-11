<?php
return [
    'login'   => [
        'showSelf'
    ],
    'include' => [
        'show' => [
            'route'  => 'account/layer/getList',
            'name'   => [],
            'crud'   => 'READ',
            'unlink' => true
        ],
        'edit' => [
            'route'  => 'account/layer/getList',
            'name'   => [],
            'crud'   => 'UPDATE',
            'unlink' => true
        ]
    ]
];
