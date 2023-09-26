<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wordpress.org/
 * @since      1.0.0
 *
 * @package    Remove_Taxonmy_Slug
 * @subpackage Remove_Taxonmy_Slug/admin/partials
 */

$listing_data 	= Remove_Taxonmy_Slug_Admin::retaxslug_get_list_of_taxonomy();
$selected_data 	= Remove_Taxonmy_Slug_Admin::retaxslug_save_list_of_taxonomy();

echo '<div class="postbox-header">
		<h2 class="hndle ui-sortable-handle">
			<span>' . __('Basic settings', 'remove-taxonmy-slug') . '</span>
		</h2>
	</div>
	<div class="inside">
		<div class="main">
			<form method = "post">
				<table class="form-table cptui-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="description">
									' . __( 'List of taxonomy', 'remove-taxonmy-slug' ) . '
								</label>
							</th>
						 <td>';
						foreach ( $listing_data as $key => $value ) {
							$checked = '';
							if( ! empty( $selected_data )
								&& in_array( $value, $selected_data) 
							){
								$checked = "checked";
							}
							echo '<input
									type="checkbox"
									name="remve_slug_selected_taxonomy[]"
									'. $checked .'
									value =" ' . $key . ' "
								/>'.$value.'<br />';
						}
				echo 	'</td>
						</tr>
						<tr>
							<th>
								<input type="submit" class="button-primary" name="remove_taxonomy_submit" value="' . __( 'Save Changes', 'remove-taxonmy-slug' ) . '">
								' . wp_nonce_field('remove_taxnomy_slug_nonce', 'remove_taxnomy_slug_nonce') . '
							</th>
							<td></td>
						</tr>
						<tr>
							<td colspan="2">' . __('You can make the donation if you like the plugin click the <a href ="https://paypal.me/imobsphere?locale.x=en_GB" target="_blank">donation</a>.', 'remove-taxonmy-slug') . '</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>';

?>