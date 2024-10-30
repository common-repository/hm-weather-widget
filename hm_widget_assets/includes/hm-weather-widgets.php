<?php
/**
 * GS vmchannel Widgets
 */


if (!class_exists('Hm_Weather_Widget')) {
class Hm_Weather_Widget extends wp_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'hm_weather_widget', // Base ID
			__( 'Hm Weather widget', 'wf' ), // Name
			array( 'description' => __( 'Shows Weather Conditon', 'wf' ), ) //Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see hm_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		$state =!empty($instance['state'] ) ? $instance['state'] : __( 'CA', 'wf' );
		$city =!empty($instance['city'] ) ? $instance['city'] : __( 'San Francisco', 'wf' );
		$options=array(
			'location' => $instance['location'] ? true:false,
			'humidity' => $instance['humidity'] ? true:false,
			'f_button' => $instance['f_button'] ? true:false,
			'history' => $instance['history'] ? true:false,
			'temp' => $instance['temp'] 
			

			);


		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'vmchannel_widget_title', $instance['title'] ). $args['after_title'];
		}

               echo $this->get_weather($state,$city,$options);
		
                
        echo $args['after_widget'];
	}


	public function get_weather($state,$city,$options){
		$date=date("Ymd");

		$geoplug=new geoPlugin();
		$geoplug->locate();

		if($options['location']==true){
			$city=$geoplug->city;
			$state=$geoplug->region;
		}
		$json_string=file_get_contents("http://api.wunderground.com/api/a533f8dcf82b1ca2/geolookup/conditions/q/$state/$city.json");
		
		$json_data=json_decode($json_string,true);

		
		 $location=$json_data['location']['city'].','.$json_data['location']['country_name'];
		 $weather=$json_data['current_observation']['weather'];
		 $temp_f=$json_data['current_observation']['temp_f'];
		 $temp_c=$json_data['current_observation']['temp_c'];
		 $icon=$json_data['current_observation']['icon_url'];
		 $humadity=$json_data['current_observation']['relative_humidity'];
		 $f_button=$json_data['current_observation']['ob_url'];

		?>


			<div class="city-weather">
				<h3><?php echo $location; ?></h3>
				<?php if($options['temp']=='Fahrenheit'): ?>
					<h1><?php echo $temp_f; ?> °F</h1>
				<?php elseif ($options['temp']=='Celsius') :?>
					<h1><?php echo $temp_c; ?> °C</h1>
				<?php else: ?>
					<h1><?php echo $temp_c; ?> °C (<?php echo $temp_f; ?> °F)</h1>
				<?php endif; ?>
				<?php echo $weather; ?>
					<img src="<?php echo $icon;?>" alt="weather">
				<div class="history">
					<?php if($options['history']==true): ?>
					<?php 

						$json_string=file_get_contents("http://api.wunderground.com/api/a533f8dcf82b1ca2/geolookup/history_".$date."/q/$state/$city.json");
						$json_data=json_decode($json_string,true);


						?>
							<table>
								<h3>Daily History :</h3>
							<?php
							foreach ($json_data['history']['observations']  as$value) {
								?>
								<tr>
								<?php
								$items = array();
								$tems=array();
								$cods=array();
							
								 $word=$value['date']['pretty'];
								 
								 $pieces = explode(" ", $word);
								 $first_part = implode(" ", array_splice($pieces, 0, 2));
								 $items[] = $first_part;
								 $tems[]= $value['tempm'];
								 $cods[]=$value['conds'];
								if($items){?>
									
										
									<?php
										foreach ($items as $v) {
										?>
										<td><?php echo $v; ?></td>

										<?php
										}

										foreach ($tems as $c) {
											?>
											<td><?php echo $c; ?>°C</td>


											<?php
										}

										foreach ($cods as $w) {
											?>
											<td><?php echo $w; ?></td>


											<?php
										}

								}
								?>

							</tr>
							<?php
							
							}

							?>

							</table>

				<?php endif; ?>
				</div>

				<?php if($options['humidity']==true):?>
					<div>
						<strong> Relative Humidity: <?php echo $humadity; ?></strong>
					</div>
				<?php endif; ?>
				<?php if($options['f_button']==true):?>
					<div>
						<strong> <a href="<?php echo $f_button;?>" target="_blank"> Show full forecast</a></strong>
					</div>
				<?php endif; ?>
			</div>



		<?php

		
	

		
	}

	/**
	 * Back-end widget form.
	 *
	 * @see hm_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title =!empty($instance['title'] ) ? $instance['title'] : __( 'weather', 'wf' );
		$state =!empty($instance['state'] ) ? $instance['state'] : __( 'CA', 'wf' );
		$city =!empty($instance['city'] ) ? $instance['city'] : __( 'San Francisco', 'wf' );
		$location = isset($instance['location'])? $instance['location'] : 'off';
		$humidity = isset($instance['humidity'])? $instance['humidity'] : 'off';
		$f_button = isset($instance['f_button'])? $instance['f_button'] : 'off';
		$history = isset($instance['history'])? $instance['history'] : 'off';
		$temp = !empty($instance['temp'])? $instance['temp']: ' ';
		
		
		// $location= isset( $instance[ 'location' ] ) ? 'on' : 'off';
		
       
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','wf' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title); ?>">
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'location' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $location ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>"><?php _e( 'Check Auto Location', 'wf' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'state' ); ?>"><?php _e( 'State:','wf' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'state' ); ?>" name="<?php echo $this->get_field_name( 'state' ); ?>" type="text" value="<?php echo esc_attr( $state); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'city' ); ?>"><?php _e( 'City:','wf' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'city' ); ?>" name="<?php echo $this->get_field_name( 'city' ); ?>" type="text" value="<?php echo esc_attr( $city); ?>">
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'history' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'history' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $history ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'history' ) ); ?>"><?php _e( 'Show daily history', 'wf' ); ?></label>
		</p>
		
   		 <p>
		    <label for="<?php echo $this->get_field_id('temp'); ?>"><?php _e( 'Select Temparature :', 'wf' ); ?></label>
	        <select class='widefat' id="<?php echo $this->get_field_id('temp'); ?>"
	                name="<?php echo $this->get_field_name('temp'); ?>">
	          <option value='Fahrenheit'<?php echo ($temp=='Fahrenheit')?'selected':''; ?>>
	            Fahrenheit
	          </option>
	          <option value='Celsius'<?php echo ($temp=='Celsius')?'selected':''; ?>>
	            Celsius
	          </option> 
	          <option value='Both'<?php echo ($temp=='Both')?'selected':''; ?>>
	            Both
	          </option> 
	        </select>                
		      
     	</p>

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'humidity' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'humidity' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $humidity ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'humidity' ) ); ?>"><?php _e( 'Show Humidity', 'wf' ); ?></label>
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'f_button' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'f_button' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $f_button ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'f_button' ) ); ?>"><?php _e( 'Show Full forecast', 'wf' ); ?></label>
		</p>
				



      
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see hm_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['city'] = ( ! empty( $new_instance['city'] ) ) ? strip_tags( $new_instance['city'] ) : '';
		$instance['state'] = ( ! empty( $new_instance['state'] ) ) ? strip_tags( $new_instance['state'] ) : '';

        $instance['location'] = isset( $new_instance['location'] )  ? strip_tags( $new_instance['location'] ) : '';
        $instance['humidity'] = isset( $new_instance['humidity'] )  ? strip_tags( $new_instance['humidity'] ) : '';
        $instance['history'] = isset( $new_instance['history'] )  ? strip_tags( $new_instance['history'] ) : '';
        $instance['f_button'] = isset( $new_instance['f_button'] )  ? strip_tags( $new_instance['f_button'] ) : '';
        $instance['temp'] = strip_tags( $new_instance['temp'] );
        
       
		return $instance;
	}

} 
}


function register_hm_Weather_widget() {
    register_widget( 'Hm_Weather_Widget' );
}
add_action( 'widgets_init', 'register_hm_Weather_widget' );