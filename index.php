<!doctype html>
<html>
 <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="William Sengdara" >
  <meta name="description" content="Control device settings using PHP">
  <title>v4l2-ctl with PHP</title>
  <style>
   body { font-size: 12pt;}
   a {text-decoration: initial; font-size:10pt;}
   a[href^="#"]{color:green;}
   input[type="submit"]{width:100%; padding:5px}
   h5, h6 {
	
    font-weight: initial;
    margin: 9px 0 0 0;
	}
	small {font-size:40%; font-weight:initial}
	.warn {color:red}
  </style>
 </head>
 <body>
  <?php
		// did we do an update?
		if (@ $_POST['update'] == 1){
			$device = $_POST['device'];

			foreach ($_POST as $name=>$value){
				switch ($name){
					case 'device':
					case 'update':
						// ignore in POST
						break;

					default:
						$command = "v4l2-ctl -d $device --set-ctrl=$name=$value";
						$x = shell_exec( $command );
				}
			}
		}

		// get list of devices
		$options = "<option value=''>-Select device-</option>";
		$output = shell_exec("v4l2-ctl --list-devices");
		$output = trim($output);		
		$devs = explode("\n", $output);

		$controls = "";

		foreach($devs as $key=>$dev){
			$dev = trim($dev);

			if (substr($dev,0, 1) !== "/" && strlen($dev)){
				$device = trim($devs[$key+1]);
				$selected = @ $_POST['device'] == $device ? "selected" : "";

				if ($selected){
					//echo "Fetching controls for $device <BR>";

					$ctls = shell_exec("v4l2-ctl -d $device --list-ctrls");
					$ctls = trim($ctls);
					//echo "<pre>$ctls</pre>";
					$ctls = explode("\n", $ctls);
					
					foreach($ctls as $line){
						$ctl = explode(":", $line);
						$setting = trim($ctl[0]);
						$settings = explode(" ", trim($ctl[1]));
						
						$default = "";
						$value = "";
						$props = "";

						foreach($settings as $key=>$val){
							$props .= "$val ";

							// get current value
							if (substr($val,0, strlen("value")) == "value"){
								$value = (int) explode("=",$val)[1];
							}

							// get default setting
							if (substr($val,0, strlen("default")) == "default"){
								$default = (int) explode("=",$val)[1];
							}
						}
						$setting = explode(" ", trim($setting))[0];
						$setting_ = ucwords($setting);
						$controls .= "$setting_ <BR> <input id='$setting' name='$setting' 
													title='$value' style='width:90%' type='range' $props> 
										<a href='#' onclick=\"document.querySelector('#$setting').value='$default'; return false;\">Reset</a><BR>";
					}
				}

			    $options .= "<option value='$device' $selected>$device -- $dev</option>";
			}
		}

		echo "<h1>v4l2-ctl with PHP <small>by William Sengdara</small></h1>
				<h5 class='warn'>Note: Tested with devices with a single interface only.</h5>
				<h5>Inspired by this article: <a href='https://www.kurokesu.com/main/2016/01/16/manual-usb-camera-settings-in-linux/' 
									target='_blank'>Manual USB camera settings in Linux</a></h5>
				<HR>
				<form method='POST'>
				<input type='hidden' name='update' value='1'>

				<select name='device' style='width:100%' onchange='document.forms[0].submit()'>
				 $options
				</select>	
				<HR>
				$controls
				<BR>
				<input type='submit' value='Update Device'>
				<BR>
			  </form>
			  <script>
				window.addEventListener('load', ()=>{
					var ctls = document.querySelectorAll(\"input[type='range']\")
					for(let idx=0; idx < ctls.length; idx++){
						let ctl = ctls[idx];
						ctl.addEventListener('mousedown',()=>{
							ctl.mousedown = true;
						});
						ctl.addEventListener('mouseup',()=>{
							ctl.mousedown = false;
						});
						ctl.addEventListener('change',()=>{
							if (!ctl.mousedown) return;
							//console.log(ctl.name, ctl.value);
							ctl.title = ctl.value;
						});
					}
				}, false);
			  </script>";


		if (@ $_POST['update'] == 1){
				$date = date('Y-m-d H:i:s');
			echo "<h6>Device was updated at $date</h6>";
		}
   ?>
	<h6>Please note: changes take a few seconds to reflect on device</h6>
 </body>
</html>
