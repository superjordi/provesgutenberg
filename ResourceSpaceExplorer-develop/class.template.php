<?php
/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

class RSE_Template {

	/**
	 * Outputs the Backbone template for an item within search results.
	 *
	 * @param string $id  The template ID.
	 * @param string $tab The tab ID.
	 * @return null
	 */
	public function item( $id, $tab ) {
		?>
		<div id="rse-item-<?php echo esc_attr( $tab ); ?>-{{ data.id }}" class="rse-item-area" data-id="{{ data.id }}">
			<div class="rse-item-container clearfix">
				<div class="rse-item-thumb">
					<img src="{{ data.thumbnail }}">
				</div>

				<div class="rse-item-main">
					<div class="rse-item-content">
						{{ data.content }}
					</div>
					<div class="rse-item-date">
						<span class="uploaded-date"><?php _e( 'Uploaded date:', 'rsexplorer' ); ?></span> {{ data.date }}
					</div>
				</div>

			</div>
		</div>

		<a href="#" id="rse-check-{{ data.id }}" data-id="{{ data.id }}" class="check" title="<?php esc_attr_e( 'Deselect', 'resourcespace' ); ?>">
			<div class="media-modal-icon"></div>
		</a>
		<?php
	}

	/**
	 * Outputs the Backbone template for a select item's thumbnail in the footer toolbar.
	 *
	 * @param string $id The template ID.
	 * @return null
	 */
	public function thumbnail( $id ) {
		?>
		<?php
	}


	/**
	 * Outputs the Backbone template for a tab's search fields.
	 *
	 * @param string $id  The template ID.
	 * @param string $tab The tab ID.
	 * @return null
	 */
		public function search( $id, $tab ) {

		switch ( $tab ) {

			case 'hashtag':

				?>
				<form action="#" class="rse-toolbar-container clearfix">
					<input
						type="text"
						name="hashtag"
						value="{{ data.params.hashtag }}"
						class="rse-input-text rse-input-search"
						size="40"
						placeholder="<?php esc_attr_e( 'Enter a Hashtag', 'mexp' ); ?>"
					>
					<input class="button button-large" type="submit" value="<?php esc_attr_e( 'Search', 'mexp') ?>">
					<div class="spinner"></div>
				</form>
				<?php

				break;

			case 'by_user':

				?>
				<form action="#" class="rse-toolbar-container clearfix">
					<input
						type="text"
						name="by_user"
						value="{{ data.params.by_user }}"
						class="rse-input-text rse-input-search"
						size="40"
						placeholder="<?php esc_attr_e( 'Enter a rp Username', 'mexp' ); ?>"
					>
					<input class="button button-large" type="submit" value="<?php esc_attr_e( 'Search', 'mexp') ?>">
					<div class="spinner"></div>
				</form>
				<?php

				break;

			case 'to_user':

				?>
				<form action="#" class="rse-toolbar-container clearfix">
					<input
						type="text"
						name="to_user"
						value="{{ data.params.to_user }}"
						class="rse-input-text rse-input-search"
						size="40"
						placeholder="<?php esc_attr_e( 'Enter a rp Username', 'mexp' ); ?>"
					>
					<input class="button button-large" type="submit" value="<?php esc_attr_e( 'Search', 'mexp') ?>">
					<div class="spinner"></div>
				</form>
				<?php

				break;

			case 'location':

				?>
				<div id="rse_rp_map_canvas"></div>
				<form action="#" class="rse-toolbar-container clearfix">
					<input
						id="<?php echo esc_attr( $id ); ?>-coords"
						type="hidden"
						name="coords"
						value="{{ data.params.location }}"
					>
					<input
						type="text"
						name="q"
						value="{{ data.params.q }}"
						class="rse-input-text rse-input-search"
						size="40"
						placeholder="<?php esc_attr_e( 'Search rp', 'mexp' ); ?>"
					>
					<label for="<?php echo esc_attr( $id ); ?>-name">
						<?php esc_attr_e( 'Location:', 'mexp' ); ?>
					</label>
					<input
						type="text"
						id="<?php echo esc_attr( $id ); ?>-name"
						name="location"
						value="{{ data.params.q }}"
						class="rse-input-text rse-input-search"
						size="40"
						placeholder="<?php esc_attr_e( 'Enter location', 'mexp' ); ?>"
					>
					<select
						id="<?php echo esc_attr( $id ); ?>-radius"
						type="text"
						name="radius"
						class="rse-input-text rse-input-select"
						placeholder="<?php esc_attr_e( 'Search rp', 'mexp' ); ?>"
					>
						<?php foreach ( array( 1, 5, 10, 20, 50, 100, 200 ) as $km ) { ?>
							<option value="<?php echo absint( $km ); ?>"><?php printf( esc_html__( 'Within %skm', 'mexp' ), $km ); ?></option>
						<?php } ?>
					</select>
					<input
						type="submit"
						class="button button-large"
						value="<?php esc_attr_e( 'Search', 'mexp' ); ?>"
					>
					<div class="spinner"></div>
				</form>
				<?php

				break;

			case 'images':
			case 'all':
			default:

				?>
				<form action="#" class="rse-toolbar-container clearfix">
					<input
						type="text"
						name="q"
						value="{{ data.params.q }}"
						class="rse-input-text rse-input-search"
						size="40"
						placeholder="<?php esc_attr_e( 'Search rp', 'mexp' ); ?>"
					>
					<input class="button button-large" type="submit" value="<?php esc_attr_e( 'Search', 'mexp') ?>">
					<div class="spinner"></div>
				</form>
				<?php

				break;

		}

	}

	/**
	 * Outputs the markup needed before a template.
	 *
	 * @param string $id  The template ID.
	 * @param string $tab The tab ID (optional).
	 * @return null
	 */
	final public function before_template( $id, $tab = null ) {
		?>
		<script type="text/html" id="tmpl-<?php echo esc_attr( $id ); ?>">
		<?php
	}

	/**
	 * Outputs the markup needed after a template.
	 *
	 * @param string $id  The template ID.
	 * @param string $tab The tab ID (optional).
	 * @return null
	 */
	final public function after_template( $id, $tab = null ) {
		?>
		</script>
		<?php
	}

}
