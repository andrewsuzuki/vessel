<?php namespace Hokeo\Vessel;

User::observe(new Observer\UserObserver);
Page::observe(new Observer\PageObserver);
Pagehistory::observe(new Observer\PagehistoryObserver);
Block::observe(new Observer\BlockObserver);
Role::observe(new Observer\RoleObserver);
Permission::observe(new Observer\PermissionObserver);