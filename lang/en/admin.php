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

    // Project pages
    'custom_pages'          => 'Additional pages',
    'custom_pages_hint'     => 'Custom content pages, in addition to organization and activities.',
    'add_pages'             => 'Add pages',
    'add_custom_page'       => 'Add page',
    'page_name'             => 'Page name',
    'add_more'              => 'Add another',
    'pages_section_org'     => 'Organization',
    'pages_section_custom'  => 'Additional pages',
    'pages_section_act'     => 'Activities',
    'delete_page'           => 'Delete page',
    'rename_page'           => 'Rename page',
    'confirm_delete_page'   => 'Delete page <strong>:name</strong>? The HTML file will be removed.',
    'page_deleted'          => 'Page deleted.',
    'pages_created'         => 'Page(s) created.',
    'org_add_hint'          => 'Only adds new pages. Does not remove existing ones.',
    'organization_hint'     => 'Choose type and count. Missing pages are created on save; removed ones are deleted.',
    'add_org_semanas'       => 'Add week',
    'add_org_modulos'       => 'Add module',
    'add_org_unidades'      => 'Add unit',

    // Protection
    'protection'            => 'Protection',
    'protect_label'         => 'Protect from deletion',
    'protect_hint'          => 'Prevents accidental deletion of the project. Only admins can change this setting.',
    'activities_hint'       => 'Check to create, uncheck to delete. Changes apply on save.',
    'slug_collision'        => 'A page with that name already exists.',

    // Project edit tabs
    'project_sections'      => 'Project sections',
    'tab_config'            => 'Configuration',
    'tab_content'           => 'Content',
    'section_identification' => 'Identification',

    // Colors
    'brand_colors'          => 'Brand colors',
    'brand_colors_hint'     => 'Only 2 colors. The system generates the full palette automatically.',
    'color_primary'         => 'Primary',
    'color_secondary'       => 'Secondary',

    // Canvas nav colors
    'nav_colors'            => 'Canvas menu colors',
    'nav_colors_hint'       => 'Background and text of the Canvas sidebar.',
    'nav_bg_color'          => 'Background',
    'nav_text_color'        => 'Text / Icons',

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

    // Project validations
    'invalid_slug'          => 'The identifier contains invalid characters.',
    'project_exists'        => 'A project with that identifier already exists.',
    'project_create_error'  => 'Error creating the project directory.',
    'project_protected'     => 'This project is protected and cannot be deleted.',

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
