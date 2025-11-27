<?php
/**
 * Custom product card template for Mantis
 * Versión customizada para un diseño moderno de catálogo
 *
 * @package Mantis-Astra-WooCommerce
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Verificar que el producto existe y es visible
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}
?>

<div <?php wc_product_class( 'mantis-product-card', $product ); ?>>
	
	<div class="mantis-product-card__image-wrapper">
		<?php 
		// Mostrar badge de oferta si aplica
		if ( $product->is_on_sale() ) : ?>
			<span class="mantis-product-card__sale-badge">¡OFERTA!</span>
		<?php endif; ?>
		
		<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="mantis-product-card__image-link">
			<?php 
			// Mostrar la imagen del producto
			echo $product->get_image( 'woocommerce_thumbnail', array( 
				'class' => 'mantis-product-card__image',
				'loading' => 'lazy' 
			) ); 
			?>
		</a>

		<?php 
		// Si el producto no tiene stock, mostrar badge
		if ( ! $product->is_in_stock() ) : ?>
			<span class="mantis-product-card__out-of-stock">Sin stock</span>
		<?php endif; ?>
	</div>

	<div class="mantis-product-card__content">
		
		<?php 
		// Mostrar categorías del producto (Opcional)
		$categories = wc_get_product_category_list( $product->get_id(), ', ' );
		if ( $categories ) : ?>
			<div class="mantis-product-card__categories">
				<?php echo wp_kses_post( $categories ); ?>
			</div>
		<?php endif; ?>

		<h2 class="mantis-product-card__title">
			<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
				<?php echo esc_html( $product->get_name() ); ?>
			</a>
		</h2>

		<?php 
		// Mostrar descripción corta si existe (opcional)
		$short_description = $product->get_short_description();
		if ( $short_description && strlen( $short_description ) > 0 ) : ?>
			<div class="mantis-product-card__excerpt">
				<?php echo wp_trim_words( $short_description, 15, '...' ); ?>
			</div>
		<?php endif; ?>

		<div class="mantis-product-card__footer">
			<div class="mantis-product-card__price">
				<?php echo $product->get_price_html(); ?>
			</div>

			<div class="mantis-product-card__actions">
				<?php 
				// Botón de agregar al carrito
				// Este hook mantiene la funcionalidad AJAX de WooCommerce
				woocommerce_template_loop_add_to_cart(); 
				?>
			</div>
		</div>

	</div>

</div>