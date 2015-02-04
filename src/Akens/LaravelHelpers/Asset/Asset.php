<?php namespace Akens\LaravelHelpers\Asset;

use Illuminate\Support\Facades\Config;
use Assetic\Asset\AssetCollection;
use Assetic\AssetWriter;
use Assetic\AssetManager;
use Assetic\FilterManager;

class Asset extends \Slushie\LaravelAssetic\Asset {

    /**
     * Override the namespace used to read the config.
     *
     * @var string
     */
    protected $namespace = 'laravel-helpers';

    /**
     * Create a new AssetCollection instance for the given group.
     *
     * @param string $name
     * @param bool $overwrite force writing
     * @return \Assetic\Asset\AssetCollection
     */
    public function createGroup($name, $overwrite = false) {
        if (isset($this->groups[$name])) {
            return $this->groups[$name];
        }

        $assets = $this->createAssetArray($name);
        $filters = $this->createFilterArray($name);
        $collectionType = $this->getConfig($name, 'collection', 'Assetic\Asset\AssetCollection');
        $coll = new $collectionType($assets, $filters);

        if ($output = $this->getConfig($name, 'output')) {
            $coll->setTargetPath($output);
        }

        // check output cache
        $writeOutput = true;
        if (!$overwrite && file_exists($output = public_path($coll->getTargetPath()))) {
            $outputModTime = filemtime($output);
            $assetModTime = $coll->getLastModified();

            if ($assetModTime && $outputModTime >= $assetModTime) {
                $writeOutput = false;
            }
        }

        // store assets
        if ($overwrite || $writeOutput) {
            $writer = new AssetWriter(public_path());
            $writer->writeAsset($coll);
        }

        return $this->groups[$name] = $coll;
    }

    protected function getConfig($group, $key, $default = null) {
        return Config::get($this->namespace . "::asset.groups.$group.$key", $default);
    }

    /**
     * Returns an array of group names.
     *
     * @return array
     */
    public function listGroups() {
        $groups = Config::get($this->namespace . '::asset.groups', array());
        return array_keys($groups);
    }

    /**
     * Creates the filter manager from the config file's filter array.
     *
     * @return FilterManager
     */
    protected function createFilterManager() {
        $manager = new FilterManager();
        $filters = Config::get($this->namespace . '::asset.filters', array());
        foreach ($filters as $name => $filter) {
            $manager->set($name, $this->createFilter($filter));
        }

        return $this->filters = $manager;
    }

    protected function createAssetManager() {
        $manager = new AssetManager;
        $config = Config::get($this->namespace . '::asset.assets', array());

        foreach ($config as $key => $refs) {
            if (!is_array($refs)) {
                $refs = array($refs);
            }

            $asset = array();
            foreach ($refs as $ref) {
                $asset[] = $this->parseAssetDefinition($ref);
            }

            if (count($asset) > 0) {
                $manager->set($key,
                    count($asset) > 1
                        ? new AssetCollection($asset)
                        : $asset[0]
                );
            }
        }

        return $this->assets = $manager;
    }
}