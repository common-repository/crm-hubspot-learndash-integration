<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SevenShores\Hubspot\Factory as HS_Factory;

/**
 * Class LD_HubSpot_Integration
 * @property HS_Factory $hubspot
 * @package LD_HusSpot
 */
class LD_HubSpot_Integration {
	public function __construct() {

		$this->options = get_option( 'learndash-hubspot-settings-key', [] );
        $api_key = null;

		if ( isset( $this->options['api_key'] ) && ! empty( $this->options['api_key'] ) ) {
		    $api_key = $this->options['api_key'];
        }

		try {
			$this->hubspot = HS_Factory::create( $api_key );
		} catch ( Exception $e ) {
			return;
		}

		add_action( 'learndash_update_course_access', [ $this, 'course_access' ], 10, 4 );
		add_action( 'learndash_course_completed', [$this, 'ld_hubspot_course_completed'], 5, 1 );

	}

	//function to run after course completion
	public function ld_hubspot_course_completed( $data ) {
		$user_id = $data['user']->ID;
		$course_id = $data['course']->ID;
		$course_status = learndash_course_status( $course_id, $user_id );

		//get current course quizes
		$user_course_quizes = get_user_meta( $user_id, '_sfwd-quizzes', true );
		$quiz_score = 0;
		$quiz_num = 0;
		foreach( $user_course_quizes as $user_course_quize ) {
			$quiz_course_id = $user_course_quize['course'];
			if ( $quiz_course_id == $course_id ) {
				$quiz_percentage = $user_course_quize['percentage'];
				$quiz_score = $quiz_percentage + $quiz_score;
				$quiz_num = 1 + $quiz_num;
			}
		}
		$quiz_score_final = $quiz_score/$quiz_num;

		//get dynamic fields
		$ldhs_deals_fields = get_option( 'learndash-hubspot-settings-deal' );
		foreach( $ldhs_deals_fields as $key => $value ) {
			if((!empty( $value ) && !empty( key )) && $key == "course_status" ) {
				$course_status_name = $value;
			}
			if( $key == "quiz_score_field" ) {
				$quiz_score_name = $value;
			}
		}
		
		//get deal id
		$deal_key = "ldhs_dl_".$course_id;
		$deal_id = get_user_meta( $user_id, $deal_key, true );

		//deal properties to update
		$deal = [
			'properties' => [
				[
					'name'  => $course_status_name,
					'value' => $course_status,
				],
				[
					'name'  => $quiz_score_name,
					'value' => $quiz_score_final,
				],
			],
		];

		$update_deal = $this->hubspot->deals()->update( $deal_id, $deal );
	}

	/**
	 * Run actions after a users list of courses is updated
	 *
	 * @param  int      $user_id
	 * @param  int      $course_id
	 * @param  array    $access_list
	 * @param  bool     $remove
	 */
	public function course_access( $user_id, $course_id, $access_list, $remove ) {
		if ( is_admin() ) {
			return;
		}

		$user_data = get_userdata( $user_id );
		$course_title  = get_the_title( $course_id );
		$meta          = get_post_meta( $course_id, '_sfwd-courses', true );
		$course_price  = $meta['sfwd-courses_course_price'];
		$course_price  = floatval( preg_replace( '/[^0-9.]/', '', $course_price ) );
		$current_time  = ( current_time( 'timestamp' ) * 1000 );
		$course_status = learndash_course_status( $course_id, $user_id );
		if ( $course_status != "Completed" ) {
			$course_status = "In Progress";
		}
		$key_data = array( "course_title"=>$course_title, "course_status"=>$course_status );
		$ldhs_deals_data = get_option( 'learndash-hubspot-settings-deal' );
		$ldhs_data_arr = [
			[
			'name'  => 'dealstage',
			'value' => "closedwon",
		],
		[
			'name'  => 'closedate',
			'value' => $current_time,
		],
		[
			'name'  => 'amount',
			'value' => $course_price,
		],
		];
		foreach( $ldhs_deals_data as $key => $value ) {
			if( (!empty( $value ) && !empty( $key ) ) && !empty( $key_data[$key] ) ) {
				$ldhs_data_arr[] =
					[
						'name' => $value,
						'value'    => $key_data[$key],
				];
			}
		}
		if ( ! $user_data ) {
			return;
		}

		try {
			$contact = $this->create_contact( $user_data );

			$ldhs_deals = [];
			$ldhs_deals = [
				'associations' => [
					'associatedVids' => [ $contact->data->vid ],
				],
				'properties'   => $ldhs_data_arr,
			];


			$create_deal = $this->hubspot->deals()->create( $ldhs_deals );
			
			//save deal id in user meta
			$dealid_value = $create_deal['dealId'];
			$update_meta = update_user_meta( $user_id, "ldhs_dl_".$course_id, $dealid_value );
		} catch ( Exception $e ) {
		}
	}

	/**
	 * Create contact if not already exists
	 * @param WP_User $user_data
	 * @return \SevenShores\Hubspot\Http\Response
	 */
	protected function create_contact( WP_User $user_data ) {
		$ldhs_contact_data = get_option( 'learndash-hubspot-settings-contact' );
		
		$ldhs_data = 	[			
				[
				'property' => 'email',
				'value'    => $user_data->user_email,
			],
		];
		foreach( $ldhs_contact_data as $key => $value ) {
			if((!empty( $value ) && !empty( $key )) && !empty( $user_data->$key ) ){
				$ldhs_data []=
					[
						'property' => $value,
						'value'    => $user_data->$key,
				];
			}
		}

		try {
			$contact = $this->hubspot->contacts()->getByEmail( $user_data->user_email );	
		} catch ( Exception $e ) {
			$contact = $this->hubspot->contacts()->create( $ldhs_data );	
		}
		return $contact;
	}
}
