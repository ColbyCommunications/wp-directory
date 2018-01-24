<?php

use PHPUnit\Framework\TestCase;

use ColbyComms\WpDirectory\Shortcodes\DepartmentShortcode;

class DepartmentShortcodeTest extends TestCase {
    public function test_render_person() {
        $data = [];
        $data['name'] = 'Jane Faculty';
		$data['title'] = 'Associate Professor of Snow';
		$data['phone'] = '207-509-5555';
		$data['bio'] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at porta augue. Mauris condimentum efficitur neque sit amet aliquam. Vivamus a consequat eros. Cras sed pellentesque elit. Aliquam tempor augue at ullamcorper tincidunt. Duis quis turpis mi. Phasellus sed odio eu ante rhoncus sagittis ut a nulla. Nam sit amet rhoncus nibh.';
        $data['email'] = 'jane.faculty';
        

    }

}