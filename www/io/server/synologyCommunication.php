<?php
namespace Mediaio;

class synologyAPICommunicationManager
{
	private $credentials = null;
	private $sid = null;
	function __construct()
	{
		//Read from the dbCredentials file
		$file_path = $_SERVER["DOCUMENT_ROOT"] . '/server/dbCredentials.json';
		$json_data = file_get_contents($file_path);
		$this->credentials = json_decode($json_data, true);
	}
	function obtainSID()
	{
		$NAS_username = $this->credentials['NAS_username'];
		$NAS_password = $this->credentials['NAS_password'];
		$NAS_domain = $this->credentials['NAS_domain'];

		//Make a request to the NAS to obtain a session ID
		$curl = curl_init();
		//Encode the username and password to be used in the request
		$NAS_username = urlencode($NAS_username);
		$NAS_password = urlencode($NAS_password);

		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL => "https://" . $NAS_domain . "/webapi/auth.cgi?api=SYNO.API.Auth&version=3&method=login&account=" . $NAS_username . "&passwd=" . $NAS_password . "&session=FileStation&format=sid",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
			)
		);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		//echo response and error


		// echo $response;
		// echo $err;

		$response = json_decode($response, true);

		//get sid from response
		$this->sid = $response['data']['sid'];

	}

	/**
	 * @param $url - the url to send the request to
	 * @param $paramters - Dictionary of parameters to send with the request
	 * @param $method - GET or POST
	 */
	function runRequest($url, $paramters, $method)
	{

		//$NAS_username = $this->credentials['NAS_username'];
		//$NAS_password = $this->credentials['NAS_password'];
		$NAS_domain = $this->credentials['NAS_domain'];
		$curl = curl_init();
		//Encode the username and password to be used in the request
		//$NAS_username = urlencode($NAS_username);
		//$NAS_password = urlencode($NAS_password);



		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL => "https://" . $NAS_domain . $url . "&_sid=" . $this->sid,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => $method,
			)
		);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		//echo "https://".$NAS_domain.$url."&_sid=".$this->sid."\n";


		return $response;
		//echo $err;
	}

	function downloadReq($path)
	{
		return 'https://' . $this->credentials['NAS_domain'] . '/webapi/entry.cgi?api=SYNO.FileStation.Download&version=2&method=download&path=' . urlencode($path) . '&mode=download&_sid=' . $this->sid . '';
	}

	function logout()
	{
		$NAS_domain = $this->credentials['NAS_domain'];
		$curl = curl_init();
		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL => "https://" . $NAS_domain . "/webapi/auth.cgi?api=SYNO.API.Auth&version=3&method=logout&session=FileStation&_sid=" . $this->sid,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
			)
		);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		$this->sid = null;

		//echo $response;
		//echo $err;
	}

	function getSid()
	{
		return $this->sid;
	}

}
//$api = new synologyAPICommunicationManager();
if (isset($_GET['mode'])) {

	if (($_GET['mode']) == 'obtainAPIKey') {
		$api->obtainSID();
		exit();
	} elseif (($_GET['mode']) == 'getRootFolderData') {
		if ($api->getSid() == null) {
			$api->obtainSID();
		}
		//$api->runRequest("/webapi/entry.cgi?api=SYNO.FileStation.List&version=2&method=list&additional=%5B%22owner%22%2C%22time%22%2C%22perm%22%2C%22type%22%5D&folder_path=%2F" . urlencode($_GET['path']), array(), "GET");
	} elseif (($_GET['mode'] == 'share')) {
		if ($api->getSid() == null) {
			$api->obtainSID();
		}
		if (isset($_GET['path']) && isset($_GET['expire_date'])) {
			$api->runRequest("/webapi/entry.cgi?api=SYNO.FileStation.Sharing&version=3&method=create&path=/" . urlencode($_GET['path']) . "&date_expired=" . urlencode($_GET['expire_date']), array(), "GET");
		} else {
			return 503;
		}

	} elseif (($_GET['mode'] == 'logout')) {
		$api->logout();
		exit();
	}
}

?>