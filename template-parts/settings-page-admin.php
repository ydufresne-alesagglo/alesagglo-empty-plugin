<?php
defined('ABSPATH') || die();
if(!is_admin()) die();
$empty = new \AlesAgglo\AlesAggloEmptyPlugin\EmptyClass('WorldPress');
?>
<div class="aep-admin-container">
<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') { ?>
	<div class="updated notice notice-success"><p>Paramètres sauvegardés avec succès.</p></div>
<?php } ?>
	<div class="wrap">
		<form method="POST" action="options.php">
			<?php
				settings_fields('aep_options_group');
				do_settings_sections('aep-settings');
				submit_button();
			?>
		</form>
		<?php echo $empty->hello(); ?>
	</div>
</div>