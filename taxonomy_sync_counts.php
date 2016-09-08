<?php
/*
	Plugin Name: Taxonomy Count Sync
	Plugin URI: 
	Description: Re-sync taxonomy post counts
	Version: 1.0
	Author: Jem Turner
	Author URI: http://jemturner.co.uk
	License: GPL2

*/

class jt_taxonomy_sync_count {

	function __construct( ) {
		add_action( 'admin_menu', array( $this, 'register_admin_menus' ) );
	}

	function register_admin_menus() {
		add_management_page( 'Resync Counts', 'Resync Counts', 'manage_options', 'taxonomy-sync-counts', array( $this, 'taxonomy_sync_counts_page' ) );
	}

	function taxonomy_sync_counts_page( ) {
		global $wpdb;
?>
		<div class="wrap">
			<h1>Synchronise Taxonomy Post Counts</h2>
			
<?php
			if ( $_SERVER['REQUEST_METHOD'] == "POST" ) {
				$ze_query = $wpdb->query( 
					"UPDATE $wpdb->term_taxonomy SET count = (
						SELECT COUNT(*) FROM $wpdb->term_relationships rel 
						LEFT JOIN $wpdb->posts po ON (po.ID = rel.object_id) 
					WHERE 
						rel.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id 
					AND 
						$wpdb->term_taxonomy.taxonomy NOT IN ('link_category')
					AND 
						po.post_status IN ('publish', 'future')
					)"
				);
					
				if ( $ze_query !== false ) {
?>
					<div id="message" class="updated notice notice-success is-dismissible">
						<p>Taxonomy post counts successfully re-synchronised.</p>
					</div>
<?php
				} else {
?>
					<div id="message" class="notice notice-error is-dismissible">
						<p>Abandon ship!<br><?php $wpdb->print_error(); ?></p>
					</div>
<?php
				}
			}
?>

			<form method="post">
			<p><input type="submit" value="Sync now"></p>
			</form>
			
		</div>
<?php
	}
}
$jt_taxonomy_sync_count = new jt_taxonomy_sync_count();