<?php

declare(strict_types=1);

namespace Lemming\RouteCache\Routing;

use Lemming\RouteCache\Factory\CacheIdentifierFactory;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Routing\Aspect\PersistedAliasMapper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CachingPersistedAliasMapper extends PersistedAliasMapper
{
    public function generate(string $value): ?string
    {
        $cache = $this->getRuntimeCache();
        $languageAspect = $this->getLanguageAspect();
        $cacheIdentifier = CacheIdentifierFactory::createCacheIdentifier($this->tableName, $value, $languageAspect);

        if ($cacheItem = $cache->get($cacheIdentifier)) {
            // Cache hit
            $result = $cacheItem['result'];
            return $this->purgeRouteValuePrefix($result);
        }

        // Cache miss
        $result = parent::generate($value);
        $cache->set($cacheIdentifier, ['result' => $result]);
        return $result;
    }

    public function getRuntimeCache(): FrontendInterface
    {
        return GeneralUtility::makeInstance(CacheManager::class)->getCache('runtime');
    }
}