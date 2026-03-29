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
    'org_add_hint'          => 'Solo agrega nuevas páginas. No elimina las existentes.',

    // Colores
    'brand_colors'          => 'Colores de la marca',
    'brand_colors_hint'     => 'Solo 2 colores. El sistema genera la paleta completa automáticamente.',
    'color_primary'         => 'Primario',
    'color_secondary'       => 'Secundario',

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
    'user_activated'            => 'Usuario activado.',
    'user_deactivated'          => 'Usuario desactivado.',
    'user_deleted'              => 'Usuario eliminado.',

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
