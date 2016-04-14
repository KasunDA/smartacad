INSERT INTO `menus` (`menu_id`, `menu`, `menu_url`, `active`, `sequence`, `icon`, `menu_header_id`, `created_at`, `updated_at`) VALUES
(1, 'SYSTEM', '#', 1, 1, 'fa fa-television', 1, '2016-03-30 00:33:49', '2016-03-30 00:33:49'),
(2, 'PROFILE', '#', 1, 1, 'fa fa-book', 2, '2016-03-30 21:33:36', '2016-03-30 21:33:36'),
(3, 'SACRAMENTS', '#', 1, 1, 'fa fa-book', 3, '2016-03-31 08:48:09', '2016-03-31 08:48:09');

INSERT INTO `menu_headers` (`menu_header_id`, `menu_header`, `active`, `sequence`, `created_at`, `updated_at`) VALUES
(1, 'SETUPS', 1, 10, '2016-03-30 00:30:39', '2016-03-30 21:33:06'),
(2, 'ACCOUNT', 1, 9, '2016-03-30 21:33:06', '2016-03-30 21:33:06'),
(3, 'RECORDS', 1, 8, '2016-03-31 08:45:49', '2016-03-31 08:45:49');

INSERT INTO `menu_items` (`menu_item_id`, `menu_item`, `menu_item_url`, `menu_item_icon`, `active`, `sequence`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 'SETTINGS', '#', 'fa fa-cogs', 1, '1', 1, '2016-03-30 11:04:10', '2016-03-30 11:04:10'),
(2, 'USERS', '#', 'fa fa-users', 1, '2', 1, '2016-03-30 11:47:28', '2016-03-30 11:47:28'),
(3, 'VIEW ', '/profiles', 'fa fa-user', 1, '1', 2, '2016-03-30 21:35:07', '2016-03-31 13:24:54'),
(4, 'EDIT', '/profiles/edit', 'fa fa-edit', 1, '2', 2, '2016-03-30 21:35:07', '2016-03-31 08:40:40'),
(5, 'BAPTISMS', '#', 'fa fa-table', 1, '1', 3, '2016-03-31 08:48:52', '2016-03-31 08:48:52'),
(6, 'CONFIRMATIONS', '#', 'fa fa-table', 1, '2', 3, '2016-03-31 08:49:27', '2016-03-31 08:49:27'),
(7, 'PARISH', '#', 'fa fa-home', 1, '3', 1, '2016-03-31 08:53:33', '2016-03-31 09:21:47');

INSERT INTO `sub_menu_items` (`sub_menu_item_id`, `sub_menu_item`, `sub_menu_item_url`, `sub_menu_item_icon`, `active`, `sequence`, `menu_item_id`, `created_at`, `updated_at`) VALUES
(1, 'MANAGE MENUS', '#', 'fa fa-list', 1, '1', 1, '2016-03-30 11:05:26', '2016-03-30 11:05:26'),
(2, 'PERMISSIONS', '#', 'fa fa-lock', 1, '2', 1, '2016-03-30 11:21:39', '2016-03-30 11:41:46'),
(3, 'ROLES', '#', 'fa fa-users', 1, '3', 1, '2016-03-30 11:41:35', '2016-03-30 11:41:46'),
(4, 'CREATE', '/users/create', 'fa fa-user', 1, '1', 2, '2016-03-30 11:49:22', '2016-03-30 11:49:22'),
(5, 'MANAGE', '/users', 'fa fa-users', 1, '2', 2, '2016-03-30 11:49:22', '2016-03-30 11:49:22'),
(6, 'CREATE', '/baptisms/create', 'fa fa-plus', 1, '2', 5, '2016-03-31 08:50:58', '2016-03-31 08:51:43'),
(7, 'MANAGE', '/baptisms', 'fa fa-list', 1, '1', 5, '2016-03-31 08:50:58', '2016-03-31 08:51:43'),
(8, 'MANAGE', '/parishes', 'fa fa-list', 1, '1', 7, '2016-03-31 08:54:46', '2016-03-31 08:54:46'),
(9, 'CREATE', '/parishes/create', 'fa fa-plus', 1, '2', 7, '2016-03-31 08:54:47', '2016-03-31 08:54:47'),
(10, 'CREATE', '/confirmations/create', 'fa fa-plus', 1, '2', 6, '2016-03-31 09:14:00', '2016-03-31 09:14:00'),
(11, 'MANAGE', '/confirmations', 'fa fa-list', 1, '1', 6, '2016-03-31 09:14:00', '2016-03-31 09:14:00');

