<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard
Breadcrumbs::for('admin.dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});

// Menu
Breadcrumbs::for('admin.master_data.menu.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Menu', route('admin.master_data.menu.index'));
});

// Create Menu
Breadcrumbs::for('admin.master_data.menu.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.master_data.menu.index');
    $trail->push('Create Menu', route('admin.master_data.menu.create'));
});

// Edit Menu
Breadcrumbs::for('admin.master_data.menu.edit', function (BreadcrumbTrail $trail, $menu) {
    $trail->parent('admin.master_data.menu.index');
    $trail->push('Edit Menu', route('admin.master_data.menu.edit', $menu));
});

// Role
Breadcrumbs::for('admin.master_data.role.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Role & Permission', route('admin.master_data.role.index'));
});

// Create Role
Breadcrumbs::for('admin.master_data.role.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.master_data.role.index');
    $trail->push('Create Role', route('admin.master_data.role.create'));
});

// Edit Role
Breadcrumbs::for('admin.master_data.role.edit', function (BreadcrumbTrail $trail, $role) {
    $trail->parent('admin.master_data.role.index');
    $trail->push('Edit Role', route('admin.master_data.role.edit', $role));
});

// Create Permission
Breadcrumbs::for('admin.master_data.permission.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.master_data.role.index');
    $trail->push('Create Permission', route('admin.master_data.permission.create'));
});

// Edit Permission
Breadcrumbs::for('admin.master_data.permission.edit', function (BreadcrumbTrail $trail, $permission) {
    $trail->parent('admin.master_data.permission.index');
    $trail->push('Edit Permission', route('admin.master_data.permission.edit', $permission));
});

// User
Breadcrumbs::for('admin.master_data.user.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('User', route('admin.master_data.user.index'));
});

// Create User
Breadcrumbs::for('admin.master_data.user.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.master_data.user.index');
    $trail->push('Create User', route('admin.master_data.user.create'));
});

// Edit User
Breadcrumbs::for('admin.master_data.user.edit', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('admin.master_data.user.index');
    $trail->push('Edit User', route('admin.master_data.user.edit', $user));
});

Breadcrumbs::for('order-list', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard'); // Or any parent breadcrumb you have, like admin.dashboard
    $trail->push('Order List', route('order-list'));
});

// History Order List (Admin/Kasir)
Breadcrumbs::for('history-order-list', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard'); // Again, use a relevant parent breadcrumb
    $trail->push('History Order List', route('history-order-list'));
});

Breadcrumbs::for('detail-order', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.dashboard'); // Again, use a relevant parent breadcrumb
    $trail->push('Order Detail ' . $id, route('detail-order', $id));
});
