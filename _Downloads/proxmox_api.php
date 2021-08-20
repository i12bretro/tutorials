<?php 
	session_start();
	if(isset($_GET['logon'])){
		session_reset();
	}
	
	// TRY TO AUTHENTICATE
	if(isset($_GET['auth'])){
		$_SESSION['ProxmoxApiURL'] = (substr($_POST['ProxmoxApiURL'],-1) !== '/') ? $_POST['ProxmoxApiURL'].'/' : $_POST['ProxmoxApiURL'];
		$_SESSION['username'] = $_POST['username'];
		$curlOptions = array(
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'username='.$_POST['username'].'&password='.$_POST['password'],
			CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded')
		);
		$r = curl_call($_SESSION['ProxmoxApiURL'].'access/ticket', $curlOptions);
		if(empty($r['error']) && !empty($r['response'])){
			$data = @json_decode($r['response'], true);
			if($data !== false){
				$_SESSION['ticket'] = $data['data']['ticket'];
				$_SESSION['token'] = $data['data']['CSRFPreventionToken'];
			}
		}
	}
	
	// LOGON FORM
	if(empty($_SESSION['ticket']) || empty($_SESSION['ProxmoxApiURL']) || isset($_GET['logon'])){
		echo '<form method="post" action="?auth">'.
			'<label for="ProxmoxApiURL">API URL: </label><input type="text" name="ProxmoxApiURL" id="ProxmoxApiURL" value="'.((!empty($_POST['ProxmoxApiURL'])) ? $_POST['ProxmoxApiURL'] : '').'" /><br />'.
			'<label for="username">Username: </label><input type="text" name="username" id="username" value="'.((!empty($_POST['username'])) ? $_POST['username'] : '').'" /><br />'.
			'<label for="password">Password: </label><input type="password" name="password" id="password" /><br />'.
			'<input type="submit" value="Log In" title="Log In" />'.
		'</form>';
	} else {	
		// SHOW MENU
		echo '<h3>Welcome '.$_SESSION['username'].'! <a href="?logon">Logout</a></h3>'.
		'Pick an option: '.
		'<ul>'.
			'<li><a href="?call=version">Version</a></li>'.
			'<li><a href="?call=nodes">Nodes</a></li>'.
			'<li><a href="?call=status">status</a></li>'.
		'</ul>';
		
		if(!empty($_GET['call'])){
			if($_GET['call'] == 'status' && !isset($_GET['node'])){
				echo '<form method="get">'.
					'<input type="hidden" name="call" value="'.$_GET['call'].'" />'.
					'<label for="node">Node: </label><input type="text" name="node" /> '.
					'<input type="submit" value="Go" />'.
				'</form>';
			} else {
				$curlOptions = array(
					CURLOPT_HTTPHEADER => array(
						'CSRFPreventionToken: '.$_SESSION['token'],
						'Cookie: PVEAuthCookie='.$_SESSION['ticket']
					)
				);
				switch($_GET['call']){
					case 'version':
						$URL = $_SESSION['ProxmoxApiURL'].'version';
						break;
					
					case 'nodes':
						$URL = $_SESSION['ProxmoxApiURL'].'nodes';
						break;
						
					case 'status':
						$URL = $_SESSION['ProxmoxApiURL'].'nodes/'.$_GET['node'].'/status';
						break;
				}
					
				$r = curl_call($URL, $curlOptions);
				if(empty($r['error']) && !empty($r['response'])){
					$data = @json_decode($r['response'], true);
					if($data !== false){
						echo 'API Response:<br />'.
						'<pre>';
							print_r($data);
						echo '</pre>';
					}
				} else {
					echo $r['header'];
				}
			}
		}
	}
	
	
	function curl_call($url, $options = null){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:75.0) Gecko/20100101 Firefox/75.0');
		curl_setopt($curl, CURLOPT_NOBODY, false);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_HEADER_OUT, true);

		if(is_array($options)){
			foreach($options AS $k=>$v){
				curl_setopt($curl, $k, $v);
			}
		}
		
		$response = curl_exec($curl);
		$error = curl_error($curl);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$responseBody = substr($response, $header_size);
		return array('error'=>$error, 'header'=>$header, 'response'=>$responseBody);
	}
?>