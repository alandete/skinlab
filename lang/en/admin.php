<?php

return [
    // General
    'title'                 => 'Administration',
    'tab_projects'          => 'Projects',
    'tab_users'             => 'Users',
    'tab_docs'              => 'Documentation',

    // Projects
    'new_project'           => 'New Project',
    'create_project'        => 'Create Project',
    'edit_project'          => 'Edit Project',
    'delete_project'        => 'Delete Project',
    'project_name'          => 'Project name',
    'project_name_hint'     => 'E.g.: My History Course',
    'project_folder'        => 'Folder',
    'project_created'       => 'Project ":name" created successfully.',
    'project_updated'       => 'Project updated successfully.',
    'project_deleted'       => 'Project deleted successfully.',
    'project_activated'     => 'Project activated.',
    'project_deactivated'   => 'Project deactivated.',
    'confirm_delete'        => 'Are you sure you want to delete <strong>:name</strong>? This action cannot be undone.',
    'no_projects'           => 'No projects created.',
    'no_projects_hint'      => 'Click <strong>New Project</strong> to get started.',

    // Organization
    'organization'          => 'Content organization',
    'org_none'              => 'No organization',
    'org_weeks'             => 'Weeks',
    'org_modules'           => 'Modules',
    'org_units'             => 'Units',
    'org_count'             => 'Count',
    'org_add_hint'          => 'Only adds new pages. Does not remove existing ones.',

    // Colors
    'brand_colors'          => 'Brand colors',
    'brand_colors_hint'     => 'Only 2 colors. The system generates the full palette automatically.',
    'color_primary'         => 'Primary',
    'color_secondary'       => 'Secondary',

    // CDNs
    'external_libs'         => 'External libraries (@import in CSS)',
    'cdn_bootstrap'         => 'Bootstrap 5 CSS',
    'cdn_bootstrap_desc'    => 'Grid, utilities, components',
    'cdn_bootstrap_icons'   => 'Bootstrap Icons',
    'cdn_bootstrap_icons_desc' => '2,000+ SVG icons',
    'cdn_fontawesome'       => 'Font Awesome 6',
    'cdn_fontawesome_desc'  => 'Vector icons',
    'cdn_animate'           => 'Animate.css',
    'cdn_animate_desc'      => 'Predefined CSS animations',

    // Compile
    'compile_css'           => 'Compile CSS',
    'compile_success'       => 'CSS compiled successfully.',
    'compile_error'         => 'Error compiling CSS.',

    // Users
    'users_title'           => 'Users',
    'add_user'              => 'Add User',
    'create_guest'          => 'Create Guest User',
    'username'              => 'Username',
    'user_role'             => 'Role',
    'user_permissions'      => 'Permissions',
    'change_password'       => 'Change password',
    'new_password'          => 'New password',
    'password_changed'      => 'Password updated successfully.',
    'user_created'          => 'User created successfully.',

    // Roles & permissions
    'role_admin'            => 'Administrator',
    'role_editor'           => 'Editor',
    'role_guest'            => 'Guest',
    'perms_admin'           => 'Create, edit, delete, compile, manage users',
    'perms_editor'          => 'Create, edit own projects, compile, export',
    'perms_guest'           => 'View projects, view code, documentation',

    // Recovery email
    'recovery_email'        => 'Recovery email',
    'recovery_hint'         => 'Used to reset passwords if you forget them.',
    'recovery_saved'        => 'Email updated successfully.',
    'recovery_missing'      => 'No email configured. You won\'t be able to recover access if you forget passwords.',

    // User actions
    'username_taken'            => 'That username already exists.',
    'cannot_deactivate_self'    => 'You cannot deactivate your own account.',
    'cannot_delete_self'        => 'You cannot delete your own account.',
    'cannot_delete_last_admin'  => 'You cannot delete the last administrator.',
    'cannot_change_own_role'    => 'You cannot change your own role.',
    'cannot_demote_last_admin'  => 'You cannot demote the last administrator.',
    'invalid_current_password'  => 'Current password is incorrect.',
    'user_activated'            => 'User activated.',
    'user_deactivated'          => 'User deactivated.',
    'user_deleted'              => 'User deleted.',
    'user_updated'              => 'User updated successfully.',
    'edit_user'                 => 'Edit user',
    'email'                     => 'Email',
    'email_required'            => 'Email is required.',
    'current_password'          => 'Your current password',
    'current_password_hint'     => 'Enter your password to confirm this change.',
    'last_login'                => 'Last login',
    'never_logged_in'           => 'Never logged in',
    'created'                   => 'Created',
    'actions'                   => 'Actions',
    'copy_password'             => 'Copy password',
    'credentials_created'       => 'Credentials created',
    'search_users'              => 'Search users...',
    'filter_all'                => 'All',
    'filter_active'             => 'Active',
    'filter_inactive'           => 'Inactive',
    'no_users_found'            => 'No users found.',
    'confirm_delete_msg'        => 'Delete user <strong>:name</strong>?',
    'confirm_toggle'            => ':action user <strong>:name</strong>?',
    'confirm_toggle_deactivate' => 'The user will not be able to log in.',

    // Project status
    'pages_count'           => ':count page|:count pages',
    'badge_demo'            => 'Demo',
    'badge_inactive'        => 'Inactive',
    'activate'              => 'Activate',
    'deactivate'            => 'Deactivate',
    'open'                  => 'Open',

    // Export
    'export_project'        => 'Export project as ZIP',
];
