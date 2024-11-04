# TYPO3 Route cache

This is a cache implementation for record slugs.

Cache entries are created after language overlaying, basically what's done with every record when it's fetched from
database. Cache lookups are done in the PersistedAliasMapper during link generation. Successful cache lookups (hits)
reduce the amount of database queries - record are not fetched and overlayed again in the PersistedAliasMapper.

## ToDos

* [ ] Slug updates in FE require a cache invalidation 