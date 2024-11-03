# TYPO3 Route cache

Implements a cache of slug field values for re-use in PersistedAliasMapper to have less database lookups.

## Background

LinkViewHelpers turn Extbase entities into uids which are then fetched and overlayed again in the PersistedAliasMapper. 