<?php
/**
 * Custom archive product template for Mantis
 * Template con loop personalizado por categorías
 *
 * @package Mantis-Astra-WooCommerce
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 * Genera el wrapper del contenido y breadcrumbs
 */
do_action( 'woocommerce_before_main_content' );

?>

<div class="mantis-shop-wrapper">

	<?php
	/**
	 * Detectar si estamos en la página principal de la tienda
	 * O si estamos en una categoría/búsqueda específica
	 */
	$is_main_shop = is_shop() && ! is_search() && ! is_filtered();
	
	/**
	 * CASO 1: Página principal de la tienda
	 * Mostramos el loop personalizado por categorías
	 */
	if ( $is_main_shop ) :
	?>
	
		<!-- Hero section de la tienda -->
		<div class="mantis-shop-hero">
			<div class="mantis-shop-hero__content">
				<h1 class="mantis-shop-hero__title">
					<?php woocommerce_page_title(); ?>
				</h1>
				<?php if ( term_description() ) : ?>
					<div class="mantis-shop-hero__description">
						<?php echo term_description(); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php
		/**
		 * Traer todas las categorías principales de productos
		 * (Las que no tienen parent, o sea las de nivel superior)
		 */
		$parent_categories = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,  // Solo mostrar categorías que tengan productos
			'parent'     => 0,     // Solo las categorías padre (sin parent)
			'orderby'    => 'menu_order', // Respetar el orden que configuraste en WP Admin
		) );

		if ( ! empty( $parent_categories ) && ! is_wp_error( $parent_categories ) ) :
			
			/**
			 * LOOP PRINCIPAL: Iterar sobre cada categoría principal
			 */
			foreach ( $parent_categories as $category ) :
				
				// URL de la categoría (para el botón "Ver más")
				$category_link = get_term_link( $category );
				
				// Imagen de la categoría si existe
				$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
				$category_image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';
			?>

			<!-- Sección de categoría individual -->
			<section class="mantis-category-section">
				
				<!-- Header de la categoría con fondo morado -->
				<div class="mantis-category-header">
					<div class="mantis-category-header__content">
						<h2 class="mantis-category-header__title">
							<?php echo esc_html( $category->name ); ?>
						</h2>
						
						<?php if ( $category->description ) : ?>
							<p class="mantis-category-header__description">
								<?php echo esc_html( $category->description ); ?>
							</p>
						<?php endif; ?>

						<?php
						/**
						 * Traer subcategorías si existen
						 */
						$subcategories = get_terms( array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => true,
							'parent'     => $category->term_id, // Hijas de esta categoría
							'orderby'    => 'menu_order',
						) );

						if ( ! empty( $subcategories ) && ! is_wp_error( $subcategories ) ) :
						?>
							<div class="mantis-subcategories">
								<h3 class="mantis-subcategories__title">Subcategorías:</h3>
								<div class="mantis-subcategories__grid">
									<?php foreach ( $subcategories as $subcat ) : 
										$subcat_link = get_term_link( $subcat );
										$subcat_thumb_id = get_term_meta( $subcat->term_id, 'thumbnail_id', true );
										$subcat_image = $subcat_thumb_id ? wp_get_attachment_url( $subcat_thumb_id ) : '';
									?>
										<a href="<?php echo esc_url( $subcat_link ); ?>" class="mantis-subcategory-card">
											<?php if ( $subcat_image ) : ?>
												<div class="mantis-subcategory-card__image">
													<img src="<?php echo esc_url( $subcat_image ); ?>" 
													     alt="<?php echo esc_attr( $subcat->name ); ?>"
													     loading="lazy">
												</div>
											<?php endif; ?>
											<h4 class="mantis-subcategory-card__name">
												<?php echo esc_html( $subcat->name ); ?>
											</h4>
										</a>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<?php
				/**
				 * Traer los primeros 8 productos de esta categoría
				 * Usando WP_Query para hacer una consulta personalizada
				 */
				$products_query = new WP_Query( array(
					'post_type'      => 'product',
					'posts_per_page' => 8, // Mostrar 8 productos por categoría
					'post_status'    => 'publish',
					'tax_query'      => array(
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'term_id',
							'terms'    => $category->term_id,
						),
					),
					// Ordenar por los más recientes o más vendidos
					'orderby'        => 'date', // Podés cambiar a 'meta_value_num' para más vendidos
					'order'          => 'DESC',
				) );

				if ( $products_query->have_posts() ) :
				?>

					<!-- Grid de productos de esta categoría -->
					<div class="mantis-products-grid mantis-products-grid--category">
						<?php
						while ( $products_query->have_posts() ) : 
							$products_query->the_post();
							
							/**
							 * IMPORTANTE: Necesitamos setear el global $product
							 * para que el template content-product.php funcione correctamente
							 */
							global $product;
							$product = wc_get_product( get_the_ID() );
							
							// Cargar nuestro template de producto
							wc_get_template_part( 'content', 'product' );
							
						endwhile;
						
						// Resetear el query para no afectar otros loops
						wp_reset_postdata();
						?>
					</div>

					<!-- Botón para ver todos los productos de la categoría -->
					<div class="mantis-category-footer">
						<a href="<?php echo esc_url( $category_link ); ?>" 
						   class="mantis-button mantis-button--primary mantis-button--large">
							Ver todos los productos de <?php echo esc_html( $category->name ); ?>
						</a>
					</div>

				<?php 
				else : 
					echo '<p class="mantis-no-products">No hay productos en esta categoría aún.</p>';
				endif;
				?>

			</section><!-- .mantis-category-section -->

			<?php 
			endforeach; // Fin del loop de categorías
			
		else : 
			echo '<p>No hay categorías configuradas todavía.</p>';
		endif;
		?>

	<?php
	/**
	 * CASO 2: Estamos en una categoría específica, búsqueda, o filtro
	 * Usamos el loop normal de WooCommerce
	 */
	else :
	?>

		<?php
		/**
		 * Hook: woocommerce_shop_loop_header.
		 * Muestra el header de la categoría actual
		 */
		do_action( 'woocommerce_shop_loop_header' );
		?>

		<?php if ( woocommerce_product_loop() ) : ?>

			<div class="mantis-shop-controls">
				<?php
				/**
				 * Hook: woocommerce_before_shop_loop.
				 * Contador de resultados y selector de ordenamiento
				 */
				do_action( 'woocommerce_before_shop_loop' );
				?>
			</div>

			<!-- Grid normal de productos -->
			<div class="mantis-products-grid">
				<?php
				if ( wc_get_loop_prop( 'total' ) ) {
					while ( have_posts() ) {
						the_post();
						do_action( 'woocommerce_shop_loop' );
						wc_get_template_part( 'content', 'product' );
					}
				}
				?>
			</div>

			<div class="mantis-shop-footer">
				<?php
				/**
				 * Hook: woocommerce_after_shop_loop.
				 * Paginación
				 */
				do_action( 'woocommerce_after_shop_loop' );
				?>
			</div>

		<?php else : ?>

			<?php
			/**
			 * Hook: woocommerce_no_products_found.
			 * Mensaje cuando no hay productos
			 */
			do_action( 'woocommerce_no_products_found' );
			?>

		<?php endif; ?>

	<?php endif; // Fin del condicional is_main_shop ?>

</div><!-- .mantis-shop-wrapper -->

<?php

/**
 * Hook: woocommerce_after_main_content.
 * Cierra el wrapper del contenido
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 * Muestra el sidebar de WooCommerce
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );