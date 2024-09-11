# silverstripe-varchar-polyfill

This module provides a polyfill for `DBClassNameVarchar` which is not available before `silverstripe/framework` 5.4.0 and was added in this [pull-request](https://github.com/silverstripe/silverstripe-framework/pull/11359).

This functionality this provides is described [here](https://github.com/silverstripe/developer-docs/blob/5/en/02_Developer_Guides/08_Performance/06_ORM.md#changing-classname-column-from-enum-to-varchar-classname-varchar).

This module works on Silverstripe 4 and 5.

When upgrading to CMS 6, and also optionally 5.4, you should uninstall this module and instead use the `DBClassNameVarchar` class provided in `silverstripe/framework`.

Before installing this module, it it's recommended that you first validate that `ALTER TABLE` queries are a significant bottleneck when running `dev/build`. You can use [emteknetnz/silverstripe-dev-build-benchark](https://github.com/emteknetnz/silverstripe-dev-build-benchmark) to help with this.

> [!WARNING]
> After installing this module ALL necessary `ALTER TABLE` queries to change `ClassName` columns from `enum` to `varchar` will be run on the next `dev/build`, which can take a long time on large databases. Keep this in mind in particular when deploying to production.
