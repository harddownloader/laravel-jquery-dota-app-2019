
					$('#mySlider div').hide();
					var $interval = $('#mySlider').attr("interval");
					var $current = $('#myCurrentSlide').first();
					$current.show();
					setInterval(
						function(){ 
							
							$current.fadeOut(1500)
							$current = $current.next();							
							if($current.length == 0)
								$current = $('#myCurrentSlide').first();
							$current.fadeIn(1500);
							
								
						},

					 $interval );
					