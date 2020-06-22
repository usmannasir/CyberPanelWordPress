<?php
$levels = pmpro_getAllLevels( true, false );
?>
<div class="aligncenter">
<table class="aligncenter">
    <thead>
    <tr>
        <th>Membership Level</th>
        <th>Price</th>
        <th>Hosting Package</th>
    </tr>
    </thead>
    <tbody>
	<?php
	foreach ( $levels as $level ) {
		?>

        <tr>
            <td><?php echo $level->name ?></td>
            <td><?php echo $level->initial_payment ?></td>
            <td><select name="package" id="hosting_package"></select></td>
        </tr>


		<?php
	}
	?>
    </tbody>
</table>
</div>