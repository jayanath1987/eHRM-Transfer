<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js') ?>"></script>
<div class="formpage2col">
    <div class="navigation">

        <?php echo message() ?>
    </div>
    <div id="status"></div>
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo __("New Transfer Request") ?></h2></div>
        <form name="frmSave" id="frmSave" method="post"  action="<?php echo url_for('transfer/WorkFlowApprove'); ?>">
            <div class="leftCol">
                <label class="controlLabel" for="txtEmployeeCode"><?php echo __("Employee No") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <input type="text" name="txtEmployeeNo"
                       id="txtEmployeeNo" class="formInputText" value="<?php echo $transferReqest->Employee->employeeId ?>" readonly="readonly"/>
            </div>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtEmployeeName"><?php echo __("Employee Name") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <input type="text" name="txtEmployee"
                       id="txtEmployee" class="formInputText" value="<?php echo $transferReqest->Employee->emp_display_name ?>" readonly="readonly"/>    
            </div>          
            <br class="clear"/>

            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Preferred Level") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <input type="text" name="txtlevel"
                       id="txtEmployee" class="formInputText" value="<?php echo $transferReqest->CompanyStructureLevels->def_name ?>" readonly="readonly"/>    

            </div>
            <br class="clear"/>
           

            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Division") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <input type="hidden" name="hiddenDivID" id="hiddenDivID" value="" readonly="readonly" />
                <input type="text" name="txtDivisionName" id="txtDivisionName" class="formInputText" readonly="readonly"
                       value="<?php echo $transferReqest->CompanyStructure->title ?>" />
            </div>

            <label for="txtLocation">
<!--                <input type="button" name="popLoc" value="..." onclick="returnLocDet()" <?php echo $disabled; ?> class="button" />-->

            </label>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Preference1") ?> </label>
            </div>
            <div class="centerCol">
                <input id="prf1"  name="prf1" type="text"  class="formInputText" value="<?php echo $transferReqest->trans_req_location_pref1 ?>"  maxlength="75" style="width: 250px;"/>
            </div>
            <br class="clear"/>
            <div class="leftCol">   
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Preference2") ?> </label>
            </div>
            <div class="centerCol">
                <input id="prf2"  name="prf2" type="text"  class="formInputText" value="<?php echo $transferReqest->trans_req_location_pref2 ?>"  maxlength="75" style="width: 250px;"/>
            </div>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Preference3") ?></label>
            </div>
            <div class="centerCol">
                <input id="prf3"  name="prf3" type="text"  class="formInputText" value="<?php echo $transferReqest->trans_req_location_pref3 ?>" maxlength="75" style="width: 250px;"/>
            </div>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtcomment"><?php echo __("Comment") ?> <span class="required"></span></label>
            </div>
            <div class="centerCol">
                <textarea class="formTextArea"  name="comment" style="width: 320px; height: 120px;"></textarea>
            </div>
             <input type="hidden" name="hiddenWfMainID" value="<?php echo $wfID ?>" />
            <input type="hidden" id="hiddenStatus" name="hiddenStatus" value="" />
            <?php if ($isAdmin == "Yes") { ?>
                <br class="clear"/>
                <div class="leftCol">
                    <label class="controlLabel" for="txtcomment"><?php echo __("Divisional Head Comment") ?> <span class="required"></span></label>
                </div>
                <div class="centerCol">
                    <textarea class="formTextArea"  name="admincomment">
                        <?php echo $transferReqest->rans_req_adminiscomment ?>
                    </textarea>
                </div>
            <?php } ?>
           

            <br class="clear"/>
            <div class="formbuttons">

                <input  type="button" class="backbutton" id="buttonRemove" onclick="submmit('1');"
                        value="<?php echo __("Approve") ?>" />


                <input type="button" class="backbutton" id="buttonRemove" onclick="submmit('0');"
                       value="<?php echo __("Reject") ?>" />
            </div>
        </form>
    </div>

</div>
<div class="requirednotice"><?php echo __("Fields marked with an asterisk") ?><span class="required"> * </span> <?php echo __("are required") ?></div>
<script type="text/javascript">
    function submmit(status){
        if(status==1){    
            $("#hiddenStatus").val("1");

        }else{
            $("#hiddenStatus").val("0");
        }
                               
        $("#frmSave").submit()
    }
  
    $(document).ready(function() {

        $("#frmSave").validate({

            rules: {
                txtComment:{maxlength:200,noSpecialCharsOnly:true }  
            },
            messages: {
                txtComment:{ maxlength: "<?php echo __("Maximum length should be 200 characters") ?>",noSpecialCharsOnly:"<?php echo __("No invalid characters are allowed") ?>"}
            }
        });

        buttonSecurityCommon(null,null,"editBtn",null);



    });
</script>
