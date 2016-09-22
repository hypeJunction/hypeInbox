<a name="6.0.1"></a>
## [6.0.1](https://github.com/hypeJunction/hypeInbox/compare/6.0.0...v6.0.1) (2016-09-22)




<a name="6.0.0"></a>
# [6.0.0](https://github.com/hypeJunction/hypeInbox/compare/5.1.7...v6.0.0) (2016-09-22)


### Bug Fixes

* **forms:** input name for recipients now matches core messages ([b6e3fff](https://github.com/hypeJunction/hypeInbox/commit/b6e3fff))
* **perms:** gatekeep resource pages ([aa57c6b](https://github.com/hypeJunction/hypeInbox/commit/aa57c6b))
* **ui:** display inbox control above the list, add missing threaded input ([b3fa686](https://github.com/hypeJunction/hypeInbox/commit/b3fa686))

### Features

* **attachments:** outsource attachments to hypeAttachments ([d2822fa](https://github.com/hypeJunction/hypeInbox/commit/d2822fa))
* **core:** update requirements ([3075568](https://github.com/hypeJunction/hypeInbox/commit/3075568))
* **deps:** access collection management is now handled by acl_builder_api ([84d82e5](https://github.com/hypeJunction/hypeInbox/commit/84d82e5))
* **deps:** no longer depends on hypeApps ([4f3eba5](https://github.com/hypeJunction/hypeInbox/commit/4f3eba5))
* **forms:** update send message form and action ([cd3b8f7](https://github.com/hypeJunction/hypeInbox/commit/cd3b8f7))
* **inbox:** improve inbox layout and queries ([5edee82](https://github.com/hypeJunction/hypeInbox/commit/5edee82))
* **inbox:** retire notifications as a message type ([038c85a](https://github.com/hypeJunction/hypeInbox/commit/038c85a))
* **listing:** simplify listings and menus ([ef1a52a](https://github.com/hypeJunction/hypeInbox/commit/ef1a52a))
* **releases:** now requires Elgg 2.2 ([5057cd7](https://github.com/hypeJunction/hypeInbox/commit/5057cd7))
* **search:** adds a inbox search functionality ([9d75d75](https://github.com/hypeJunction/hypeInbox/commit/9d75d75))
* **threads:** improve threading UX ([f93bef1](https://github.com/hypeJunction/hypeInbox/commit/f93bef1))


### BREAKING CHANGES

* listing: Removes most of the custom styling
Simplifies message menu
* releases: Now requires Elgg 2.2
* forms: Original message guid input name has been changed from guid to original_guid
to match core messages plugin.
Enables HTML support in messages
* forms: If you were listening to recipients_guids input in your events,
update the input name to recipients
* attachments: Attachments are now handled by hypeAttachments
* deps: The plugin no longer depends on hypeApps, subsequently any APIs
or instance checks relating to hypeApps will fail.
Action handling is now done in action files - there are no
longer hypeApps dependant action classes.
hypeInbox() was made private and will be phased out.
* core: Now requires Elgg 2.1



<a name="5.1.7"></a>
## [5.1.7](https://github.com/hypeJunction/hypeInbox/compare/5.1.6...v5.1.7) (2016-07-08)


### Bug Fixes

* **autocomplete:** rely on core search where possible ([8525bdf](https://github.com/hypeJunction/hypeInbox/commit/8525bdf))



<a name="5.1.6"></a>
## [5.1.6](https://github.com/hypeJunction/hypeInbox/compare/5.1.5...v5.1.6) (2016-02-23)


### Bug Fixes

* **deps:** update hypeLists version ([4cf202b](https://github.com/hypeJunction/hypeInbox/commit/4cf202b))
* **logs:** fix warnings in logs ([0928610](https://github.com/hypeJunction/hypeInbox/commit/0928610))



<a name="5.1.5"></a>
## [5.1.5](https://github.com/hypeJunction/hypeInbox/compare/5.1.4...v5.1.5) (2016-02-09)


### Bug Fixes

* **notifications:** make sure notifier marks message as read ([59b771a](https://github.com/hypeJunction/hypeInbox/commit/59b771a))



<a name="5.1.4"></a>
## [5.1.4](https://github.com/hypeJunction/hypeInbox/compare/5.1.3...v5.1.4) (2016-02-07)




<a name="5.1.3"></a>
## [5.1.3](https://github.com/hypeJunction/hypeInbox/compare/5.1.2...v5.1.3) (2016-02-07)




<a name="5.1.2"></a>
## [5.1.2](https://github.com/hypeJunction/hypeInbox/compare/5.1.1...v5.1.2) (2016-02-07)




<a name="5.1.1"></a>
## [5.1.1](https://github.com/hypeJunction/hypeInbox/compare/5.1.0...v5.1.1) (2016-02-07)


### Bug Fixes

* **deps:** fix required version of tokeninput ([df4595a](https://github.com/hypeJunction/hypeInbox/commit/df4595a))



<a name="5.1.0"></a>
# [5.1.0](https://github.com/hypeJunction/hypeInbox/compare/5.0.2...v5.1.0) (2016-02-05)


### Bug Fixes

* **forms:** wrap reply form in info module instead of aside ([fc19f7e](https://github.com/hypeJunction/hypeInbox/commit/fc19f7e))
* **inbox:** weirdness in class name resolution ([f7c65bc](https://github.com/hypeJunction/hypeInbox/commit/f7c65bc)), closes [#20](https://github.com/hypeJunction/hypeInbox/issues/20)
* **js:** switch to elgg spinner and require jquery form ([077b017](https://github.com/hypeJunction/hypeInbox/commit/077b017))
* **notifications:** make sure the sender is not notified by email ([b8c1a84](https://github.com/hypeJunction/hypeInbox/commit/b8c1a84))
* **views:** fix typo in function name ([76b9cbf](https://github.com/hypeJunction/hypeInbox/commit/76b9cbf))

### Features

* **notifications:** integration with notifications templates ([80de728](https://github.com/hypeJunction/hypeInbox/commit/80de728))
* **thread:** increate messages shown per thread to 100 ([a8b3057](https://github.com/hypeJunction/hypeInbox/commit/a8b3057))
* **ux:** focus reply form when reply button is clicked ([5bfdd18](https://github.com/hypeJunction/hypeInbox/commit/5bfdd18))



<a name="5.0.2"></a>
## [5.0.2](https://github.com/hypeJunction/hypeInbox/compare/5.0.1...v5.0.2) (2016-01-26)


### Bug Fixes

* **composer:** update composer ([5118a0f](https://github.com/hypeJunction/hypeInbox/commit/5118a0f))
* **messages:** mark senders copy as read ([25c7639](https://github.com/hypeJunction/hypeInbox/commit/25c7639))



<a name="5.0.1"></a>
## [5.0.1](https://github.com/hypeJunction/hypeInbox/compare/5.0.0...v5.0.1) (2016-01-26)


### Bug Fixes

* **assets:** css and js fixes ([c84c5e6](https://github.com/hypeJunction/hypeInbox/commit/c84c5e6))
* **css:** combine all css ([0c19eaf](https://github.com/hypeJunction/hypeInbox/commit/0c19eaf))
* **manifest:** fix versions ([697cea9](https://github.com/hypeJunction/hypeInbox/commit/697cea9))
* **menus:** fix thread menu ([d900190](https://github.com/hypeJunction/hypeInbox/commit/d900190))
* **menus:** fix typo in menu registration ([916cc4b](https://github.com/hypeJunction/hypeInbox/commit/916cc4b))



<a name="5.0.0"></a>
# [5.0.0](https://github.com/hypeJunction/hypeInbox/compare/4.2.0...v5.0.0) (2016-01-26)


### Bug Fixes

* **getter:** use string values of readYet ([35d94e5](https://github.com/hypeJunction/hypeInbox/commit/35d94e5))

### Features

* **popup:** adds a popup to topbar ([1155818](https://github.com/hypeJunction/hypeInbox/commit/1155818))
* **releases:** update supported Elgg versions ([f5c3abd](https://github.com/hypeJunction/hypeInbox/commit/f5c3abd))
* **style:** rebuild menus and views for better Ui and Ux ([f71d9bc](https://github.com/hypeJunction/hypeInbox/commit/f71d9bc))
* **views:** improvements to views ([d2217e2](https://github.com/hypeJunction/hypeInbox/commit/d2217e2))
* **views:** make icon size configurable ([19d2c03](https://github.com/hypeJunction/hypeInbox/commit/19d2c03))



