<?php

declare(strict_types=1);

namespace Lemming\RouteCache\EventListener;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Domain\Event\AfterRecordLanguageOverlayEvent;

final class AfterRecordLanguageOverlayEventListener
{
    protected FrontendInterface $runtimeCache;

    public function __construct(CacheManager $cacheManager)
    {
        $this->runtimeCache = $cacheManager->getCache('runtime');
    }

    public function populateSlugCacheEntry(AfterRecordLanguageOverlayEvent $event): void
    {
        $table = $event->getTable();
        if ($table !== 'tt_content') {
            $slugFieldName = $this->getSlugFieldName($table);
            if ($slugFieldName) {
                $languageAspect = $event->getLanguageAspect();
                $record = $event->overlayingWasAttempted() ? $event->getLocalizedRecord() : $event->getRecord();
                $cacheIdentifier = implode(
                    '__', [
                    'routeCache',
                    $table,
                    $record['uid'],
                    $languageAspect->getId(),
                    $languageAspect->getContentId(),
                    $languageAspect->getOverlayType(),
                    implode('_', $languageAspect->getFallbackChain())
                ]);
                $this->runtimeCache->set($cacheIdentifier, ['result' => $record[$slugFieldName]]);
            }
        }
    }

    protected function getSlugFieldName(string $table): ?string
    {
        foreach ($GLOBALS['TCA'][$table]['columns'] as $columnName => $columnConfiguration) {
            if ($columnConfiguration['config']['type'] === 'slug') {
                return $columnName;
            }
        }
        return null;
    }
}
