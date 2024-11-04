<?php

declare(strict_types=1);

namespace Lemming\RouteCache\Factory;

use TYPO3\CMS\Core\Context\LanguageAspect;

class CacheIdentifierFactory
{
    public static function createCacheIdentifier(string $tableName, string|int $recordUid, LanguageAspect $languageAspect): string
    {
        $cacheIdentifier = implode(
            '__', [
            'routeCache',
            $tableName,
            $recordUid,
            $languageAspect->getId(),
            $languageAspect->getContentId(),
            $languageAspect->getOverlayType(),
            implode('_', $languageAspect->getFallbackChain())
        ]);
        return $cacheIdentifier;
    }
}