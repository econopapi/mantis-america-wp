<?php
/**
 * Mantis Astra Theme Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Mantis Astra Theme
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_MANTIS_ASTRA_THEME_VERSION', '1.0.1' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {
	wp_enqueue_style( 'mantis-astra-theme-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_MANTIS_ASTRA_THEME_VERSION, 'all' );
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

/**
 * ====================================
 * SISTEMA DE BANNER DE ANUNCIOS GLOBAL
 * ====================================
 * 
 * Este código permite configurar un banner de anuncio que aparece arriba de todo el sitio, de forma global
 * Se administra desde una página dedicada en el dashboard de wordpress en el menú `Apariencia`
 */


/**
 * Crear la página de configuración del banner en el admin
 * Esta función agrega un nuevo menú en "Apariencia" llamado "Banner de Anuncios"
 * 
 * @return void
 */
function mantis_agregar_pagina_banner() {
	add_theme_page(
		'Configuración del Banner de Texto',           // Título de la página
		'Mantis Banner de Anuncios',                 // Texto del menú
		'manage_options',                     // Capacidad requerida (solo administradores)
		'mantis-banner-config',               // Slug único de la página
		'mantis_renderizar_pagina_banner'    // Función que muestra el contenido
	);
}
add_action( 'admin_menu', 'mantis_agregar_pagina_banner' );


/**
 * Registrar los settings del banner en db de WordPress
 * 
 * @return void
 */
function mantis_registrar_settings_banner() {
	// registro de opciones 'mantis_banner_settings'
	register_setting( 
		'mantis_banner_settings',              // Grupo de opciones
		'mantis_banner_activo',                // Nombre de la opción individual
		array( 'sanitize_callback' => 'mantis_sanitizar_checkbox' )
	);
	
	register_setting( 
		'mantis_banner_settings', 
		'mantis_banner_texto',
		array( 'sanitize_callback' => 'sanitize_text_field' )
	);
	
	register_setting( 
		'mantis_banner_settings', 
		'mantis_banner_enlace',
		array( 'sanitize_callback' => 'esc_url_raw' )
	);
	
	register_setting( 
		'mantis_banner_settings', 
		'mantis_banner_color',
		array( 
			'sanitize_callback' => 'sanitize_hex_color',
			'default' => '#ff8800'
		)
	);
}
add_action( 'admin_init', 'mantis_registrar_settings_banner' );


/**
 * Función helper para sanitizar checkboxes.
 * Se transforma respuesta a '1' o '0'
 * 
 * @param $valor Valor recibido en el checkbox
 * @return string '1' si está seleccionado, '0' si no lo está
 */
function mantis_sanitizar_checkbox( $valor ) {
	return ( isset( $valor ) && $valor == '1' ) ? '1' : '0';
}


/**
 * Página de condiguración en Dashboard > Apariencia > Mantis Banner de Anuncios
 * @return void
 */
