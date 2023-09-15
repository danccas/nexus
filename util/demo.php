
<?php
require_once __DIR__ . '/framework.php';



dd(db()->insert('public.usuario', [
    'usuario' => 'demo123123123123',
    'clave'   => 'demo123123123123',
]));