INSERT INTO `sub_most_menu_items` (`sub_most_menu_item_id`, `sub_most_menu_item`, `sub_most_menu_item_url`, `sub_most_menu_item_icon`, `active`, `sequence`, `sub_menu_item_id`, `created_at`, `updated_at`) VALUES
(1, 'Header', '/menu-headers', 'fa fa-list', 1, '1', 1, '2016-03-30 11:15:33', '2016-03-30 11:15:33'),
(2, 'Menu', '/menus', 'fa fa-list', 1, '2', 1, '2016-03-30 11:16:39', '2016-03-30 11:16:39'),
(3, 'Menu Items', '/menu-items', 'fa fa-list', 1, '3', 1, '2016-03-30 11:17:42', '2016-03-30 11:18:54'),
(4, 'Sub Menu', '/sub-menu-items', 'fa fa-list', 1, '4', 1, '2016-03-30 11:18:54', '2016-03-30 11:18:54'),
(5, 'Sub-most Menu', '/sub-most-menu-items', 'fa fa-list', 1, '5', 1, '2016-03-30 11:19:42', '2016-03-30 11:25:09'),
(6, 'Manage', '/permissions', 'fa fa-list', 1, '1', 2, '2016-03-30 11:24:08', '2016-03-30 11:25:56'),
(7, 'Assign', '/permissions/roles-permissions/', 'fa fa-users', 1, '2', 2, '2016-03-30 11:34:38', '2016-03-30 11:35:07'),
(8, 'Manage', '/roles', 'fa fa-table', 1, '1', 3, '2016-03-30 11:43:20', '2016-03-30 11:43:20'),
(9, 'Assign', '/roles/users-roles', 'fa fa-users', 1, '2', 3, '2016-03-30 11:43:20', '2016-03-30 11:43:20');

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2016_03_03_195545_create_user_type_table', 1),
('2016_03_03_195633_create_users_table', 1),
('2016_03_03_195659_create_all_menus_table', 1),
('2016_03_05_060819_entrust_setup_tables', 1),
('2016_03_15_050508_create_roles_menus_assoc_tables', 1),
('2016_03_03_195545_create_user_type_table', 1),
('2016_03_03_195633_create_users_table', 1),
('2016_03_03_195659_create_all_menus_table', 1),
('2016_03_05_060819_entrust_setup_tables', 1),
('2016_03_15_050508_create_roles_menus_assoc_tables', 1);

