<?php namespace Akens\LaravelHelpers\Asset\Filter;

use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetInterface;

class I18NextFilter  implements FilterInterface {
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent($this->evalContent($asset->getContent()));
    }

    protected function evalContent($content)
    {
        // Eliminate the opening <?php tag.
        $content = preg_replace('|(?mi-Us)<\\?php|', '', $content);

        // Replace the laravel-style variables with i18next-style.
        $content = preg_replace('|(?mi-Us):([^\\s,\\.!?"]+)|', '__$1__', $content);

        return eval($content);
    }
}