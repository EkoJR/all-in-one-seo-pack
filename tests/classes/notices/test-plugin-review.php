<?php

namespace classes\AIOSEOP_Notices\notices {

	include_once AIOSEOP_UNIT_TESTING_DIR . '\base\class-aioseop-notices-testcase.php';
	use AIOSEOP_Notices_TestCase;

	/**
	 * Class Test_Plugin_Review
	 *
	 * @since 2.4.5.1
	 *
	 * @package classes\AIOSEOP_Notices\notices
	 */
	class Test_Plugin_Review extends AIOSEOP_Notices_TestCase {

		/**
		 * Mock Single Notice
		 *
		 * @since 2.4.5.1
		 *
		 * @return array
		 */
		protected function mock_notice() {
			return aioseop_notice_review_plugin();
		}
	}
}
