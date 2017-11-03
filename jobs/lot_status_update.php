<?php
include "../assets/utils/fwdbutil.php";
$dbh = setupPDO();
$updt_qry = "update application_lots
				set lot_status = 'COMPLETE'
				where lot_status not in ('ON_BALANCE_HOLD', 'NEW', 'REJECTED')
					and application_lot_id in (select lot_id
												from lot_applications 
												group by lot_id having sum(case when application_status in ('Approved', 'Rejected') then 0 else 1 end)  = 0
											)
			";
echo "Update complete.", "\n";
try {
	runUpdate($dbh, $updt_qry, array());
} catch (PDOException $ex) {
	echo "Error in update..", "\n";
	echo "error: ", $ex->getMessage();
	echo "\n";
	echo "query: ", $updt_qry;
}
?>
