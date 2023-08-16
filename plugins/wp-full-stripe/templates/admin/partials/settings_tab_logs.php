<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2018.12.06.
 * Time: 12:23
 */

$logsTable = new WPFS_Log_Table();
$logsTable->prepare_items();

?>
<div id="logs-tab">
	<h2>
		<img src="<?php echo MM_WPFS_Assets::images( 'loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>" class="showLoading"/>
	</h2>
	<form method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
		<label for="wpfs-log-module"><?php esc_html_e( 'Module: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
		<select id="wpfs-log-module" name="module">
			<option value="" <?php echo ! isset( $_REQUEST['module'] ) || $_REQUEST['module'] == '' ? 'selected' : ''; ?>><?php esc_html_e( 'All', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
			<?php
			foreach ( MM_WPFS_LoggerService::getModules() as $module ) {
				$option_row = '<option';
				$option_row .= ' value="' . esc_attr( $module ) . '"';
				if ( isset( $_REQUEST['module'] ) ) {
					if ( $module === $_REQUEST['module'] ) {
						$option_row .= ' selected="selected"';
					}
				}
				$option_row .= '>';
				$option_row .= esc_html( $module );
				$option_row .= '</option>';
				echo $option_row;
			}
			?>
		</select>
		<label for="wpfs-log-level"><?php esc_html_e( 'Level: ', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></label>
		<select id="wpfs-log-level" name="level">
			<option value="" <?php echo ! isset( $_REQUEST['level'] ) || $_REQUEST['level'] == '' ? 'selected' : ''; ?>><?php esc_html_e( 'All', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></option>
			<?php
			foreach ( MM_WPFS_LoggerService::getLevels() as $level ) {
				$option_row = '<option';
				$option_row .= ' value="' . esc_attr( $level ) . '"';
				if ( isset( $_REQUEST['level'] ) ) {
					if ( $level === $_REQUEST['level'] ) {
						$option_row .= ' selected="selected"';
					}
				}
				$option_row .= '>';
				$option_row .= esc_html( $level );
				$option_row .= '</option>';
				echo $option_row;
			}
			?>
		</select>
		<span class="wpfs-search-actions">
			<button class="button button-primary"><?php esc_html_e( 'Search', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></button> <?php esc_html_e( 'or', MM_WPFS::L10N_DOMAIN_ADMIN ); ?>
			<a href="<?php echo admin_url( 'admin.php?page=fullstripe-logs' ); ?>"><?php esc_html_e( 'Reset', MM_WPFS::L10N_DOMAIN_ADMIN ); ?></a>
		</span>
		<?php
		/** @var WPFS_Log_Table $logsTable */
		$logsTable->display();
		?>
	</form>
</div>
