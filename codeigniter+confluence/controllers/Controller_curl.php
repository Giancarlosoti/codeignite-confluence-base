<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Controller_curl extends CI_Controller {
public function __construct() {
parent::__construct();
}
public function index(){

//  Calling cURL Library
$this->load->library('curl');

//  Setting URL To Fetch Data From
$this->curl->create('http://200.6.115.193/wp/wp-json/dcms-seo-yoast/v1/links/234');

//  To Temporarily Store Data Received From Server
$this->curl->option('buffersize', 10);

//  To support Different Browsers
$this->curl->option('useragent', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8 (.NET CLR 3.5.30729)');

//  To Receive Data Returned From Server
$this->curl->option('returntransfer', 1);

//  To follow The URL Provided For Website
$this->curl->option('followlocation', 1);

//  To Retrieve Server Related Data
$this->curl->option('HEADER', true);

//  To Set Time For Process Timeout
$this->curl->option('connecttimeout', 600);

//  To Execute 'option' Array Into cURL Library & Store Returned Data Into $data
$data = $this->curl->execute();

//  To Display Returned Data
echo 'vcd'$data;
}
}
?>
