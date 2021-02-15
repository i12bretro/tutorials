<?php
	try{
		$conn = new MongoDB\Driver\Manager("mongodb://localhost:27017");
	} catch (MongoDBDriverExceptionException $e) {
		echo 'Failed to connect to MongoDB, is the service intalled and running?<br /><br />';
		echo $e->getMessage();
		exit();
	}

	$states = array(
		array('State'=>'Alabama', 'Capital'=> 'Montgomery'),
		array('State'=>'Alaska', 'Capital'=> 'Juneau'),
		array('State'=>'Arizona', 'Capital'=> 'Phoenix'),
		array('State'=>'Arkansas', 'Capital'=> 'Little Rock'),
		array('State'=>'California', 'Capital'=> 'Sacramento'),
		array('State'=>'Colorado', 'Capital'=> 'Denver'),
		array('State'=>'Connecticut', 'Capital'=> 'Hartford'),
		array('State'=>'Delaware', 'Capital'=> 'Dover'),
		array('State'=>'Florida', 'Capital'=> 'Tallahassee'),
		array('State'=>'Georgia', 'Capital'=> 'Atlanta'),
		array('State'=>'Hawaii', 'Capital'=> 'Honolulu'),
		array('State'=>'Idaho', 'Capital'=> 'Boise'),
		array('State'=>'Illinois', 'Capital'=> 'Springfield'),
		array('State'=>'Indiana', 'Capital'=> 'Indianapolis'),
		array('State'=>'Iowa', 'Capital'=> 'Des Moines'),
		array('State'=>'Kansas', 'Capital'=> 'Topeka'),
		array('State'=>'Kentucky', 'Capital'=> 'Frankfort'),
		array('State'=>'Louisiana', 'Capital'=> 'Baton Rouge'),
		array('State'=>'Maine', 'Capital'=> 'Augusta'),
		array('State'=>'Maryland', 'Capital'=> 'Annapolis'),
		array('State'=>'Massachusetts', 'Capital'=> 'Boston'),
		array('State'=>'Michigan', 'Capital'=> 'Lansing'),
		array('State'=>'Minnesota', 'Capital'=> 'Saint Paul'),
		array('State'=>'Mississippi', 'Capital'=> 'Jackson'),
		array('State'=>'Missouri', 'Capital'=> 'Jefferson City'),
		array('State'=>'Montana', 'Capital'=> 'Helena'),
		array('State'=>'Nebraska', 'Capital'=> 'Lincoln'),
		array('State'=>'Nevada', 'Capital'=> 'Carson City'),
		array('State'=>'New Hampshire', 'Capital'=> 'Concord'),
		array('State'=>'New Jersey', 'Capital'=> 'Trenton'),
		array('State'=>'New Mexico', 'Capital'=> 'Santa Fe'),
		array('State'=>'New York', 'Capital'=> 'Albany'),
		array('State'=>'North Carolina', 'Capital'=> 'Raleigh'),
		array('State'=>'North Dakota', 'Capital'=> 'Bismarck'),
		array('State'=>'Ohio', 'Capital'=> 'Columbus'),
		array('State'=>'Oklahoma', 'Capital'=> 'Oklahoma City'),
		array('State'=>'Oregon', 'Capital'=> 'Salem'),
		array('State'=>'Pennsylvania', 'Capital'=> 'Harrisburg'),
		array('State'=>'Rhode Island', 'Capital'=> 'Providence'),
		array('State'=>'South Carolina', 'Capital'=> 'Columbia'),
		array('State'=>'South Dakota', 'Capital'=> 'Pierre'),
		array('State'=>'Tennessee', 'Capital'=> 'Nashville'),
		array('State'=>'Texas', 'Capital'=> 'Austin'),
		array('State'=>'Utah', 'Capital'=> 'Salt Lake City'),
		array('State'=>'Vermont', 'Capital'=> 'Montpelier'),
		array('State'=>'Virginia', 'Capital'=> 'Richmond'),
		array('State'=>'Washington', 'Capital'=> 'Olympia'),
		array('State'=>'West Virginia', 'Capital'=> 'Charleston'),
		array('State'=>'Wisconsin', 'Capital'=> 'Madison'),
		array('State'=>'Wyoming', 'Capital'=> 'Cheyenne')
	);

	$cmd = new MongoDB\Driver\Command(['listDatabases' => 1]);
	try {
		$result = $conn->executeCommand('admin', $cmd);
		$dbArray = $result->toArray()[0];
	} catch(MongoDB\Driver\Exception $e) {
		echo $e->getMessage().'<br />';
		exit;
	}

	if(!array_search('phpDemo', array_column($dbArray->databases, 'name'))){
		echo 'phpDemo database doesn\'t exist, creating it<br />';
		foreach($states AS $state){
			$row = new MongoDB\Driver\BulkWrite();
			$row->insert($state);
			$conn->executeBulkWrite('phpDemo.state', $row);
			echo '&emsp;Added '.$state['State'].'<br />';
		}
	} 

	$query = new MongoDB\Driver\Query([],[]);
	$result = $conn->executeQuery('phpDemo.state', $query);

	if($result){
		echo '<h3>Reading data from MongoDB</h3>'.
		'<table width="500" align="center">'.
			'<thead>'.
				'<tr><th>_id</th><th>State</th><th>Capital</th></tr>'.
			'</thead>'.
			'<tbody>';
				foreach ($result as $rs){
					echo '<tr><td>'.$rs->{'_id'}.'</td><td>'.$rs->State.'</td><td>'.$rs->Capital.'</td></tr>';
				}
			echo '</tbody>'.
		'</table>';
		unset($query, $result);
		
		$query = new MongoDB\Driver\Query(['State'=> 'Massachusetts'],[]);
		$result = $conn->executeQuery('phpDemo.state', $query);
		if($result){
			$rs = $result->toArray()[0];
			echo '<h3>Reading specific state from MongoDB</h3>'.
			'The capital of '.$rs->State.' is '.$rs->Capital.'<br /><br />_id: '.$rs->{'_id'};
		}
	}
?>