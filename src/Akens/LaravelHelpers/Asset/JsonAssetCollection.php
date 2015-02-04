<?php namespace Akens\LaravelHelpers\Asset;

use Assetic\Asset\AssetCollection;
use Assetic\Filter\FilterInterface;

class JsonAssetCollection extends AssetCollection {
    public function dump(FilterInterface $additionalFilter = null) {
        // loop through leaves and dump each asset
        $parts = array();
        foreach ($this as $asset) {
            $name = preg_replace('|(?mi-Us)\\.php|', '', $asset->getSourcePath());
            $parts[$name] = $asset->dump($additionalFilter);
        }

        return json_encode($parts);
    }
}