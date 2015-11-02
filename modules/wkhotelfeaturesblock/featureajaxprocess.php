<?php
	include_once '../../config/config.inc.php';
	$id_row = $_POST['id_feature_row'];

	if (isset($id_row) && $id_row)
	{
		$result = Db::getInstance()->delete('htl_features_block_data','id='.$id_row);

		if($result)
			die('success');
		else
			die('failed1');
	}
	else
		die('failed2');
?>