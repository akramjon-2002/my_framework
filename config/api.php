<?php

use App\Controllers\PostController;

return [
    ['method' => 'GET', 'path' => '/posts', 'handler' => [PostController::class, 'index']],
    ['method' => 'POST', 'path' => '/posts', 'handler' => [PostController::class, 'store']],
];