function mantis_renderizar_pagina_banner() {
	// verificación de permisos
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	// obtener opciones guardadas
	$activo = get_option( 'mantis_banner_activo', '0' );
	$texto = get_option( 'mantis_banner_texto', '' );
	$enlace = get_option( 'mantis_banner_enlace', '' );
	$color = get_option( 'mantis_banner_color', '#ff8800' );
	
	// mensaje de éxito al guardar
	if ( isset( $_GET['settings-updated'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>¡Configuración guardada exitosamente!</p></div>';
	}
	?>
	
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		
		<p class="description">
			Configurá el banner de anuncio que aparece arriba de todo el sitio. 
			Este banner es visible en todas las páginas cuando está activado.
		</p>
		
		<!-- preview del banner -->
		<div class="mantis-banner-preview"
			 style="margin: 20px 0; padding: 20px; background: #f0f0f1; border-radius: 4px;">

			<h2 style="margin-top: 0;">Vista Previa</h2>

			<div id="mantis-preview-container">
				<?php if ( $activo == '1' && ! empty( $texto ) ) : ?>
					<?php if ( ! empty( $enlace ) ) : ?>
						<a href="<?php echo esc_url( $enlace ); ?>" 
						   class="mantis-anuncio-banner" 
						   id="mantis-preview-banner"
						   style="background-color: <?php echo esc_attr( $color ); ?>; display: block; padding: 12px 20px; text-align: center; text-decoration: none; color: white; font-weight: 600; transition: opacity 0.3s ease; border-radius: 4px;">
							<?php echo esc_html( $texto ); ?>
						</a>
					<?php else : ?>
						<div class="mantis-anuncio-banner" 
						     id="mantis-preview-banner"
						     style="background-color: <?php echo esc_attr( $color ); ?>; display: block; padding: 12px 20px; text-align: center; color: white; font-weight: 600; border-radius: 4px;">
							<?php echo esc_html( $texto ); ?>
						</div>
					<?php endif; ?>
				<?php else : ?>
					<p style="color: #666; font-style: italic;">
						El banner no se mostrará porque <?php echo $activo != '1' ? 'está desactivado' : 'no tiene texto configurado'; ?>.
					</p>
				<?php endif; ?>
			</div>
		</div>
		
		<!-- form de configuración -->
		<form method="post" action="options.php" id="mantis-banner-form">
			<?php 
			// hidden fields necesarios para que WordPress procese el formulario
			settings_fields( 'mantis_banner_settings' );
			?>
			
			<table class="form-table" role="presentation">
				<!-- Campo: Activar/Desactivar banner -->
				<tr>
					<th scope="row">
						<label for="mantis_banner_activo">Estado del Banner</label>
					</th>
					<td>
						<label>
							<input type="checkbox" 
							       name="mantis_banner_activo" 
							       id="mantis_banner_activo" 
							       value="1" 
							       <?php checked( $activo, '1' ); ?>>
							<strong>Activar banner</strong>
						</label>
						<p class="description">
							Desmarcá esta opción para ocultar temporalmente el banner sin borrar su contenido.
						</p>
					</td>
				</tr>
				
				<!-- Campo: Texto del anuncio -->
				<tr>
					<th scope="row">
						<label for="mantis_banner_texto">Texto del Anuncio</label>
					</th>
					<td>
						<input type="text" 
						       name="mantis_banner_texto" 
						       id="mantis_banner_texto" 
						       value="<?php echo esc_attr( $texto ); ?>" 
						       class="regular-text"
						       placeholder="Ej: ¡ENVÍO GRATIS EN COMPRAS SUPERIORES A $5000!">
						<p class="description">
							El mensaje que se mostrará en el banner. Tratá de mantenerlo corto y claro.
						</p>
					</td>
				</tr>
				
				<!-- Campo: Enlace (opcional) -->
				<tr>
					<th scope="row">
						<label for="mantis_banner_enlace">Enlace (opcional)</label>
					</th>
					<td>
						<input type="url" 
						       name="mantis_banner_enlace" 
						       id="mantis_banner_enlace" 
						       value="<?php echo esc_attr( $enlace ); ?>" 
						       class="regular-text"
						       placeholder="https://mantis-america.com/promociones">
						<p class="description">
							Si ingresás una URL, todo el banner será clickeable y llevará a esta dirección. Dejalo vacío si no querés que sea clickeable.
						</p>
					</td>
				</tr>
				
				<!-- Campo: Color de fondo -->
				<tr>
					<th scope="row">
						<label for="mantis_banner_color">Color de Fondo</label>
					</th>
					<td>
						<input type="color" 
						       name="mantis_banner_color" 
						       id="mantis_banner_color" 
						       value="<?php echo esc_attr( $color ); ?>">
						<p class="description">
							Elegí el color de fondo del banner. Asegurate de que el texto blanco sea legible sobre el color elegido.
						</p>
					</td>
				</tr>
			</table>
			
			<?php submit_button( 'Guardar Configuración' ); ?>
		</form>
		
		<!-- Script para actualizar la vista previa en tiempo real -->
		<script>
		(function() {
			// Obtener referencias a los campos del formulario
			const activoCheckbox = document.getElementById('mantis_banner_activo');
			const textoInput = document.getElementById('mantis_banner_texto');
			const enlaceInput = document.getElementById('mantis_banner_enlace');
			const colorInput = document.getElementById('mantis_banner_color');
			const previewContainer = document.getElementById('mantis-preview-container');
			
			// Función que actualiza la vista previa
			function actualizarPreview() {
				const activo = activoCheckbox.checked;
				const texto = textoInput.value.trim();
				const enlace = enlaceInput.value.trim();
				const color = colorInput.value;
				
				// Si está desactivado o no hay texto, mostrar mensaje
				if (!activo || !texto) {
					const razon = !activo ? 'está desactivado' : 'no tiene texto configurado';
					previewContainer.innerHTML = '<p style="color: #666; font-style: italic;">El banner no se mostrará porque ' + razon + '.</p>';
					return;
				}
				
				// Crear el HTML del banner
				const estiloBase = 'background-color: ' + color + '; display: block; padding: 12px 20px; text-align: center; color: white; font-weight: 600; border-radius: 4px;';
				
				if (enlace) {
					previewContainer.innerHTML = '<a href="' + enlace + '" class="mantis-anuncio-banner" style="' + estiloBase + ' text-decoration: none; transition: opacity 0.3s ease;">' + texto + '</a>';
				} else {
					previewContainer.innerHTML = '<div class="mantis-anuncio-banner" style="' + estiloBase + '">' + texto + '</div>';
				}
			}
			
			// Escuchar cambios en todos los campos
			activoCheckbox.addEventListener('change', actualizarPreview);
			textoInput.addEventListener('input', actualizarPreview);
			enlaceInput.addEventListener('input', actualizarPreview);
			colorInput.addEventListener('input', actualizarPreview);
		})();
		</script>
		
		<style>
			#mantis-preview-banner:hover {
				opacity: 0.9;
				cursor: pointer;
			}
		</style>
	</div>
	
	<?php
}


/**
 * Mostrar el banner en el frontend del sitio
 * Esta función lee la configuración guardada y renderiza el banner
 * arriba del header de Astra usando el hook astra_header_before
 * 
 * @return void
 */
function mantis_mostrar_banner_frontend() {
	// Obtener la configuración guardada
	$activo = get_option( 'mantis_banner_activo', '0' );
	$texto = get_option( 'mantis_banner_texto', '' );
	$enlace = get_option( 'mantis_banner_enlace', '' );
	$color = get_option( 'mantis_banner_color', '#ff8800' );
	
	// No mostrar nada si está desactivado o no hay texto
	if ( $activo != '1' || empty( $texto ) ) {
		return;
	}
	
	// Renderear el HTML del banner
	if ( ! empty( $enlace ) ) {
		?>
		<a href="<?php echo esc_url( $enlace ); ?>" 
		   class="mantis-anuncio-banner-global" 
		   style="background-color: <?php echo esc_attr( $color ); ?>; display: block; padding: 12px 20px; text-align: center; text-decoration: none; color: white; font-weight: 600; transition: opacity 0.3s ease;">
			<?php echo esc_html( $texto ); ?>
		</a>
		<?php
	} else {
		?>
		<div class="mantis-anuncio-banner-global" 
		     style="background-color: <?php echo esc_attr( $color ); ?>; display: block; padding: 12px 20px; text-align: center; color: white; font-weight: 600;">
			<?php echo esc_html( $texto ); ?>
		</div>
		<?php
	}
}
add_action( 'astra_header_before', 'mantis_mostrar_banner_frontend' );


/**
 * Estilos adicionales para el banner en el frontend
 * Estos estilos mejoran la experiencia de usuario y hacen el banner responsive
 * 
 * @return void
 */
function mantis_banner_frontend_styles() {
	?>
	<style>
		/* Estilos para el banner de anuncio global */
		.mantis-anuncio-banner-global {
			position: relative;
			z-index: 999;
		}
		
		.mantis-anuncio-banner-global:hover {
			opacity: 0.9;
			cursor: pointer;
		}
		
		/* Hacer el banner responsive para móviles */
		@media (max-width: 768px) {
			.mantis-anuncio-banner-global {
				font-size: 14px;
				padding: 10px 15px !important;
			}
		}
		
		/* En pantallas muy pequeñas, reducir aún más */
		@media (max-width: 480px) {
			.mantis-anuncio-banner-global {
				font-size: 12px;
				padding: 8px 10px !important;
			}
		}
	</style>
	<?php
}
add_action( 'wp_head', 'mantis_banner_frontend_styles' );