<?php 

namespace Yaro\ApiDocs;

use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Contracts\Config\Repository as Config;
use Yaro\ApiDocs\Exceptions\InvalidFilesystemDisc;

class Blueprint
{
    private $filesystem;
    private $config;
    private $endpoints = [];
    private $routePrefix;

    public function __construct(Factory $filesystem, Config $config)
    {
        $this->filesystem = $filesystem;
        $this->config = $config;
    }
    
    public function setRoutePrefix($routePrefix)
    {
        $this->routePrefix = $routePrefix;
    }
    
    public function setEndpoints(array $rawEndpoints)
    {
        $endpoints = [];
        $delimiter = $this->config->get('yaro.apidocs.blueprint.reference_delimiter');
        
        foreach ($rawEndpoints as $group => $groupEndpoints) {
            $group = preg_replace('~\.~', $delimiter, $group);
            $endpoints[$group] = $groupEndpoints;
        }
        
        $this->endpoints = $endpoints;
    }
    
    public function render()
    {
        $host = $this->config->get('yaro.apidocs.blueprint.host');
        if (!$host) {
            $host = url($this->routePrefix);
        }
        
        return view('apidocs::blueprint', [
            'endpoints'    => $this->endpoints, 
            'host'         => $host, 
            'title'        => $this->config->get('yaro.apidocs.blueprint.title'), 
            'introduction' => $this->config->get('yaro.apidocs.blueprint.introduction')
        ])->render();
    }

    public function create(string $snapshotName = '', string $diskName = '')
    {
        $snapshotName = $snapshotName ?: 'blueprint_'. date('Y-m-d_H-i-s');
        $diskName = $diskName ?: $this->config->get('yaro.apidocs.blueprint.disc');
        
        $disk = $this->getDisk($diskName);

        $fileName = $snapshotName.'.apib';
        $fileName = pathinfo($fileName, PATHINFO_BASENAME);

        $this->createFile($fileName, $disk);
        
        return $snapshotName;
    }

    private function getDisk(string $diskName)
    {
        $path = sprintf('filesystems.disks.%s', $diskName);
        if (is_null($this->config->get($path))) {
            throw new InvalidFilesystemDisc($diskName);
        }

        return $this->filesystem->disk($diskName);
    }

    private function createFile(string $fileName, FilesystemAdapter $disk)
    {
        $tempFileHandle = tmpfile();
        fwrite($tempFileHandle, $this->render());

        $disk->put($fileName, $tempFileHandle);

        fclose($tempFileHandle);
    }
    
}
