<?php
require __DIR__ . '/vendor/autoload.php';

function updateSheet($values, $row){
	$client = new Google_Client();
	
	$client->setApplicationName('Consentium Sheet');
	$client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
	$client->setAccessType('online');

	$client->setAuthConfig(__DIR__ . '/consentium_account.json');

	$sheets = new Google_Service_Sheets($client);

	$spreadsheetId = "1wIi8mGsQ_2ew2zkf1HyV3Gtt8-0kFQCTjY16b85Ptdo";

	$updateRange = 'emails!A'.$row.':H'.$row;
	$updateBody = new Google_Service_Sheets_ValueRange([
	    'majorDimension' => 'ROWS',
	    'values' => [$values],
	]);

	$sheets->spreadsheets_values->update(
	    $spreadsheetId,
	    $updateRange,
	    $updateBody,
	    ['valueInputOption' => 'USER_ENTERED']
	);	
}
?>