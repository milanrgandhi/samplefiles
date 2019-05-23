<?php $this->load->view("include_files/inc-headtags.php"); ?>

<body class="new page-register">

<div class="content-area-bar">
  <section class="content-area w cf">
  <div class="login-box">
  <a href="<?php echo site_url();?>"><span class="logo"><img src="<?php echo CDN_STATIC_PATH_URL;?>images/stay-square-accommodation-logo.svg" alt="Stay Square Accommodations Logo"></span></a>
  <h1 class="heading">Create an account</h1>
  <form method="post" action="<?php echo site_url('register/');?>" name="propertyfrm1" id="propertyfrm1" class="cmxform" >
    <div class="col1">
      <div class="content">

          <div class="gr">
            <div class="gc-12">
              <div class="ih">
                <label>Email Address</label>
                <input type="text" class="ip" name="emailid" value="<?php echo set_value('emailid'); ?>" id="emailid" required >
                <label class="error"><?php echo form_error('emailid'); ?></label>
                <span id="Loading"><img src="<?php echo CDN_STATIC_PATH_URL;?>images/ajax-loader-new.gif" alt="Please wait...searching..." /></span> 
              
              </div>
            </div>
            <div class="gc-12">
              <div class="ih">
                <label>Password</label>
                <input type="password" class="ip" name="pwd" value="<?php echo set_value('pwd'); ?>" id="pwd" required >
                <label class="error"><?php echo form_error('pwd'); ?></label>
              </div>
            </div>
          </div>

            <div class="gr">
              <div class="gc-12">
                <div class="ih">
                  <label>First Name</label>
                  <input type="text" class="ip" name="firstname" value="<?php echo set_value('firstname'); ?>" id="firstname" required >
                  <label class="error"><?php echo form_error('firstname'); ?></label>
                </div>
              </div>
              <div class="gc-12">
                <div class="ih">
                  <label>Last Name</label>
                  <input type="text" class="ip" name="lastname" value="<?php echo set_value('lastname'); ?>" id="lastname" required>
                  <label class="error"><?php echo form_error('lastname'); ?></label>
                </div>
              </div>
            </div>
            <!-- gr -->
            
            <div class="gr">
              <div class="gc-12">
                <div class="ih">
                  <label>Phone</label>
                  <input type="text" class="ip" id="phone" value="<?php echo set_value('phone'); ?>" name="phone" required >
                  <label class="error"><?php echo form_error('phone'); ?></label>
                </div>
              </div>
              <div class="gc-12">
                <div class="ih">
                  <label>Country</label>
                  <?php $selcountry = $this->country_model->get_all_country();
                 $cnt_country = count($selcountry); 
                 ?>
                  <select class="csb" name="selcountry" id="selcountry" >
                    <option value="">Select</option>
                    <?php for($p = 0; $p<$cnt_country; $p++) {
                    if($selcountry[$p]['id'] == 13) { echo $sel = 'selected'; } else { $sel = ''; } 
                  ?>
                    <option value="<?php echo $selcountry[$p]['id']; ?>" <?php echo $sel; ?>><?php echo $selcountry[$p]['country_name']; ?></option>
                    <?php } ?>
                  </select>
                  <label class="error"><?php echo form_error('selcountry'); ?></label>
                </div>
              </div>
            </div>
            <!-- gr -->
            
            <div class="gr">
              <div class="gc-1">
                <div class="ih">
                  <label>Company Name</label>
                  <input type="text" class="ip" name="company_name" value="<?php echo set_value('company_name'); ?>" id="company_name">
                </div>
              </div>
            </div>
            <!-- gr -->
            
            <p class="agreement">By clicking Save and Continue, you agree to Stay Square’s standard <a href="<?php echo site_url('terms-and-conditions');?>" target="_blank">Terms and Conditions</a>.</p>
 

        
      </div>
    </div>
    <div class="bh"> 
      <button class="btn l" type="submit" name="btnSubmit1">Create an account</button>
    </div>
    </div>
    <!-- col1 -->
  </form>
  <div class="col2"> </div>
</div>
</section>
</div>

<?php $this->load->view("include_files/inc-footer-scripts.php"); ?>
<script>

/* Check username(Email) availability */ 

$(document).ready(function() {
  /// make loader hidden in start
  $('#Loading').hide();    

  $('#emailid').blur(function(){
    
          var a = $("#emailid").val();
          var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
             // check if email is valid
          if(filter.test(a)){
            // show loader 
            $('#Loading').show();
            $.post("<?php echo site_url()?>free_property_listing/check_email_availablity", {
              email: $('#emailid').val()
            }, function(response){
              //#emailInfo is a span which will show you message
              $('#Loading').hide();
              setTimeout("finishAjax('Loading', '"+escape(response)+"')", 400);
            });
            return false;
          }else{
            
            var response = "<span style='color:#f00' id='notficationEmail'>Invalid Email</span>";
            setTimeout("finishAjax('Loading', '"+escape(response)+"')", 400);
            
          }
     
  });
});



</script>
</body>
</html>