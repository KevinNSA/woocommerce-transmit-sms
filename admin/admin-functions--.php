<?php
add_action( 'admin_init', 'jQueryUi' );
function jQueryUi() {
    wp_enqueue_style( 'css-jquery-ui', WB_PLUGIN_URL . '/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css' );
    wp_enqueue_style( 'baseCSS', WB_PLUGIN_URL . '/style.css' );
     wp_register_script('jquery-ui',  WB_PLUGIN_URL . '/jquery-ui/jquery-ui-1.10.4.custom.js', false, null);
     wp_enqueue_script('jquery-ui');
}
function WB_admin_menu() {
	add_submenu_page('woocommerce', __('Transmit SMS Notifications', 'burst_sms'),  __('Transmit SMS Notifications', 'burst_sms') , 'manage_woocommerce', 'WBSMSC_options', 'WBSMSC_options');
    //add_menu_page( 'Receive SMS Enquiry', 'Receive SMS Enquiry', 'manage_options', 'setting contact form', 'WBSMSC_options' );
}

function WBSMSC_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	if(isset($_POST['WB_hidden']) && $_POST['WB_hidden'] == "Y"){
            WBSMSC_handleSubmit();
        }
        echo WBSMSC_settingForm();
}
function WBSMSC_settingForm(){
    global $arrPhoneCodeCountry;
    global $defPhoneCodeCountry;
    $WBSms = unserialize(stripslashes(get_option('WBSmsSettings')));
    $burstSmsApiKey  = base64_decode($WBSms['apikey']);
    $burstSmsApiSecret  = base64_decode($WBSms['apisecret']);
    $burstSmsAdminNumber = base64_decode($WBSms['reciver_number']);
    empty($WBSms['country_code'])?$burstSmsCountryCode=$arrPhoneCodeCountry:$burstSmsCountryCode=$WBSms['country_code'];
    empty($WBSms['receivedCustom'])?$burstSmsreceivedCustom=WB_orderRecivedMsg:$burstSmsreceivedCustom=trim($WBSms['receivedCustom']);
    empty($WBSms['processingCustom'])?$burstSmsprocessingCustom=WB_orderProccessingMsg:$burstSmsprocessingCustom=trim($WBSms['processingCustom']);
    empty($WBSms['completedCustom'])?$burstSmscompletedCustom=WB_orderCompletedMsg:$burstSmscompletedCustom=trim($WBSms['completedCustom']);
    empty($WBSms['pendingCustom'])?$burstSmspendingCustom=WB_orderPendingMsg:$burstSmspendingCustom=trim($WBSms['pendingCustom']);
    empty($WBSms['failedCustom'])?$burstSmsfailedCustom=WB_orderFailedMsg:$burstSmsfailedCustom=trim($WBSms['failedCustom']);
    empty($WBSms['onholdCustom'])?$burstSmsonholdCustom=WB_orderOnholdMsg:$burstSmsonholdCustom=trim($WBSms['onholdCustom']);
    empty($WBSms['refundedCustom'])?$burstSmsrefundedCustom=WB_orderRefundedMsg:$burstSmsrefundedCustom=trim($WBSms['refundedCustom']);
    empty($WBSms['cancelledCustom'])?$burstSmscancelledCustom=WB_orderCancelledMsg:$burstSmscancelledCustom=trim($WBSms['cancelledCustom']);
    empty($WBSms['default_country_code'])?$defaultPhonecountry=$defPhoneCodeCountry:$defaultPhonecountry=$WBSms['default_country_code'];
    $statuses = (array) get_terms( 'shop_order_status', array( 'hide_empty' => 0, 'orderby' => 'id' ) );

    
    
  
  
   // $burstSmsCountryCode = array(62=>'+62 Indonesia',41=>'+41 Australia');
    
    
     ob_start();  
  
?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        //dialog add CC
        var ccvalue = jQuery( "#ccvalue" ),
			cclabel = jQuery( "#cclabel" ),
			allFields = jQuery( [] ).add( ccvalue ).add( cclabel ),
			tips = jQuery( ".validateTips" );
            
        function updateTips( t ) {
			tips
				.text( t )
				.addClass( "ui-state-highlight" );
			setTimeout(function() {
				tips.removeClass( "ui-state-highlight", 1500 );
			}, 500 );
		}

		function checkLength( o, n, min, max ) {
			if ( o.val().length > max || o.val().length < min ) {
				o.addClass( "ui-state-error" );
				updateTips( "Length of " + n + " must be between " +
					min + " and " + max + "." );
				return false;
			} else {
				return true;
			}
		}

		function checkRegexp( o, regexp, n ) {
			if ( !( regexp.test( o.val() ) ) ) {
				o.addClass( "ui-state-error" );
				updateTips( n );
				return false;
			} else {
				return true;
			}
		}
        
        jQuery('[rel="Transmitparamdata"]').css('display','none');
        jQuery('[rel="Transmitparamdata2"]').css('display','none');
        var toogleOthrSetting = 0;
        jQuery('#otherSetting').click(function(){
            if(toogleOthrSetting >0){
                jQuery('[rel="Transmitparamdata2"]').slideUp('slow');
                toogleOthrSetting = 0;
                 jQuery(this).html('Other Settings &#9660');
            }else {
                jQuery('[rel="Transmitparamdata2"]').slideDown('slow');
                toogleOthrSetting = 1
                jQuery(this).html('Other Settings &#9650');
                jQuery('#WB_pendingCustom').focus();
            }
           
        });
        renderlist();
        required = ["WB_apikey", "WB_apisecret", "WB_adminNumber","WB_submitLabel"];
        errornotice = jQuery("#error");
        emptyerror = "Please fill out this field.";
        jQuery("#WBform").submit(function(){	
	//Validate required fields
		for (i=0;i<required.length;i++) {
			var input = jQuery('#'+required[i]);
			if ((input.val() == "") || (input.val() == emptyerror)) {
				input.addClass("needsfilled");
				input.val(emptyerror);
				errornotice.fadeIn(750);
			} else {
				input.removeClass("needsfilled");
			}
		}
		//if any inputs on the page have the class 'needsfilled' the form will not submit
		if (jQuery(":input").hasClass("needsfilled")) {
                   return false;
		} else {
			errornotice.hide();
			return true;
		}
         });
         // Clears any fields in the form when the user clicks on them
	jQuery(":input").focus(function(){		
	   if (jQuery(this).hasClass("needsfilled") ) {
			jQuery(this).val("");
			jQuery(this).removeClass("needsfilled");
	   }
	});
    	jQuery( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
			buttons: {
				"Add country": function() {
					var bValid = true;
					allFields.removeClass( "ui-state-error" );

					bValid = bValid && checkLength( ccvalue, "country code", 1, 3 );
					bValid = bValid && checkLength( cclabel, "label of country", 2, 100 );
                    
					bValid = bValid && checkRegexp( ccvalue, /^[0-9]+$/i, "Country Code must in a number ex : 61." );
					//bValid = bValid && checkRegexp( cclabel, /^[a-z]([0-9a-z_])+$/i, "Country may consist of a-z, 0-9, underscores, begin with a letter. ex: +61 Australia" );
					// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
					//bValid = bValid && checkRegexp( cclabel, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
				
					if ( bValid ) {
					       //add hidden field
                        jQuery('#burstCCHiden').append('<input type="hidden" name="burstcc[]" value="' + cclabel.val().toString() + '|' + ccvalue.val().toString() + '">');
                        //jQuery('#listCC').prepend("<div class='cpcBox' id='cpc" + ccvalue.val() + "' onclick='deletePhoneCC(\"" + ccvalue.val() + "\");'  style='float:left;padding:5px; margin-right:8px; border:1px #ccc solid'>" + ccvalue.val().toString() + ' : ' + cclabel.val().toString() + "</div>");
                         jQuery('#listCC').prepend("<div id='cpc" + ccvalue.val() + "' style='float:left; margin-right: 15px;'><div class=\"tagchecklist\" onclick='deletePhoneCC(\""+ ccvalue.val() +"\");' title=\"remove {$cc}\"  style='margin-left:0px !important'><span><a id=\"post_tag-check-num-0\" class=\"ntdelbutton\">X</a></span></div> <div class=\"cpcBox\" id=\"cpcBox"+ ccvalue.val() +"\"  onclick='setdefaultPhoneCC(\""+ ccvalue.val() +"\");' title=\"set as default\"  >"+ ccvalue.val() +' : '+ cclabel.val() +"</div></div>");      
                         /*var o = new Option(cclabel.val(), ccvalue.val());
                        jQuery(o).html(cclabel.val());
                        jQuery("#WB_countryCode").append(o) */
						jQuery( this ).dialog( "close" );
                    	}
				},
				Cancel: function(event) {
					jQuery( this ).dialog( "close" );
                    event.preventDefault();
                    
				}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});

		jQuery( "#dialogCC" )
			.button()
			.click(function(event) {
				jQuery( "#dialog-form" ).dialog( "open" );
                event.preventDefault();
			});
     
     /*	jQuery( "#dialog-form-testSMS" ).dialog({
			autoOpen: false,
			height: 400,
			width: 500,
			modal: true,
			buttons: {
				"Send SMS": function() {
                    jQuery.ajax({
                       url: '<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>',
                       type:'POST',
                       data:'action=testSMS&phone=' + jQuery('#tsPhoneNUmber').val() + '&message='+ jQuery('#tsMessage').val() + '&key='+ jQuery('#WB_apikey').val() + '&secret='+ jQuery('#WB_apisecret').val(),
                       success: function(result){
                             if(result== 'success'){
                                jQuery('#dialog-form-testSMS').prepend('<div class="success">Message has been sent</div>');
                             }else {
                                jQuery('#dialog-form-testSMS').prepend('<div class="error">'+result +'</div>');
                             }     
                          }
    	              });
				},
				Cancel: function(event) {
					jQuery( this ).dialog( "close" );
                    event.preventDefault();
                    
				}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});
    */
     jQuery("#dialogTestSMS")
        .button()
			.click(function(event) {
			     if(jQuery('#WB_adminNumber').val().length < 1){
			         jQuery('#WB_adminNumber').addClass("needsfilled");
				    jQuery('#WB_adminNumber').val(emptyerror);
				    errornotice.fadeIn(750);
                    return false;
			     }
				jQuery.ajax({
                       url: '<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>',
                       type:'POST',
                       data:'action=testSMS&phone=' + jQuery('#WB_adminNumber').val() + '&message='+ jQuery('#WB_receivedCustom').val() + '&key='+ jQuery('#WB_apikey').val() + '&secret='+ jQuery('#WB_apisecret').val(),
                       success: function(result){
                             if(result== 'success'){
                                jQuery('#dialogTestSMS').after('<div class="success">Message has been sent</div>');
                                setTimeout(function() {
                                    jQuery('.success').remove();
                                }, 5000);
                             }else {
                                jQuery('#dialogTestSMS').after('<div class="error">'+result +'</div>');
                                setTimeout(function() {
                                    jQuery('.error').remove();
                                }, 5000);
                             }     
                          }
    	              });
                      return false;
			});
     
     });
     
     function renderlist(){
         var apikey = jQuery('#WB_apikey').val();
         var apisecret = jQuery('#WB_apisecret').val();
          if(apikey != "" && apisecret != ""){
            jQuery("#msgVerify").fadeIn('fast');
            jQuery.ajax({
                   url: '<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>',
                   type:'POST',
                   data:'getlist=Y&apikey=' + apikey + '&secret=' + apisecret + '&selected=' + <?php echo empty($burstSmsList)?"'N'":$burstSmsList ?>,
                   success: function(result){
                        obj = JSON.parse(result);
                        if(parseInt(obj.status) > 0){
                            jQuery('#WB_addToList').html(obj.result);
                            jQuery("#msgVerify").css("color","green");
                            jQuery('[rel="Transmitparamdata"]').css('display','block');
                            jQuery("#msgVerify").html("<?php echo WB_successVerify ?>");
                        }else{
                              jQuery("#msgVerify").css("color","red");
                              jQuery('[rel="Transmitparamdata"]').css('display','none');
                              //if(obj.result.length)
                              //  jQuery("#msgVerify").html(obj.result);
                            //  else 
                                 jQuery("#msgVerify").html("<?php echo WB_failVerify ?>");
                           } 
                        }
		});
               }
      return false;
    }
    function deletePhoneCC(val){
        jQuery( "#dialog-confirm" ).dialog({
			resizable: false,
			height:180,
			modal: true,
			buttons: {
				"Remove": function(event) {
                    jQuery.ajax({
                       url: '<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>',
                       type:'POST',
                       data:'id=' + val + '&action=deleteCPC',
                       success: function(result){
                               jQuery('#cpc' + val).remove();
                               } 
                           });
				    
					jQuery( this ).dialog( "close" );
                    event.preventDefault();
				},
				Cancel: function(event) {
					jQuery( this ).dialog( "close" );
                    event.preventDefault();
				    },
                }
			})
    }
    var currDefCC = <?=$defaultPhonecountry?>;
    function setdefaultPhoneCC(val){
       jQuery('#cpcBox' + val).css("background-color", "#E9E8E4");
        jQuery('#cpcBox' + currDefCC).css("background-color", "white");
         jQuery('#cpcBox' + currDefCC).attr("onclick", "setdefaultPhoneCC('" + currDefCC + "')");
         jQuery('#cpcBox' + currDefCC).attr("title", "set to default");
         jQuery('#cpcBox' + val).attr("title", "default");
        currDefCC = val;
        jQuery("#defburstcc").val(val);
    }
    
 </script>
