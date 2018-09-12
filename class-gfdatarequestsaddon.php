<?php

GFForms::include_addon_framework();

class GFDataRequestsAddOn extends GFAddOn {
	protected $_version = UWO_GF_DATA_REQUESTS_ADDON_VERSION;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'datarequestsaddon';
	protected $_path = 'gf-data-requests/data-requests.php';
	protected $_full_path = __FILE__;
	protected $_title = 'UWO Gravity Forms Data Requests Add-On';
    protected $_short_title = 'Data Requests Add-On';
    
    private static $_instance = null;

    /**
	 * Get an instance of this class.
	 *
	 * @return GFDataRequestsAddOn
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFDataRequestsAddOn();
		}
		return self::$_instance;
    }
    
    /**
	 * Handles hooks and loading of language files.
	 */
	public function init() {
		parent::init();
		add_action( 'gform_after_submission', array( $this, 'after_submission' ), 10, 2 );
    }

    // # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------
    
    /**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {
		return array(
			array(
				'title'  => esc_html__( 'Data Requests Add-On Settings', 'datarequestsaddon' ),
				'fields' => array(
					array(
						'name'              => 'jirasite',
						'tooltip'           => esc_html__( 'This is the Jira site endpoint for the issue creation.', 'datarequestsaddon' ),
						'label'             => esc_html__( 'Jira Site', 'datarequestsaddon' ),
						'type'              => 'text',
						'class'             => 'medium',
						'feedback_callback'	=> array( $this, 'is_valid_site_url' ),
                    ),
					array(
						'name'              => 'username',
						'tooltip'           => esc_html__( 'This is the account that the data should pass thru to Jira creating an issue.', 'datarequestsaddon' ),
						'label'             => esc_html__( 'Jira Username', 'datarequestsaddon' ),
						'type'              => 'text',
						'class'             => 'small',
                    ),
                    array(
						'name'              => 'apikey',
						'tooltip'           => esc_html__( 'This is the API key that is associated with the Jira username.', 'datarequestsaddon' ),
						'label'             => esc_html__( 'Jira API Key', 'datarequestsaddon' ),
						'type'              => 'text',
						'class'             => 'medium',
                    ),
                    array(
						'name'              => 'projectkey',
						'tooltip'           => esc_html__( 'This is the project key that is associated with the Data Requests Jira project.', 'datarequestsaddon' ),
						'label'             => esc_html__( 'Project Key', 'datarequestsaddon' ),
						'type'              => 'text',
						'class'             => 'small',
						'feedback_callback' => array( $this, 'is_valid_project_key' ),
					),
					array(
						'name'              => 'projectname',
						'tooltip'           => esc_html__( 'This is the project name that is associated with the Data Requests Jira project.', 'datarequestsaddon' ),
						'label'             => esc_html__( 'Project Name', 'datarequestsaddon' ),
						'type'              => 'text',
						'class'             => 'small',
                    ),
				)
			)
		);
    }
    
    /**
	 * Configures the settings which should be rendered on the Form Settings > Data Requests Add-On tab.
	 *
	 * @return array
	 */
	public function form_settings_fields( $form ) {
		return array(
			array(
				'title'  => esc_html__( 'Data Requests Form Settings', 'datarequestsaddon' ),
				'fields' => array(
					array(
						'label'   => esc_html__( 'Write data requests to Jira', 'datarequestsaddon' ),
						'type'    => 'checkbox',
						'name'    => 'enabled',
						'tooltip' => esc_html__( 'If this is checked, all form submissions will create a new issue in the Data Requests Jira Project.', 'datarequestsaddon' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'Enabled', 'datarequestsaddon' ),
								'name'  => 'enabled',
							),
						),
                    ),
                    array(
						'label'             => esc_html__( 'Issue Summary', 'datarequestsaddon' ),
						'type'              => 'text',
						'name'              => 'issuesummary',
						'merge_tags'     	=> true,
						'tooltip'           => esc_html__( 'This is where you can create what the issue summary looks like.', 'datarequestsaddon' ),
						'class'             => 'medium merge-tag-support mt-position-right',
					),
                    array(
						'label'   		=> esc_html__( 'Issue Description', 'datarequestsaddon' ),
						'type'    		=> 'textarea',
						'name'    		=> 'issuedescription',
						'merge_tags'    => true,
						'tooltip' 		=> esc_html__( 'This is where you can create what the issue description looks like.', 'datarequestsaddon' ),
						'class'   		=> 'medium merge-tag-support mt-position-right',
					),
				),
			),
		);
	}
    
    // # SIMPLE CONDITION EXAMPLE --------------------------------------------------------------------------------------
    
    /**
	 * Performing a call to Jira to publish the issue that was submitted in the form.
	 *
	 * @param array $entry The entry currently being processed.
	 * @param array $form The form currently being processed.
	 */
	public function after_submission( $entry, $form ) {
        $settings = $this->get_form_settings( $form );
        if ( isset( $settings['enabled'] ) && true == $settings['enabled'] ) { // If enabled on form, proceed
			// Getting the Jira settings
			$jirasite   = $this->get_plugin_setting( 'jirasite' );
            $username   = $this->get_plugin_setting( 'username' );
            $apikey   = $this->get_plugin_setting( 'apikey' );
			$projectkey   = $this->get_plugin_setting( 'projectkey' );
			$projectname   = $this->get_plugin_setting( 'projectname' );

			// Getting the issue summary and description
			$issue_summary = $form["datarequestsaddon"]["issuesummary"];
			$issue_description = $form["datarequestsaddon"]["issuedescription"];

			// Parsing the merge tags
			$issue_summary = GFCommon::replace_variables( $issue_summary, $form, $entry, false, true, false, 'text' );
			$issue_description = GFCommon::replace_variables( $issue_description, $form, $entry, false, true, false, 'text' );

			// Forming the data
			$data = array(
				"fields" => array(
					"project" => array(
						"key" => $projectkey
					),
					"summary" => $issue_summary,
					"description" => $issue_description,
					"issuetype" => array(
						"name" => $projectname
					)
				)
			);
			$data = json_encode($data);

			// Making the cURL request to Jira
			$ch = curl_init($jirasite . "/rest/api/2/issue/");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/json",
				"Content-Length: " . strlen($data)
			));
			curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $apikey);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$result = curl_exec($ch);
        }
	}
    
    // # HELPERS -------------------------------------------------------------------------------------------------------
    
    /**
	 * The feedback callback for the 'project key' setting on the plugin settings page.
	 *
	 * @param string $value The setting value.
	 *
	 * @return bool
	 */
	public function is_valid_project_key( $value ) {
		return strlen( $value ) < 4;
	}

	/**
	 * The feedback callback for the 'site url' setting on the plugin settings page.
	 *
	 * @param string $value The setting value.
	 *
	 * @return bool
	 */
	public function is_valid_site_url( $value ) {
		return filter_var($value, FILTER_VALIDATE_URL);
	}
}