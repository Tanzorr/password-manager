<?php

namespace App\Adapter\Storage;


use App\Core\Filesystem\Filesystem;
use App\Core\Storage\Repository\RepositoryInterface;
use App\Domain\Model\Vault;
use Illuminate\Contracts\Config\Repository;

class VaultRepository implements RepositoryInterface
{
    protected mixed $vaultsStoragePath;
    protected mixed $vaultLogsPath;

    public function __construct(
        protected Filesystem $filesystem,
        private Repository   $config,
    )
    {
        $this->vaultsStoragePath = $this->config->get('vaultsStoragePath');
        $this->vaultLogsPath = $this->config->get('vaultsLogs');
    }


    public function create(array $attributes): object
    {
        $vaultName = $attributes['name'];
        $vaultPath = $this->vaultsStoragePath . $vaultName . '.json';

        if ($this->isVaultExist($vaultPath)) {
            throw new \Exception('Vault already exists');
        }

        $attributes['path'] = $vaultPath;
        $vault = new Vault($attributes);

        $this->filesystem->put($vaultPath, '');
        $this->updateVaultLogs($attributes);

        return $vault;
    }

    private function updateVaultLogs(array $attributes): void
    {
        $vaultLogsContent = $this->filesystem->get($this->vaultLogsPath);
        $vaultsData = json_decode($vaultLogsContent, true) ?? [];
        $vaultsData[] = $attributes;

        $this->filesystem->put($this->vaultLogsPath, json_encode($vaultsData, JSON_PRETTY_PRINT));
    }

    /**
     * @throws \Exception
     */
    public function update(array $attributes): bool
    {
        if (!$this->isVaultExist($attributes['name'] . '.json') && !$this->isVaultExist($attributes['name'])) {
            throw new \Exception('Vault does not exist');
        }

        $vaultsData = json_decode($this->filesystem->get($this->vaultLogsPath), true);

        foreach ($vaultsData as &$vault) {
            if ($vault['name'] . '.json' === $attributes['name']) {
                $vault['updated_at'] = $attributes['updated_at'];
            }
        }

        $this->filesystem->put($this->vaultLogsPath, json_encode($vaultsData, JSON_PRETTY_PRINT));

        return true;
    }


    public function updateVaultName(string $vaultName, string $newName): bool
    {
        $oldName = $this->vaultsStoragePath . $vaultName;
        $newName = $this->vaultsStoragePath . $newName . '.json';

        if (rename($oldName, $newName)) {
            return true;
        }
        return false;
    }

    /**
     * @throws \Exception
     */
    public function delete(int|string $id): bool
    {
        if (!$this->isVaultExist($id)) {
            throw new \Exception('Vault does not exist');
        }

        $this->filesystem->delete($this->vaultsStoragePath . $id);
        $vaultLogsContent = $this->filesystem->get($this->vaultLogsPath);
        $vaultsData = json_decode($vaultLogsContent, true);

        $vaultsData = array_filter($vaultsData, function ($vault) use ($id) {
            return $vault['name'] !== $id;
        });

        $this->filesystem->put($this->vaultLogsPath, json_encode($vaultsData, JSON_PRETTY_PRINT));

        return true;
    }

    /**
     * @throws \Exception
     */
    public function find(int|string $id): ?object
    {
        if (!$this->isVaultExist($id)) {
            throw new \Exception("Vault $id does not exist");
        }

        return $this->filesystem->get($this->vaultsStoragePath . $id);
    }

    public function findAll(): array
    {
        return $this->filesystem->getAllFiles($this->vaultsStoragePath);
    }

    private function isVaultExist(string $name): bool
    {
        return file_exists($this->vaultsStoragePath . $name);
    }
}