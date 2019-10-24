<?php
if ($mode == '1') {
    $editMode = false;
    $disabled = '';
} else {
    $editMode = true;
    $disabled = 'disabled="disabled"';
}
$encrypt = new EncryptionHandler();
?>
<?php
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';
?>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery-ui.min.js') ?>"></script>
<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery-ui.css') ?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/time.js') ?>"></script>
<div class="formpage4col" >
    <div class="outerbox">
            <div class="mainHeading"><h2><?php echo __("Transfer Reason") ?></h2></div>
            <?php echo message(); ?>
        <form name="frmSave" id="frmSave" method="post"  action="">
            <div class="leftCol">
                &nbsp;
            </div>
            <div class="centerCol">
                <label class="languageBar"><?php echo __("English") ?></label>
            </div>
            <div class="centerCol">
                <label class="languageBar"><?php echo __("Sinhala") ?></label>
            </div>
            <div class="centerCol">
                <label class="languageBar"><?php echo __("Tamil") ?></label>
            </div>
            <br class="clear"/>
            <div class="leftCol">
                <label for="txtLocationCode"><?php echo __("Transfer Reason") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <input id="txtReason"  name="txtReason" type="text"  class="formInputText" value="<?php echo $transferReasonGetById->trans_reason_en ?>" maxlength="100" />
            </div>

            <div class="centerCol">
                <input id="txtReasonsi"  name="txtReasonsi" type="text"  class="formInputText" value="<?php echo $transferReasonGetById->trans_reason_si ?>" maxlength="100" />
            </div>
            <div class="centerCol">

                <input id="txtReasonta"  name="txtReasonta" type="text"  class="formInputText" value="<?php echo $transferReasonGetById->trans_reason_ta ?>" maxlength="100" />
                <input id="txtTransferReasonCode"  name="txtTransferReasonCode" type="hidden"  class="inputText" value="<?php echo $transferReasonGetById->trans_reason_id; ?>" maxlength="100" />          
            </div>
            <br class="clear"/>
            <div class="formbuttons">
                <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton'; ?>" name="EditMain" id="editBtn"
                       value="<?php echo $editMode ? __("Edit") : __("Save"); ?>"
                       title="<?php echo $editMode ? __("Edit") : __("Save"); ?>"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                <input type="reset" class="clearbutton" id="btnClear" tabindex="5"
                       onmouseover="moverButton(this);" onmouseout="moutButton(this);"<?php echo $disabled; ?>
                       value="<?php echo __("Reset"); ?>" />
                <input type="button" class="backbutton" id="btnBack"
                       value="<?php echo __("Back") ?>" tabindex="10" />
            </div>
        </form>
    </div>
    <div class="requirednotice"><?php echo __("Fields marked with an asterisk") ?><span class="required"> * </span> <?php echo __("are required") ?></div>
    <br class="clear" />
</div>


<script type="text/javascript">

    function validateYear(type) {        
        $(type).change(function() {
           
            if(this.value < <?php echo date("Y"); ?> ){
                alert("Please enter valied year.");  
            }
       
            if(this.value )
            {
                  
            }
            if(isNaN(this.value)){
                $(this).val("");
                alert("Please Enter Numeric Values");
                return false;
            }
        });
    }
    
    $(document).ready(function() {
        validateYear('input[name="cmbYear"]');

        buttonSecurityCommon(null,"editBtn",null,null);    
<?php if ($mode == 0) { ?>
            $('#editBtn').show();
            buttonSecurityCommon(null,null,"editBtn",null);

            $('#frmSave :input').attr('disabled', true);
            $('#editBtn').removeAttr('disabled');
            $('#btnBack').removeAttr('disabled');
<?php } ?>

        //Validate the form
        $("#frmSave").validate({

            rules: {
                txtReason:{required: true,maxlength:50},
                txtReasonsi: {noSpecialCharsOnly: false, maxlength:50 },
                txtReasonta: {noSpecialCharsOnly: false, maxlength:50 }
            },
            messages: {
                txtReason:{required:"<?php echo __("This field is required") ?>",maxlength:"<?php echo __("Maximum 50 Characters") ?>"},
                txtReasonsi:{maxlength:"<?php echo __("Maximum 50 Characters") ?>"},
                txtReasonta:{maxlength:"<?php echo __("Maximum 50 Characters") ?>"}                              
            },submitHandler: function(form) {
                $('#editBtn').unbind('click').click(function() {return false}).val("<?php echo __('Wait..'); ?>");
                form.submit();
            }
        });


        // When click edit button
        $("#frmSave").data('edit', <?php echo $editMode ? '1' : '0' ?>);

        // When click edit button
        $("#editBtn").click(function() {
            var editMode = $("#frmSave").data('edit');
            if (editMode == 1) {
                // Set lock = 1 when requesting a table lock

                location.href="<?php echo url_for('transfer/SaveTransferReason?id=' . $encrypt->encrypt($transferReasonGetById->trans_reason_id) . '&lock=1') ?>";
            }
            else {

                $('#frmSave').submit();
            }
        });

        //When click reset buton
        $("#btnClear").click(function() {
            if($("#frmSave").data('edit') != 1){
                location.href="<?php echo url_for('transfer/SaveTransferReason?id=' . $encrypt->encrypt($transferReasonGetById->trans_reason_id) . '&lock=0') ?>";
            }else{
                document.forms[0].reset('');
            }
        });

        //When Click back button
        $("#btnBack").click(function() {
            location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/TransferReason')) ?>";
        });

    });
</script>



