<?php
defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(dirname(__FILE__))) . '/local/ucla/lib.php');

class block_ucla_subject_links extends block_base {
    
    static function get_location() {
        global $CFG;
        return $CFG->dirroot . '/blocks/ucla_subject_links/content/';
    }
    /**
     * Called by moodle
     */
    public function init() {
        // Initialize name and title
        $this->title = get_string('title', 'block_ucla_subject_links');
    }

    /**
     * Called by moodle
     */
    public function get_content() {
        global $COURSE;

        return $this->content;
    }

    /**
     * Use UCLA Course menu block hook
     */
    public static function get_navigation_nodes($course) {
        $subjname = self::get_subject_areas($course);
        $nodes = array();
        if (!empty($subjname)) {
            foreach ($subjname as $sub) {
                $url = new moodle_url('/blocks/ucla_subject_links/view.php',array('course_id' => $course->id, 'subj_area' => $sub));
                $nodes[] = navigation_node::create(get_string('link_text', 'block_ucla_subject_links', $sub), $url);
            }
        }
        return $nodes;
    }

    /**
     * Called by moodle
     */
    public function applicable_formats() {
        return array(
            'site-index' => false,
            'course-view' => false,
            'my' => false,
            'not-really-applicable' => true
        );
        // hack to make sure the block can never be instantiated
    }

    /**
     * Called by moodle
     */
    public function instance_allow_multiple() {
        return false; //disables multiple blocks per page
    }

    /**
     * Called by moodle
     */
    public function instance_allow_config() {
        return false; // disables instance configuration
    }

    public static function get_subject_areas($course) {
        $subjname = array();
        $courseinfo = ucla_get_course_info($course->id);
        if (!empty($courseinfo)) {
            foreach ($courseinfo as $cinfo) {
                $subject = $cinfo->subj_area;
                $subject = preg_replace('/-\s/', '', $subject);
                $subject = strtoupper($subject); 
                if (!in_array($subject, $subjname) && 
                        file_exists(self::get_location().$subject.'/index.htm')) {
                    $subjname[] = $subject;
                }   
            }
        }
        return $subjname; 
    }
    
    public static function subject_exist($course, $subjarea) {
        $subjname = self::get_subject_areas($course);
        return in_array($subjarea, $subjname);
    }
}