<style>
    #error {
	color:red;
	font-size:10px;
	display:none;
    }
    .needsfilled {
	
	color:red !important;
        border: 1px solid red !important; 
    }
    form ul{list-style-type: none;}
        form ul li{clear: both;height: auto;padding-bottom: 30px;position: relative;}
        .clearfix:after {clear: both;content: ".";display: block;height: 0;margin-bottom: -17px;visibility: hidden;}
        .clearfix {display: block;}
    label {padding-left: 0px;width: 120px;color: #3C3C3C;float: left;text-align:left;}
    input[type="text"], select, textarea, .textarea {background: none repeat scroll 0 0 #F0EFE9;border: 1px solid #938F77;color:#666;float: left;outline: medium none;padding: 4px;width:550px;}
    textarea{height:120px;}
    form em{
        font-size: 11px;
        margin-left: 120px;
    }
    
</style>
<div id="post-body" class="metabox-holder columns-1">
<div class="wrap"> 
    <div id="icon-options-general" class="icon32">
        <br> </div>
<h2> <?php echo  __( 'Transmit SMS Enquiry', 'WB_trdom' );?> </h2><br>

    <div id="postbox-container-2" class="postbox-container">
        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
            <div id="revisionsdiv" class="postbox ">
           <!-- <div class="handlediv" title="Click to toggle"><br></div> -->
                <h3 class="hndle"><span style="float:left; margin:0 7px 20px 0;" class="ui-icon ui-icon-gear"></span> <span>
                        <?php    echo "<span>" . __( 'Settings', 'WB_trdom' ) . "</span></h3>"; ?>
            <div class="inside">

            <div style="width:96%; padding:10px">
                <form name="WBform" id="WBform" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
                    <input type="hidden" name="WB_hidden" value="Y">  
                    
                    <div style=" width:700px">
                        <ul style=" width:700px">
                            <li class="clearfix" style="margin-bottom:-7px !important"> <label for="WB_apikey"><?php _e("API Key : " ); ?> </label><input type="text" name="WB_apikey" id="WB_apikey" value="<?php echo $burstSmsApiKey; ?>" >
                        </li>
                        <li class="clearfix" style="margin-bottom:-7px !important"><label for="WB_apisecret"><?php _e("API Secret : " ); ?></label><input type="text" name="WB_apisecret"  id="WB_apisecret" value="<?php echo $burstSmsApiSecret; ?>" >
                            <em> Get these details from the API settings section of your account.</em>
                        </li>
                         <li class="clearfix">
                             <label for="verify">&nbsp;</label>
                               <input id="verify" class="button button-primary button-large" type="button" onclick="renderlist();" accesskey="p" value="Verify key" name="verify">
                               <em id="msgVerify" style="margin-left:10px !important;display:none"><img src="<?php echo WB_PLUGIN_URL ?>/images/loading.gif" title="still loading" > </em>
                         </li>
                      
                         <li class="clearfix" rel="Transmitparamdata"><label for="WB_addtolits"><?php _e("Add to list: " ); ?></label><select name="WB_addToList" id="WB_addToList"> </select>
                        <em> List ID can be found just before the list name when viewing the list.</em>
                     </li>
                       <li class="clearfix" rel="Transmitparamdata"><label for="WB_addtolits"><?php _e("Phone number country code: " ); ?></label>
                       <div id="listCC" style="width: 540px; float: left;">
                            <?PHP
                               if(is_array($burstSmsCountryCode)){
                                foreach($burstSmsCountryCode as $k =>$cc){
                                    if($defaultPhonecountry == $k){
                                        echo "<div id='cpc".$k."' style='float:left; margin-right: 15px;'>
                                           <div class=\"tagchecklist\"  onclick='deletePhoneCC(\"".$k."\");' title=\"remove {$cc}\"  style='margin-left:0px !important'><span><a id=\"post_tag-check-num-0\" class=\"ntdelbutton\">X</a></span></div>
                                            <div class=\"cpcBox\" id=\"cpcBox".$k."\" style='background-color:#E9E8E4;'  title=\"default\"  >".$k.' : '.$cc."</div>
                                         </div>";
                                    }else{
                                        echo "<div id='cpc".$k."' style='float:left; margin-right: 15px;'>
                                           <div class=\"tagchecklist\" onclick='deletePhoneCC(\"".$k."\");' title=\"remove {$cc}\"  style='margin-left:0px !important'><span><a id=\"post_tag-check-num-0\" class=\"ntdelbutton\">X</a></span></div>
                                            <div class=\"cpcBox\" id=\"cpcBox".$k."\"  onclick='setdefaultPhoneCC(\"".$k."\");' title=\"set as default\"  >".$k.' : '.$cc."</div>
                                         </div>";
                                        }
                                    }
                               }
                               ?>
                               
                        <div style="float: left;margin-right: 8px;"/><button id="dialogCC">add new</button> </div> 
                       </div>
                       
                        <span id="burstCCHiden">
                        <?PHP
                        if(is_array($burstSmsCountryCode)){
                           foreach($burstSmsCountryCode as $k =>$cc){
                                echo "<input type='hidden' name='burstcc[]' value='".$cc."|".$k."'>";
                           }
                        }
                       ?> 
                       <input type="hidden" name="defburstcc" id="defburstcc" value="<?=$defaultPhonecountry?>" />
                       </span>
                    
                     <!--
                       <li class="clearfix" rel="Transmitparamdata"><label for="WB_ownerCustom"><?php _e("Owner costum message : " ); ?></label><textarea name="WB_ownerCostum" id="WB_ownerCostum"><?=$burstSmsownerCostum?></textarea>
                        <em> You can use the variables : [NAME] and [MESSAGE]  </em>
                     </li> -->
                     </ul>
                      <h3 rel="Transmitparamdata" class="hndle"><span>
                        <?php    echo "<span>" . __( 'Admin Notifications', 'WB_trdom' ) . "</span></h3>"; ?>
                        <ul>
                         <li class="clearfix" rel="Transmitparamdata"><label for="WB_enaReceivedCustom"> &nbsp;</label>
                            <input type="checkbox" value="1" name="WB_enaReceivedCustom" <?= (int)@$WBSms['enaReceivedCustom'] >0?'':' checked="checked"' ?> /> <?php _e("Enable new order SMS admin notifications " ); ?>
                         </li>
                             <li class="clearfix" rel="Transmitparamdata"><label for="WB_adminNumber"><?php _e("Admin Mobile Number : " ); ?></label><input type="text" name="WB_adminNumber" id="WB_adminNumber" value="<?php echo $burstSmsAdminNumber; ?>">
                           <em style=" margin-left: 13px !important"> The mobile number you wish to receive messages on in international format eg. +614XXXXXXXX +447XXXXXXXX  </em>
                       </li>
                            
                       <li class="clearfix" rel="Transmitparamdata"><label for="WB_receivedCustom"><?php _e("Order received custom message : " ); ?></label><textarea type="text" name="WB_receivedCustom"  id="WB_receivedCustom" ><?=$burstSmsreceivedCustom?></textarea>
                            <em> This message would sent to admin.</em>
                   
                       </li>
                        </li>
                       <li class="clearfix" rel="Transmitparamdata"><label for="testSMS"><?php _e("Test Sending SMS: " ); ?></label><button id="dialogTestSMS" name="testSMS">Send SMS</button> </em>
                     </li>
                       </ul>
                       
                       
                       
                       
                      <h3 rel="Transmitparamdata" class="hndle"><span>
                        <?php    echo "<span>" . __( 'Customer Notifications', 'WB_trdom' ) . "</span></h3>"; ?>
                       <ul>
                       
                       <!--NEW one-->
                        <li class="clearfix" rel="Transmitparamdata">
                       <?PHP
                        foreach($statuses as $ks => $orderStatus){
                            if($orderStatus->slug == 'processing' || $orderStatus->slug == 'completed' ){
                       ?>   <div style="float: left; padding: 4px;width: 330px;">
                                    <input type="checkbox" value="1" name="WB_ena<?= ucfirst($orderStatus->slug)?>Custom" <?= (int)@$WBSms[ucfirst($orderStatus->slug)] < 1?'':' checked="checked"' ?> /> <?php _e("Enable ".ucfirst($orderStatus->slug)." order SMS  notifications " ); ?>
                                    </div>
                                
                         <?PHP
                            }
                         }
                       ?>
                       </li>
                       
                       
                       
                       
                       <li class="clearfix" rel="Transmitparamdata">
                            <div style="float: left; padding: 4px;width: 330px;">
                                <input type="checkbox" value="1" name="WB_enaProcessingCustom" <?= (int)@$WBSms['enaProcessingCustom'] < 1?'':' checked="checked"' ?> /> <?php _e("Enable processing order SMS  notifications " ); ?>
                             </div>
                             <div style="float: left; padding: 4px;width: 330px;">
                                <input type="checkbox" value="1"  name="WB_enaCompletedCustom" <?= (int)@$WBSms['enaCompletedCustom'] < 1?'':' checked="checked"' ?>/> <?php _e("Enable completed order SMS notifications " ); ?>
                             </div>
                            
                         </li>
                         
                         
                         
                        <li class="clearfix" rel="Transmitparamdata"><label for="WB_processingCustom"><?php _e("Order processing custom message : " ); ?></label><textarea type="text" name="WB_processingCustom"  id="WB_processingCustom"> <?=$burstSmsprocessingCustom?></textarea>
                             <em> This message would  sent to customer.</em>
                        </li>
                       <li class="clearfix" rel="Transmitparamdata"><label for="WB_completedCustom"><?php _e("Order completed custom message : " ); ?></label><textarea type="text" name="WB_completedCustom"  id="WB_completedCustom"><?=$burstSmscompletedCustom?></textarea>
                        <em> This message would sent to customer.</em></li>
                       </ul>
                       <div rel="Transmitparamdata" style="width: 700px;padding-bottom: 5px; border-bottom: 1px #ccc solid; cursor: pointer;">
                            <a href="#" id="otherSetting" >Other Settings &#9660 </a>
                       </div>
                       <ul style=" width:700px"> 
                       <li class="clearfix" rel="Transmitparamdata2">
                        <div style="float: left; padding: 4px;width: 330px;">
                                <input type="checkbox" value="1" name="WB_enaPendingCustom" <?= (int)@$WBSms['enaPendingCustom'] < 1?'':' checked="checked"' ?>/> <?php _e("Enable pending order SMS notifications " ); ?>
                             </div>
                             <div style="float: left; padding: 4px;width: 330px;">
                              <input type="checkbox" value="1" name="WB_enaFailedCustom" <?= (int)@$WBSms['enaFailedCustom'] < 1?'':' checked="checked"' ?> /> <?php _e("Enable failed order SMS  notifications " ); ?>
                             </div>
                             <div style="float: left; padding: 4px;width: 330px;">
                                <input type="checkbox" value="1" name="WB_enaOnholdCustom" <?= (int)@$WBSms['enaOnholdCustom'] < 1?'':' checked="checked"' ?>  /> <?php _e("Enable on-hold order SMS  notifications " ); ?>
                            </div>
                            <div style="float: left; padding: 4px;width: 330px;">
                                <input type="checkbox" value="1" name="WB_enaRefundedCustom" <?= (int)@$WBSms['enaRefundedCustom'] < 1?'':' checked="checked"' ?> /> <?php _e("Enable refunded order SMS notifications " ); ?>
                             </div>
                             <div style="float: left; padding: 4px;width: 330px;">
                                <input type="checkbox" value="1" name="WB_enaCancelledCustom" <?= (int)@$WBSms['enaCancelledCustom'] < 1?'':' checked="checked"' ?> /> <?php _e("Enable cancelled order SMS  notifications " ); ?>
                             </div>
                        </li>
                       
                         <li class="clearfix" rel="Transmitparamdata2"><label for="WB_pendingCustom"><?php _e("Order pending custom message : " ); ?></label><textarea type="text" name="WB_pendingCustom"  id="WB_pendingCustom"><?=$burstSmspendingCustom?></textarea>
                        <em> This message would sent to customer.</em></li>
                         <li class="clearfix" rel="Transmitparamdata2"><label for="WB_failedCustom"><?php _e("Order failed custom message : " ); ?></label><textarea type="text" name="WB_failedCustom"  id="WB_failedCustom"><?=$burstSmsfailedCustom?></textarea>
                        <em> This message would sent to customer.</em></li>
                         <li class="clearfix" rel="Transmitparamdata2"><label for="WB_onholdCustom"><?php _e("Order  on-hold message : " ); ?></label><textarea type="text" name="WB_onholdCustom"  id="WB_onholdCustom"><?=$burstSmsonholdCustom?></textarea>
                        <em> This message would sent to customer.</em></li>
                         <li class="clearfix" rel="Transmitparamdata2"><label for="WB_refundedCustom"><?php _e("Order refunded custom message : " ); ?></label><textarea type="text" name="WB_refundedCustom"  id="WB_refundedCustom"><?=$burstSmsrefundedCustom?></textarea>
                        <em> This message would sent to customer.</em></li>
                         <li class="clearfix" rel="Transmitparamdata2"><label for="WB_cancelledCustom"><?php _e("Order cancelled custom message : " ); ?></label><textarea type="text" name="WB_cancelledCustom"  id="WB_cancelledCustom"><?=$burstSmscancelledCustom?></textarea>
                        <em> This message would sent to customer.</em></li>
                       
                        <li class="clearfix" rel="Transmitparamdata"> <label for="publish">&nbsp;</label>
                            <input id="publish" class="button button-primary button-large" type="submit" accesskey="p" value="&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp" name="publish">

                      </li>
                      
                        </div>  
                </form>  
                </div>
                </div>
        </div>

    </div>
    </div>

     <div id="postbox-container-2" class="postbox-container" style="margin-left:20px">
        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
            <div id="revisionsdiv" class="postbox ">
                 <h3 class="hndle"><span>
                        Avalaible Shortcode </span> </h3>
                    <div class="inside">
                        <ul>
                            <li style="height: 339px;list-style:bullet;margin-left: 10px;margin-bottom: 10px; width: 340px;">
                             <div style="float: left; width: 105px; padding: 4px;background-color: #D7FFF4; margin: 4px;">[[order_number]]</div>
                            <div style="float: left; width: 90px; padding: 4px;background-color: #D7FFF4;margin: 4px;">[[order_date]]</div>
                            <div style="float: left; width: 90px; padding: 4px;background-color: #D7FFF4;margin: 4px;">[[order_total]]</div>
                            <div style="float: left; width: 160px; padding: 4px;background-color: #D7FFF4;margin: 4px;">[[order_payment_method]]</div>
                           <div style="float: left; width: 160px; padding: 4px;background-color: #D7FFF4;margin: 4px;"> [[order_billing_first_name]]</div>
                             <div style="float: left; width: 160px; padding: 4px;background-color: #D7FFF4;margin: 4px;">[[order_billing_last_name]]</div>
                            <div style="float: left; width: 135px; padding: 4px;background-color: #D7FFF4;margin: 4px;"> [[order_billing_phone]]</div>
                            <div style="float: left; width: 130px; padding: 4px;background-color: #D7FFF4;margin: 4px;"> [[order_billing_email]]</div>
                             <div style="float: left; width: 160px; padding: 4px;background-color: #D7FFF4;margin: 4px;"> [[order_billing_company]]</div>
                             <div style="float: left; width: 160px; padding: 4px;background-color: #D7FFF4;margin: 4px;">  [[order_billing_company]]</div>
                              <div style="float: left; width: 160px; padding: 4px;background-color: #D7FFF4;margin: 4px;"> [[order_billing_address_1]]</div>
                              <div style="float: left; width: 160px; padding: 4px;background-color: #D7FFF4;margin: 4px;"> [[order_billing_address_2]]</div>
                              <div style="float: left; width: 130px; padding: 4px;background-color: #D7FFF4;margin: 4px;"> [[order_billing_city]]</div>
                            <div style="float: left; width: 130px; padding: 4px;background-color: #D7FFF4;margin: 4px;">   [[order_billing_state]]</div>
                            <div style="float: left; width: 160px; padding: 4px;background-color: #D7FFF4;margin: 4px;"> [[order_billing_postcode]]</div>
                              <div style="float: left; width: 160px; padding: 4px;background-color: #D7FFF4;margin: 4px;"> [[order_billing_country]]</div>
                             </li>
                        </ul>
                    </div>
                 <h3 class="hndle"><span>  How to use </span> </h3>
                    <div class="inside">
                        <ul style="list-style:decimal; margin-left:10px">
                          <li style="width: 340px;"> Fill the textarea with text message depend on type of order status</li>
                        </ul>
                      <!--       <span style="background-color:#ffee92;padding: 4px;margin-top:4px">echo do_shortcode('[[WBSMSContact]]'); </span> </li> -->
                </div>
            </div>
            </div>
        </div>                
            
</div> 
</div>
<!--dialog for add ne CC-->
<div id="dialog-form" title="Add country code">
<p class="validateTips">All form fields are required.</p>
<form>
<fieldset>
<label for="name">Country Code</label>
<input type="text" name="ccvalue" id="ccvalue" class="text ui-widget-content ui-corner-all" style="width: 150px !important;">
<label for="email">Country Label</label>
<input type="text" name="cclabel" id="cclabel" value="" class="text ui-widget-content ui-corner-all" style="width: 150px !important;">
</fieldset>
</form>
</div>
<!--- confirm dialog----->

<div id="dialog-confirm" title="Delete International Phone Code" style="display: none;">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>These items will be delete. Are you sure?</p>
    <br />
</div>

<!-- send test sms diaolog--->
<!--
<div id="dialog-form-testSMS" title="Send Test SMS">
<p class="validateTips">Phone number must be in international format.</p>
<form>
<fieldset>
<label for="tsPhoneNUmber">Phone number</label>
<input type="text" name="tsPhoneNUmber" id="tsPhoneNUmber" class="text ui-widget-content ui-corner-all" style="width: 300px !important;">
<label for="tsMessage">Message</label>
<textarea name="tsMessage" id="tsMessage" style="width: 300px; height: 200px; !important;"></textarea>
</fieldset>
</form>
</div> -->
    <?php
    $FORM = ob_get_contents();
    ob_end_clean();
    
   return $FORM;
    
}
function  WBSMSC_handleSubmit(){
    
    $apikey = base64_encode(trim($_POST['WB_apikey']));
    $apisecret = base64_encode(trim($_POST['WB_apisecret']));
    $recivernumber = base64_encode(trim($_POST['WB_adminNumber']));
    $ownerCostum = empty($_POST['WB_ownerCostum'])?'':$_POST['WB_ownerCostum'];
    $receivedCustom = trim($_POST['WB_receivedCustom']);
    $processingCustom = trim($_POST['WB_processingCustom']);
    $completedCustom = trim($_POST['WB_completedCustom']);
    $listId = $_POST['WB_addToList'];
    $burstcc = array();
    if(isset($_POST['burstcc']) && is_array($_POST['burstcc'])){
        foreach($_POST['burstcc'] as $key => $val){
            $arrBccTemp = explode('|',$val);
            $burstcc[$arrBccTemp[1]] = $arrBccTemp[0];        
        }
    }
    $arrSEtting = array('apikey'=>$apikey,'apisecret'=>$apisecret,'reciver_number' =>$recivernumber,
                    'ownerCostum'=>$ownerCostum,'receivedCustom'=> $receivedCustom, 'processingCustom' => $processingCustom,
                    'completedCustom' => $completedCustom,
                    'pendingCustom' => trim($_POST['WB_pendingCustom']), 'failedCustom' =>trim($_POST['WB_failedCustom']), 'onholdCustom' => trim($_POST['WB_onholdCustom']),
                    'refundedCustom' =>trim($_POST['WB_refundedCustom']), 'cancelledCustom' =>trim($_POST['WB_cancelledCustom']),
                    'list_id'=>$listId,'country_code'=>$burstcc,
                    'enaReceivedCustom' => empty($_POST['WB_enaReceivedCustom'])?'':$_POST['WB_enaReceivedCustom'], 'enaProcessingCustom' => empty($_POST['WB_enaProcessingCustom'])?'':$_POST['WB_enaProcessingCustom'],
                    'enaCompletedCustom' => empty($_POST['WB_enaCompletedCustom'])?'':$_POST['WB_enaCompletedCustom'],'enaPendingCustom' => empty($_POST['WB_enaPendingCustom'])?'':$_POST['WB_enaPendingCustom'],
                    'enaRefundedCustom' => empty($_POST['WB_enaRefundedCustom'])?'':$_POST['WB_enaRefundedCustom'],'enaCancelledCustom' => empty($_POST['WB_enaCancelledCustom'])?'':$_POST['WB_enaCancelledCustom'],
                    'enaOnholdCustom' => empty($_POST['WB_enaOnholdCustom'])?'':$_POST['WB_enaOnholdCustom'],'enaFailedCustom' =>empty($_POST['WB_enaFailedCustom'])?'':$_POST['WB_enaFailedCustom'],
                    'default_country_code' => $_POST['defburstcc'],
                    );
                    
    update_option( 'WBSmsSettings', addslashes(serialize($arrSEtting)));
}


?>