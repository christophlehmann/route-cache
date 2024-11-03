<?php

declare(strict_types=1);

namespace Lemming\RouteCache\Routing;

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
        $cacheIdentifier = implode('__', [
            'routeCache',
            $this->tableName,
            $value,
            $languageAspect->getId(),
            $languageAspect->getContentId(),
            $languageAspect->getOverlayType(),
            implode('_', $languageAspect->getFallbackChain())
        ]);

        if ($cacheItem = $cache->get($cacheIdentifier)) {
            $result = $cacheItem['result'];
            return $this->purgeRouteValuePrefix($result);
        }

        $result = parent::generate($value);
        $cache->set($cacheIdentifier, ['result' => $result]);
        return $result;
    }

    public function getRuntimeCache(): FrontendInterface
    {
        return GeneralUtility::makeInstance(CacheManager::class)->getCache('runtime');
    }
}