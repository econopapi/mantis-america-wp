<?php
/**
 * Template para la página de producto individual
 * Con hero full-width y soporte para galerías múltiples
 *
 * @package Mantis-Astra-WooCommerce
 * @version 1.1.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 * Abre el wrapper del contenido
 */
do_action( 'woocommerce_before_main_content' );

?>

<style>
.woocommerce-breadcrumb,
.woocommerce-breadcrumb > a {
	color: #fff!important;
}

.mantis-product-card__title {
  font-size: 1.1rem!important;
}
</style>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>

	<?php 
	/**
	 * Setear el producto global para que todos los hooks funcionen
	 */
	global $product;
	?>

	<!-- Hero con fondo morado oscuro FULL WIDTH -->
	<div class="mantis-product-hero mantis-product-hero--fullwidth">
		
		<!-- Contenedor interno con max-width para el contenido -->
		<div class="mantis-product-hero__container">
			<div class="mantis-product-hero__inner">

				<!-- Columna izquierda: Galería de imágenes -->
				<div class="mantis-product-hero__gallery">
					<?php
					/**
					 * Hook: woocommerce_before_single_product_summary.
					 * 
					 * Este hook trae la galería de imágenes del producto
					 * @hooked woocommerce_show_product_sale_flash - 10 (badge de oferta)
					 * @hooked woocommerce_show_product_images - 20 (galería con thumbnails)
					 */
					do_action( 'woocommerce_before_single_product_summary' );
					?>
				</div>

				<!-- Columna derecha: Información del producto -->
				<div class="mantis-product-hero__summary">
					<div class="mantis-product-summary">
						<?php
						/**
						 * Hook: woocommerce_single_product_summary.
						 * 
						 * Este hook trae toda la información del producto:
						 * @hooked woocommerce_template_single_title - 5 (título)
						 * @hooked woocommerce_template_single_rating - 10 (estrellas de rating)
						 * @hooked woocommerce_template_single_price - 10 (precio)
						 * @hooked woocommerce_template_single_excerpt - 20 (descripción corta)
						 * @hooked woocommerce_template_single_add_to_cart - 30 (botón de compra)
						 * @hooked woocommerce_template_single_meta - 40 (SKU, categorías, tags)
						 * @hooked woocommerce_template_single_sharing - 50 (compartir en redes)
						 */
						do_action( 'woocommerce_single_product_summary' );
						?>
					</div>
				</div>

			</div>
		</div>

	</div>

	<!-- Sección de tabs (descripción, información adicional, reviews) -->
	<!-- Esta sección SÍ tiene max-width porque es contenido de lectura -->
	<div class="mantis-product-single-wrapper">
		<div class="mantis-product-tabs-wrapper">
			<?php
			/**
			 * Hook: woocommerce_after_single_product_summary.
			 * 
			 * Este hook trae los tabs de información del producto:
			 * @hooked woocommerce_output_product_data_tabs - 10 (tabs)
			 * @hooked woocommerce_upsell_display - 15 (productos relacionados/upsells)
			 * @hooked woocommerce_output_related_products - 20 (productos relacionados)
			 */
			do_action( 'woocommerce_after_single_product_summary' );
			?>
		</div>
	</div>

<?php endwhile; // fin del loop ?>

<?php

/**
 * Hook: woocommerce_after_main_content.
 * Cierra el wrapper del contenido
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 * Muestra el sidebar de WooCommerce si está habilitado
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );