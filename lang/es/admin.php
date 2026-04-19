<?php

return [
    // General
    'title'                 => 'Administración',
    'tab_projects'          => 'Proyectos',
    'tab_users'             => 'Usuarios',
    'tab_docs'              => 'Documentación',

    // Proyectos
    'new_project'           => 'Nuevo Proyecto',
    'create_project'        => 'Crear Proyecto',
    'edit_project'          => 'Editar Proyecto',
    'delete_project'        => 'Eliminar Proyecto',
    'project_name'          => 'Nombre del proyecto',
    'project_name_hint'     => 'Ej: Mi Curso de Historia',
    'project_folder'        => 'Carpeta',
    'project_created'       => 'Proyecto ":name" creado correctamente.',
    'project_updated'       => 'Proyecto actualizado correctamente.',
    'project_deleted'       => 'Proyecto eliminado correctamente.',
    'project_activated'     => 'Proyecto activado.',
    'project_deactivated'   => 'Proyecto desactivado.',
    'confirm_delete'        => '¿Estás seguro de eliminar <strong>:name</strong>? Esta acción no se puede deshacer.',
    'no_projects'           => 'No hay proyectos creados.',
    'no_projects_hint'      => 'Haz clic en <strong>Nuevo Proyecto</strong> para comenzar.',

    // Organización
    'organization'          => 'Organización del contenido',
    'org_none'              => 'Sin organización',
    'org_weeks'             => 'Semanas',
    'org_modules'           => 'Módulos',
    'org_units'             => 'Unidades',
    'org_count'             => 'Cantidad',

    // Páginas del proyecto
    'custom_pages'          => 'Páginas adicionales',
    'custom_pages_hint'     => 'Páginas de contenido personalizado, adicionales a la organización y actividades.',
    'add_pages'             => 'Agregar páginas',
    'add_custom_page'       => 'Agregar página',
    'page_name'             => 'Nombre de la página',
    'add_more'              => 'Agregar otra',
    'pages_section_org'     => 'Organización',
    'pages_section_custom'  => 'Páginas adicionales',
    'pages_section_act'     => 'Actividades',
    'delete_page'           => 'Eliminar página',
    'rename_page'           => 'Renombrar página',
    'confirm_delete_page'   => '¿Eliminar la página <strong>:name</strong>? El archivo HTML se eliminará.',
    'page_deleted'          => 'Página eliminada.',
    'pages_created'         => 'Página(s) creada(s).',
    'org_add_hint'          => 'Solo agrega nuevas páginas. No elimina las existentes.',
    'organization_hint'     => 'Define el tipo y cantidad de páginas. Las faltantes se crean al guardar; las eliminadas aquí se borran del proyecto.',
    'add_org_semanas'       => 'Agregar semana',
    'add_org_modulos'       => 'Agregar módulo',
    'add_org_unidades'      => 'Agregar unidad',

    // Protección
    'protection'            => 'Protección',
    'protect_label'         => 'Proteger contra eliminación',
    'protect_hint'          => 'Impide que el proyecto sea eliminado accidentalmente. Solo administradores pueden cambiar este ajuste.',
    'activities_hint'       => 'Marca para crear, desmarca para eliminar. Los cambios se aplican al guardar.',
    'slug_collision'        => 'Ya existe una página con ese nombre.',

    // Pestañas de edición de proyecto
    'project_sections'      => 'Secciones del proyecto',
    'tab_config'            => 'Configuración',
    'tab_content'           => 'Contenido',
    'section_identification' => 'Identificación',

    // Colores
    'brand_colors'          => 'Colores de la marca',
    'brand_colors_hint'     => 'Solo 2 colores. El sistema genera la paleta completa automáticamente.',
    'color_primary'         => 'Primario',
    'color_secondary'       => 'Secundario',

    // Colores del nav Canvas
    'nav_colors'            => 'Colores del menú Canvas',
    'nav_colors_hint'       => 'Fondo y texto de la barra lateral de Canvas.',
    'nav_bg_color'          => 'Fondo',
    'nav_text_color'        => 'Texto / Íconos',

    // CDNs
    'external_libs'         => 'Librerías externas (@import en CSS)',
    'cdn_bootstrap'         => 'Bootstrap 5 CSS',
    'cdn_bootstrap_desc'    => 'Grid, utilidades, componentes',
    'cdn_bootstrap_icons'   => 'Bootstrap Icons',
    'cdn_bootstrap_icons_desc' => '2,000+ íconos SVG',
    'cdn_fontawesome'       => 'Font Awesome 6',
    'cdn_fontawesome_desc'  => 'Íconos vectoriales',
    'cdn_animate'           => 'Animate.css',
    'cdn_animate_desc'      => 'Animaciones CSS predefinidas',

    // Compilar
    'compile_css'           => 'Compilar CSS',
    'compile_success'       => 'CSS compilado correctamente.',
    'compile_error'         => 'Error al compilar CSS.',

    // Validaciones de proyecto
    'invalid_slug'          => 'El identificador contiene caracteres inválidos.',
    'project_exists'        => 'Ya existe un proyecto con ese identificador.',
    'project_create_error'  => 'Error al crear el directorio del proyecto.',
    'project_protected'     => 'Este proyecto está protegido y no puede eliminarse.',

    // Usuarios
    'users_title'           => 'Usuarios',
    'add_user'              => 'Agregar Usuario',
    'create_guest'          => 'Crear Usuario Invitado',
    'username'              => 'Usuario',
    'user_role'             => 'Rol',
    'user_permissions'      => 'Permisos',
    'change_password'       => 'Cambiar contraseña',
    'new_password'          => 'Nueva contraseña',
    'password_changed'      => 'Contraseña actualizada correctamente.',
    'user_created'          => 'Usuario creado correctamente.',

    // Roles y permisos
    'role_admin'            => 'Administrador',
    'role_editor'           => 'Editor',
    'role_guest'            => 'Invitado',
    'perms_admin'           => 'Crear, editar, eliminar, compilar, gestionar usuarios',
    'perms_editor'          => 'Crear, editar proyectos propios, compilar, exportar',
    'perms_guest'           => 'Ver proyectos, ver código, documentación',

    // Correo de recuperación
    'recovery_email'        => 'Correo de recuperación',
    'recovery_hint'         => 'Se usa para resetear contraseñas si las olvidas.',
    'recovery_saved'        => 'Correo actualizado correctamente.',
    'recovery_missing'      => 'Sin correo configurado. No podrás recuperar acceso si olvidas las contraseñas.',

    // Acciones de usuario
    'username_taken'            => 'Ese nombre de usuario ya existe.',
    'cannot_deactivate_self'    => 'No puedes desactivar tu propia cuenta.',
    'cannot_delete_self'        => 'No puedes eliminar tu propia cuenta.',
    'cannot_delete_last_admin'  => 'No puedes eliminar el último administrador.',
    'cannot_change_own_role'    => 'No puedes cambiar tu propio rol.',
    'cannot_demote_last_admin'  => 'No puedes quitar el rol de administrador al único admin.',
    'invalid_current_password'  => 'La contraseña actual es incorrecta.',
    'user_activated'            => 'Usuario activado.',
    'user_deactivated'          => 'Usuario desactivado.',
    'user_deleted'              => 'Usuario eliminado.',
    'user_updated'              => 'Usuario actualizado correctamente.',
    'edit_user'                 => 'Editar usuario',
    'email'                     => 'Correo electrónico',
    'email_required'            => 'El correo electrónico es obligatorio.',
    'current_password'          => 'Tu contraseña actual',
    'current_password_hint'     => 'Ingresa tu contraseña para confirmar este cambio.',
    'last_login'                => 'Último acceso',
    'never_logged_in'           => 'Sin acceso',
    'created'                   => 'Creado',
    'actions'                   => 'Acciones',
    'copy_password'             => 'Copiar contraseña',
    'credentials_created'       => 'Credenciales creadas',
    'search_users'              => 'Buscar usuarios...',
    'filter_all'                => 'Todos',
    'filter_active'             => 'Activos',
    'filter_inactive'           => 'Inactivos',
    'no_users_found'            => 'No se encontraron usuarios.',
    'confirm_delete_msg'        => '¿Eliminar al usuario <strong>:name</strong>?',
    'confirm_toggle'            => '¿:action al usuario <strong>:name</strong>?',
    'confirm_toggle_deactivate' => 'El usuario no podrá iniciar sesión.',

    // Estado de proyectos
    'pages_count'           => ':count página|:count páginas',
    'badge_demo'            => 'Demo',
    'badge_inactive'        => 'Inactivo',
    'activate'              => 'Activar',
    'deactivate'            => 'Desactivar',
    'open'                  => 'Abrir',

    // Exportar
    'export_project'        => 'Exportar proyecto como ZIP',
];