INSERT INTO `permissions` (`permission_id`, `name`, `display_name`, `description`, `uri`, `created_at`, `updated_at`) VALUES
(1, 'AuthController@getLogin', 'User Login', '', 'auth/login/', '2016-03-30 11:28:56', '2016-03-30 11:28:56'),
(2, 'AuthController@getLogout', 'User Logout', '', 'auth/logout/', '2016-03-30 11:28:56', '2016-03-30 11:28:56'),
(3, 'AuthController@getRegister', 'Register a user', '', 'auth/register/', '2016-03-30 11:28:56', '2016-03-30 11:28:56'),
(4, 'DashboardController@getIndex', 'View dashboard information', '', 'dashboard/index/', '2016-03-30 11:28:56', '2016-03-30 11:28:56'),
(5, 'DashboardController@getIndexDashboard', 'View dashboard information', '', 'dashboard', '2016-03-30 11:28:56', '2016-03-30 11:28:56'),
(6, 'MenuController@getDelete', 'Delete Menu', '', 'menus/delete/', '2016-03-30 11:28:56', '2016-03-30 11:28:56'),
(7, 'MenuController@getIndex', 'Manage menu', '', 'menus/index/', '2016-03-30 11:28:56', '2016-03-30 11:28:56'),
(8, 'MenuController@getIndexMenus', 'Manage Menu', '', 'menus', '2016-03-30 11:28:56', '2016-03-30 11:28:56'),
(9, 'MenuHeaderController@getDelete', 'delete Menu header', '', 'menu-headers/delete/', '2016-03-30 11:28:56', '2016-03-30 11:28:56'),
(10, 'MenuHeaderController@getIndex', 'Manage menu header', '', 'menu-headers/index/', '2016-03-30 11:28:57', '2016-03-30 11:28:57'),
(11, 'MenuHeaderController@getIndexMenu-headers', 'Manage menu header', '', 'menu-headers', '2016-03-30 11:28:57', '2016-03-30 11:28:57'),
(12, 'MenuItemController@getDelete', 'Delete Menu-items', '', 'menu-items/delete/', '2016-03-30 11:28:57', '2016-03-30 11:28:57'),
(13, 'MenuItemController@getIndex', 'Manage menu items', '', 'menu-items/index/', '2016-03-30 11:28:57', '2016-03-30 11:28:57'),
(14, 'MenuItemController@getIndexMenu-items', 'Manage menu items', '', 'menu-items', '2016-03-30 11:28:57', '2016-03-30 11:28:57'),
(15, 'PermissionsController@getIndex', 'Manage Permissions', '', 'permissions/index/', '2016-03-30 11:28:57', '2016-03-30 11:28:57'),
(16, 'PermissionsController@getIndexPermissions', 'Manage Permissions', '', 'permissions', '2016-03-30 11:28:57', '2016-03-30 11:28:57'),
(17, 'PermissionsController@getRolesPermissions', 'assign Permissions', '', 'permissions/roles-permissions/', '2016-03-30 11:28:57', '2016-03-30 11:36:42'),
(18, 'RolesController@getDelete', 'Delete roles', '', 'roles/delete/', '2016-03-30 11:28:57', '2016-03-30 11:36:42'),
(19, 'RolesController@getIndex', 'Manage roles', '', 'roles/index/', '2016-03-30 11:28:57', '2016-03-30 11:36:42'),
(20, 'RolesController@getIndexRoles', 'Manage roles', '', 'roles', '2016-03-30 11:28:57', '2016-03-30 11:36:42'),
(21, 'RolesController@getUsersRoles', 'Manage users roles', '', 'roles/users-roles/', '2016-03-30 11:28:57', '2016-03-30 11:36:42'),
(22, 'SubMenuItemController@getDelete', 'Delete sub menu items', '', 'sub-menu-items/delete/', '2016-03-30 11:28:57', '2016-03-30 11:36:42'),
(23, 'SubMenuItemController@getIndex', 'Manage sub menu items', '', 'sub-menu-items/index/', '2016-03-30 11:28:57', '2016-03-30 11:36:42'),
(24, 'SubMenuItemController@getIndexSub-menu-items', 'Manage sub menu items', '', 'sub-menu-items', '2016-03-30 11:28:57', '2016-03-30 11:36:42'),
(25, 'SubMostMenuItemController@getDelete', 'Delete sub most menu items', '', 'sub-most-menu-items/delete/', '2016-03-30 11:28:57', '2016-03-30 11:36:43'),
(26, 'SubMostMenuItemController@getIndex', 'Manage sub most menu items', '', 'sub-most-menu-items/index/', '2016-03-30 11:28:57', '2016-03-30 11:36:43'),
(27, 'SubMostMenuItemController@getIndexSub-most-menu-items', 'Manage sub most menu items', '', 'sub-most-menu-items', '2016-03-30 11:28:57', '2016-03-30 11:36:43'),
(28, 'UserController@getChange', 'Change user', '', 'users/change/', '2016-03-30 11:28:57', '2016-03-30 11:38:03'),
(29, 'UserController@getCreate', 'Create a new user', '', 'users/create/', '2016-03-30 11:28:57', '2016-03-30 11:38:03'),
(30, 'UserController@getEdit', 'Edit existing user', '', 'users/edit/', '2016-03-30 11:28:57', '2016-03-30 11:38:03'),
(31, 'UserController@getIndex', 'Manage users', '', 'users/index/', '2016-03-30 11:28:58', '2016-03-30 11:38:03'),
(32, 'UserController@getIndexUsers', 'Manage users', '', 'users', '2016-03-30 11:28:58', '2016-03-30 11:38:03'),
(33, 'UserController@getShow', 'View user profile', '', 'users/show/', '2016-03-30 11:28:58', '2016-03-30 11:38:03'),
(34, 'UserController@getStatus', 'View users status', '', 'users/status/', '2016-03-30 11:28:58', '2016-03-30 11:40:13'),
(35, 'UserTypeController@getDelete', 'Delete user types', '', 'user-types/delete/', '2016-03-30 11:28:58', '2016-03-30 11:40:13'),
(36, 'UserTypeController@getIndex', 'Manage User types', '', 'user-types/index/', '2016-03-30 11:28:58', '2016-03-30 11:40:13'),
(37, 'UserTypeController@getIndexUser-types', 'Manage User types', '', 'user-types', '2016-03-30 11:28:58', '2016-03-30 11:40:13');

INSERT INTO `roles` (`role_id`, `name`, `display_name`, `description`, `user_type_id`, `created_at`, `updated_at`) VALUES
(1, 'developer', 'Developer', 'The software developer', 2, '2016-03-30 00:30:11', '2016-03-31 14:09:43'),
(2, 'super_admin', 'Super Admin', 'System Administrator', 1, '2016-03-30 11:51:57', '2016-03-31 14:08:59');


INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1);

INSERT INTO `roles_menus` (`role_id`, `menu_id`) VALUES
(1, 1),
(1, 2),
(1, 3);

INSERT INTO `roles_menu_headers` (`role_id`, `menu_header_id`) VALUES
(1, 1),
(1, 2),
(1, 3);


INSERT INTO `roles_menu_items` (`role_id`, `menu_item_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7);

INSERT INTO `roles_sub_menu_items` (`role_id`, `sub_menu_item_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11);

INSERT INTO `roles_sub_most_menu_items` (`role_id`, `sub_most_menu_item_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9);

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1);

INSERT INTO `user_types` (`user_type_id`, `user_type`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', NULL, NULL),
(2, 'Admin', NULL, NULL);
