<?php

namespace App\Adapter\Storage\Repository;

use App\Core\Filesystem\Filesystem;
use App\Domain\Model\Vault;
use App\Domain\Port\Storage\VaultRepositoryInterface;
use App\InputOutput;
use Illuminate\Contracts\Config\Repository;

class VaultRepository implements VaultRepositoryInterface
{
    protected mixed $vaultsStoragePath;
    protected mixed $vaultLogsPath;

    public function __construct(
        protected Filesystem $filesystem,
        private Repository   $config,
        private InputOutput  $inputOutput
    ) {
        $this->vaultsStoragePath = $this->config->get('vaultsStoragePath');
        $this->vaultLogsPath = $this->config->get('vaultsLogs');

    }


    public function create(array $attributes): object
    {
        $vaultName = $attributes['name'];
        $vaultPath = $this->vaultsStoragePath . $vaultName . '.json';

        if ($this->isVaultExist($vaultPath)) {
            $this->inputOutput->writeln("Vault {$vaultName} already exists");
        }

        $attributes['path'] = $vaultPath;
        $vault = new Vault($attributes);

        $this->filesystem->put($vaultPath, '');
        $this->updateVaultLogs($attributes);

        $this->inputOutput->writeln("Vault {$vaultName} created successfully");

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
        if (!$this->isVaultExist($attributes['name'].'.json') && !$this->isVaultExist($attributes['name'])) {
            throw new \Exception('Vault does not exist');
        }

        $vaultsData = json_decode($this->filesystem->get($this->vaultLogsPath), true);

        foreach ($vaultsData as &$vault) {
            if ($vault['name'].'.json' === $attributes['name']) {
                $vault['updated_at'] = $attributes['updated_at'];
            }
        }

        $this->filesystem->put($this->vaultLogsPath, json_encode($vaultsData, JSON_PRETTY_PRINT));
        $this->inputOutput->writeln('Vault ' . $attributes['name'] . ' updated successfully');

        return true;
    }

    /**
     * @throws \Exception
     */
    public function delete(int|string $id): bool
    {
        if (!$this->isVaultExist($id.'.json')) {
            $this->inputOutput->writeln('Vault ' . $id . ' does not exist');
            return false;
        }

        $this->filesystem->delete($this->vaultsStoragePath . $id . '.json');
        $vaultLogsContent = $this->filesystem->get($this->vaultLogsPath);
        $vaultsData = json_decode($vaultLogsContent, true);

        $vaultsData = array_filter($vaultsData, function ($vault) use ($id) {
            return $vault['name'] !== $id;
        });

        $this->filesystem->put($this->vaultLogsPath, json_encode($vaultsData, JSON_PRETTY_PRINT));

        $this->inputOutput->writeln('Vault' . $id . ' deleted successfully');

        return true;
    }

    /**
     * @throws \Exception
     */
    public function find(int|string $id): ?object
    {
        if (!$this->isVaultExist($id.'.json')) {
            throw new \Exception('Vault does not exist');
        }

        $this->filesystem->get($this->vaultsStoragePath . $id);

        return new \stdClass();
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
