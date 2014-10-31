<?php



class SPF_View {

	private $mobile;

	public function __construct() {
		require SPF_CLASS . 'Extern/Mobile_Detect.php';
		
		$this->mobile = new Mobile_Detect();
	}
	
	public function getDevice() {
		return $this->mobile;
	}
}