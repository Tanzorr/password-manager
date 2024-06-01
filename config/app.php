<?php
$root_path = $_SERVER['DOCUMENT_ROOT'];

return [
    'parameters'=> './service.yaml',
    'vaultsStoragePath' =>'./vaults/',
    'vaultsLogs' => './logs/vault-log.json',
    'activeVault' => '',
];