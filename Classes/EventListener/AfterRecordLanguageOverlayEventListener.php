<?php

declare(strict_types=1);

namespace Lemming\RouteCache\EventListener;

use Lemming\RouteCache\Factory\CacheIdentifierFactory;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Domain\Event\AfterRecordLanguageOverlayEvent;
use TYPO3\CMS\Core\Http\ApplicationType;

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
        if ($this->isFrontendRequest() && $table !== 'tt_content') {
            $slugFieldName = $this->getSlugFieldName($table);
            if ($slugFieldName) {
                $languageAspect = $event->getLanguageAspect();
                $record = $event->overlayingWasAttempted() ? $event->getLocalizedRecord() : $event->getRecord();
                $cacheIdentifier = CacheIdentifierFactory::createCacheIdentifier($table, $record['uid'], $languageAspect);
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

    protected function isFrontendRequest(): bool
    {
        return !Environment::isCli()
            && ($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }
}
