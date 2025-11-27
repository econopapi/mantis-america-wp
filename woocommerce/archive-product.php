<?php
/**
 * Custom archive product template for Mantis
 * Template para el catálogo de productos con diseño moderno
 *
 * @package Mantis-Astra-WooCommerce
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 * 
 * Hook que genera el wrapper del contenido y los breadcrumbs.
 * Funcionalidades útiles del hook:
 * - woocommerce_output_content_wrapper - 10 (abre los divs del contenido)
 * - woocommerce_breadcrumb - 20 (las miguitas de pan)
 * - WC_Structured_Data::generate_website_data() - 30 (datos estructurados para SEO)
 */
do_action( 'woocommerce_before_main_content' );

?>

<div class="mantis-shop-wrapper">

	<?php
	/**
	 * Sección Hero del Catálogo
	 * Se puede poner banner, título destacado, o lo que se necesite
	 * Comentalo si no lo vas a usar por ahora
	 */
	if ( is_shop() ) : ?>
		<div class="mantis-shop-hero">
			<div class="mantis-shop-hero__content">
				<h1 class="mantis-shop-hero__title">
					<?php woocommerce_page_title(); ?>
				</h1>
				<?php 
				// Descripción de la tienda (si la configuraste en WooCommerce > Ajustes)
				if ( term_description() ) : ?>
					<div class="mantis-shop-hero__description">
						<?php echo term_description(); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_shop_loop_header.
	 * Esto muestra el header de categorías cuando estás en una taxonomía
	 * @hooked woocommerce_product_taxonomy_archive_header - 10
	 */
	do_action( 'woocommerce_shop_loop_header' );
	?>

	<?php if ( woocommerce_product_loop() ) : ?>

		<div class="mantis-shop-controls">
			<?php
			/**
			 * Hook: woocommerce_before_shop_loop.
			 * 
			 * Estos hooks traen los controles del catálogo:
			 * - woocommerce_output_all_notices - 10 (notificaciones y mensajes)
			 * - woocommerce_result_count - 20 (muestra "Mostrando 1-12 de 50 resultados")
			 * - woocommerce_catalog_ordering - 30 (dropdown de ordenar por precio, etc.)
			 */
			do_action( 'woocommerce_before_shop_loop' );
			?>
		</div>

		<?php
		/**
		 * ACÁ EMPIEZA LA MAGIA
		 * En lugar de usar woocommerce_product_loop_start() que genera un <ul>,
		 * usamos nuestro propio contenedor con clases custom.
		 * Esto nos da control total sobre el layout con CSS Grid o Flexbox.
		 */
		?>
		<div class="mantis-products-grid">

			<?php
			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();

					/**
					 * Hook: woocommerce_shop_loop.
					 * Este hook está vacío por defecto, pero algunos plugins lo usan
					 * Lo dejamos por si acaso tenés algún plugin que se engancha acá
					 */
					do_action( 'woocommerce_shop_loop' );

					/**
					 * ACÁ SE CARGA TU content-product.php CUSTOMIZADO
					 * Como cambiamos el elemento de <li> a <div>, esto va a funcionar perfecto
					 */
					wc_get_template_part( 'content', 'product' );
				}
			}
			?>

		</div>
		<?php
		// Acá cerramos nuestro grid custom
		// Antes se usaba woocommerce_product_loop_end() que cerraba el </ul>
		?>

		<div class="mantis-shop-footer">
			<?php
			/**
			 * Hook: woocommerce_after_shop_loop.
			 * @hooked woocommerce_pagination - 10 (la paginación 1, 2, 3, etc.)
			 */
			do_action( 'woocommerce_after_shop_loop' );
			?>
		</div>

	<?php else : ?>

		<?php
		/**
		 * Hook: woocommerce_no_products_found.
		 * Muestra el mensaje cuando no hay productos
		 * @hooked wc_no_products_found - 10
		 */
		do_action( 'woocommerce_no_products_found' );
		?>

	<?php endif; ?>

</div><!-- .mantis-shop-wrapper -->

<?php

/**
 * Hook: woocommerce_after_main_content.
 * Cierra el wrapper del contenido
 * @hooked woocommerce_output_content_wrapper_end - 10
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 * Muestra el sidebar de WooCommerce si tu tema lo tiene habilitado
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );