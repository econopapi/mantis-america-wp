<?php
/**
 * Template para páginas de categorías individuales
 * Con filtros por subcategorías y navegación mejorada
 *
 * @package Mantis-Astra-WooCommerce
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 */
do_action( 'woocommerce_before_main_content' );

// Obtener la categoría actual
$current_category = get_queried_object();
$current_category_id = $current_category->term_id;

// Obtener imagen de la categoría si existe
$thumbnail_id = get_term_meta( $current_category_id, 'thumbnail_id', true );
$category_image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';

?>

<style>
.woocommerce-breadcrumb {
    text-align: center;
    width: 100%; /* Ensures the container takes full width for centering to work */
    display: block; /* Ensures it acts like a block element if it's inline by default */
}
</style>

<div class="mantis-shop-wrapper">

	<!-- Hero de la categoría con fondo morado -->
	<div class="mantis-category-hero">
		<?php if ( $category_image ) : ?>
			<div class="mantis-category-hero__background" 
			     style="background-image: url('<?php echo esc_url( $category_image ); ?>');">
			</div>
		<?php endif; ?>
		
		<div class="mantis-category-hero__overlay"></div>
		
		<div class="mantis-category-hero__content">
			<h1 class="mantis-category-hero__title">
				<?php echo esc_html( $current_category->name ); ?>
			</h1>
			
			<?php if ( $current_category->description ) : ?>
				<div class="mantis-category-hero__description">
					<?php echo wp_kses_post( $current_category->description ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php
	/**
	 * Sección de filtros de categorías y subcategorías
	 */
	
	// Traer todas las categorías principales para el primer dropdown
	$parent_categories = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'orderby'    => 'menu_order',
	) );

	// Traer las subcategorías de la categoría actual (si existen)
	$subcategories = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => $current_category_id,
		'orderby'    => 'menu_order',
	) );

	// Si la categoría actual tiene parent, traer sus hermanas (subcategorías del mismo padre)
	$sibling_categories = array();
	if ( $current_category->parent != 0 ) {
		$sibling_categories = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => $current_category->parent,
			'orderby'    => 'menu_order',
		) );
	}

	// Determinar si debemos mostrar los filtros
	$show_filters = ! empty( $parent_categories ) || ! empty( $subcategories ) || ! empty( $sibling_categories );

	if ( $show_filters ) :
	?>

	<div class="mantis-category-filters">
		<div class="mantis-category-filters__inner">
			
			<?php 
			/**
			 * DROPDOWN 1: Categorías principales
			 * Este dropdown siempre se muestra y permite navegar entre categorías principales
			 */
			if ( ! empty( $parent_categories ) ) : 
			?>
				<div class="mantis-filter-group">
					<label for="mantis-category-select" class="mantis-filter-group__label">
						Categoría:
					</label>
					<select id="mantis-category-select" 
					        class="mantis-filter-select" 
					        onchange="if(this.value) window.location.href = this.value;">
						<option value="">Ver todas las categorías</option>
						<?php 
						foreach ( $parent_categories as $cat ) : 
							// Marcar como seleccionada la categoría actual o su padre
							$is_selected = ( $cat->term_id == $current_category_id ) || 
							               ( $cat->term_id == $current_category->parent );
							?>
							<option value="<?php echo esc_url( get_term_link( $cat ) ); ?>"
							        <?php selected( $is_selected, true ); ?>>
								<?php echo esc_html( $cat->name ); ?> 
								(<?php echo $cat->count; ?> productos)
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<?php 
			/**
			 * DROPDOWN 2: Subcategorías
			 * Este dropdown solo se muestra si:
			 * 1. La categoría actual tiene subcategorías, O
			 * 2. La categoría actual es una subcategoría (mostrar sus hermanas)
			 */
			$categories_to_show = ! empty( $subcategories ) ? $subcategories : $sibling_categories;
			
			if ( ! empty( $categories_to_show ) ) : 
			?>
				<div class="mantis-filter-group">
					<label for="mantis-subcategory-select" class="mantis-filter-group__label">
						<?php echo ! empty( $subcategories ) ? 'Subcategoría:' : 'Filtrar por:'; ?>
					</label>
					<select id="mantis-subcategory-select" 
					        class="mantis-filter-select"
					        onchange="if(this.value) window.location.href = this.value;">
						<?php 
						// Si estamos viendo subcategorías de la actual, mostrar opción de "todas"
						if ( ! empty( $subcategories ) ) : ?>
							<option value="<?php echo esc_url( get_term_link( $current_category ) ); ?>">
								Todos los productos
							</option>
						<?php endif; ?>
						
						<?php foreach ( $categories_to_show as $subcat ) : ?>
							<option value="<?php echo esc_url( get_term_link( $subcat ) ); ?>"
							        <?php selected( $subcat->term_id, $current_category_id ); ?>>
								<?php echo esc_html( $subcat->name ); ?> 
								(<?php echo $subcat->count; ?> productos)
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<?php 
			/**
			 * Botón opcional para ver todos los productos sin categorizar
			 * Lo comentamos por ahora, pero lo dejamos por si lo querés usar
			 */
			/*
			<div class="mantis-filter-group">
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" 
				   class="mantis-button mantis-button--secondary">
					Ver todos los productos
				</a>
			</div>
			*/
			?>

		</div>
	</div>

	<?php endif; // Fin de los filtros ?>

	<?php if ( woocommerce_product_loop() ) : ?>

		<div class="mantis-shop-controls">
			<?php
			/**
			 * Hook: woocommerce_before_shop_loop.
			 * Muestra el contador de resultados y el selector de ordenamiento
			 */
			do_action( 'woocommerce_before_shop_loop' );
			?>
		</div>

		<!-- Grid de productos de la categoría -->
		<div class="mantis-products-grid">
			<?php
			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();
					
					/**
					 * Hook: woocommerce_shop_loop.
					 */
					do_action( 'woocommerce_shop_loop' );

					/**
					 * Cargar nuestro template de producto customizado
					 */
					wc_get_template_part( 'content', 'product' );
				}
			}
			?>
		</div>

		<div class="mantis-shop-footer">
			<?php
			/**
			 * Hook: woocommerce_after_shop_loop.
			 * Muestra la paginación
			 */
			do_action( 'woocommerce_after_shop_loop' );
			?>
		</div>

	<?php else : ?>

		<div class="mantis-no-products-wrapper">
			<?php
			/**
			 * Hook: woocommerce_no_products_found.
			 * Muestra el mensaje cuando no hay productos
			 */
			do_action( 'woocommerce_no_products_found' );
			?>
		</div>

	<?php endif; ?>

</div><!-- .mantis-shop-wrapper -->

<?php

/**
 * Hook: woocommerce_after_main_content.
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );