<?php
    if($update=="Update"){
    if ($lockMode == '1') {
        $editMode = false;
        $disabled = '';
    } else {
        $editMode = true;
        $disabled = 'disabled="disabled"';
    }
    }
    $encrypt = new EncryptionHandler();
?>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery-ui.min.js') ?>"></script>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js') ?>"></script>

<script type="text/javascript" src="<?php echo public_path('../../scripts/time.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery.placeholder.js') ?>"></script>
<?php
$sysConf = OrangeConfig::getInstance()->getSysConf();
$inputDate = $sysConf->getDateInputHint();
$dateDisplayHint = $sysConf->dateDisplayHint;
$format = LocaleUtil::convertToXpDateFormat($sysConf->getDateFormat());
?>
<?php
                if ($Culture == "en") {
                    $EName = "emp_display_name";
                } else {
                    $EName = "emp_display_name_" . $Culture;
                }
                if ($transfer->Employee->$EName == null) {
                    $empName = $transfer->Employee->emp_display_name;
                } else {
                    $empName = $transfer->Employee->$EName;
                }
                ?>
<div class="formpage4col">
    <div class="navigation">

    </div>
    <div id="status"></div>
    <div class="outerbox">

        <div class="mainHeading"><h2><?php echo __("Transfer Details") ?></h2></div>

        <?php echo message() ?>


                  <?php if($update!="Update"){?>  
            <form enctype="multipart/form-data" action="<?php echo url_for('transfer/SaveTransferDetail') ?>" method="POST" id="frmEmpJob" name="frmEmpJob"  >
            <?php }else{?>
                <form enctype="multipart/form-data" action="<?php echo url_for('transfer/SaveTransferDetail?update=yes&id=' . $encrypt->encrypt($transfer->trans_id)) ?>" method="POST" id="frmEmpJob" name="frmEmpJob"  >
                    <?php }?>    
        
        <div class="leftCol">
                <label class="controlLabel" for="txtEmployeeCode"><?php echo __("Employee No") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <input type="text" name="txtEmployeeNo"
                       id="txtEmployeeNo" class="formInputText" value="<?php echo $transfer->Employee->employeeId; ?>" readonly="readonly"/>                
            </div>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Employee Name") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <input type="text" name="txtEmployee" 
                       id="txtEmployee" class="formInputText" value="<?php echo $empName; ?>" readonly="readonly"/>

                <input type="hidden" name="txtEmpId" id="txtEmpId" value="<?php echo $transfer->trans_emp_number; ?>"/>&nbsp;
            </div>

            <label for="txtLocation">
                <input class="button" type="button" value="..." id="empRepPopBtn" <?php echo $disabled; ?> />
            </label>


            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Current Division") ?><span class="required">*</span> </label>
            </div>
            <div class="centerCol">
                <input type="text" value="" class="formInputText" name="txtcurrentDivname" id="txtCurDiv" readonly="readonly" />
                <input type="hidden" name="txtcurrentDiv" id="txtcurrentDiv" value="<?php if($transfer->trans_currentdiv_id!=null){ echo $transfer->trans_currentdiv_id;} ?>" />

            </div>
            <label for="hirachy" style="width:250px;" id="hirachy">

            </label>
            <br class="clear"/>

            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Preferred Division/District/National") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <select name="cmbLevel" id="cmbLevel" class="formSelect" style="width: 150px;" tabindex="4" onchange="getLevel(this.value);" <?php echo $disabled; ?>>

                    <option value=""><?php echo __("--Select--") ?></option>
                    <?php foreach ($listLevel as $Level) {
                        ?>
                        <option value="<?php echo $Level->def_level ?>"<?php
                    if ($Level->def_level == $transfer->TransferPreferCompanyStructure->def_level)
                        echo "selected"
                            ?>><?php
                        $abc = "def_name_" . $Culture;
                        if ($Culture == "en")
                            echo $Level->def_name;
                        else
                            echo $Level->$abc;
                        ?></option>
                    <?php } ?>

                </select>
            </div>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("District/Division/National") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol" id="course">

                <select name="cmdLevel2" id="cmdLevel2" class="formSelect" style="width: 150px;" tabindex="4" <?php echo $disabled; ?>>

                    <option value=""><?php echo __("--Select--") ?></option>
                    
                    <?php if($DivList!=null) foreach ($DivList as $clist) { ?>

                        <option value="<?php echo $clist['id'] ?>" <?php if ($clist['id'] == $transfer->trans_prefer_div_id)
                        echo "selected" ?>><?php
                        if($Culture=="en"){
                            $abc = "title";
                        }else{
                            $abc = "title_" . $Culture;
                        }
                        
                        if ($clist[$abc] == "")
                            echo $clist['title']; else
                            echo $clist[$abc];
                        ?></option>
                    <?php } ?>

                </select>
            </div>
            <br class="clear"/>

            <div class="leftCol">
                <label class="controlLabel" for="txtLatterId"><?php echo __("Reference Number") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <input type="text" value="<?php echo $transfer->trans_letter_ld; ?>" class="formInputText"  name="txtLetterID" id="txtLetterID" maxlength="100" <?php echo $disabled; ?> />
            </div>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Transfer Reason") ?></label>
            </div>
            <div class="centerCol">
                <select name="reasons" id="cmbReseons" class="formSelect" style="width: 150px;" <?php echo $disabled; ?>>
                    <option value=""><?php echo __("--Select--") ?></option>
                    <?php foreach ($transResList as $transreslist) { ?>
                        <option value="<?php echo $transreslist->trans_reason_id ?>" <?php if ($transreslist->trans_reason_id == $transfer->trans_reason_id)
                        echo "selected" ?> ><?php
                    $abc = "getTrans_reason_" . $Culture;
                    if ($transreslist->$abc() == "")
                        echo $transreslist->getTrans_reason_en(); else
                        echo $transreslist->$abc();
                        ?></option>
                    <?php } ?>
                </select>
            </div>

            <br class="clear"/>



            <div class="leftCol">
                <label for="txtLocation"><?php echo __("Sub-division"); ?></label>
            </div>
           <?php if ($Culture == "en") {
                    $field = "title";
                } else {
                    $field = "title_" . $Culture;
                }
                if ($transfer->TransferCompanyStructure->$field == null) {
                    $Loc = $transfer->TransferCompanyStructure->title;
                } else {
                    $Loc = $transfer->TransferCompanyStructure->$field;
                } ?>
            <div class="centerCol">
                <input type="hidden" name="cmbLocation" id="cmbLocation" value="<?php echo $transfer->trans_div_id; ?>" readonly="readonly" />
                <input type="text" name="txtLocation" id="txtLocation" class="formInputText" readonly="readonly"
                       value="<?php  echo $Loc; ?>" />
            </div>

            <label for="txtLocation">
                <input type="button" name="popLoc" value="..." onclick="returnLocDet()" <?php echo $disabled; ?> class="button" />

            </label>


            <div id="location">
                <div class="leftCol" style="float: none;">
                    <label for="txtLocation" style="width: 140px;"><?php echo __("Location"); ?></label>
                </div>

                <div class="centerCol">
                    <input type="text" name="TLocation" id="TLocation" class="formInputText" maxlength="100" <?php echo $disabled; ?> />
                </div>
            </div>


            <br class="clear"/>

            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Mutual Transfer") ?> </label>
            </div>
            <div class="centerCol">
                <input type="checkbox" name="isMutual" id="isMutual" class="formCheckbox" value="true" <?php if ($transfer->trans_mu_name != null)
                        echo "checked" ?> <?php echo $disabled; ?> />
            </div>


            <div id="mname">
                <div class="leftCol" style="float: none;">

                    <label class="controlLabel" for="txtLocationCode" style="width: 140px;"><?php echo __("Mutual Transferred Employee Name") ?><span class="required">*</span> </label>

                </div>
                <div class="centerCol">
                <?php
                if ($Culture == "en") {
                    $EName = "emp_display_name";
                } else {
                    $EName = "emp_display_name_" . $Culture;
                }
                if ($transfer->MutualEmployee->$EName == null) {
                    $empName = $transfer->MutualEmployee->emp_display_name;
                } else {
                    $empName = $transfer->MutualEmployee->$EName;
                }
                ?>
            <input type="text" value="<?php echo $empName; ?>" readonly class="formInputText" name="MTemployee" id="MTemployee" maxlength="100" />
            <input type="hidden" name="txtMutualEmpId" id="txtMutualEmpId" value="<?php //echo $transfer->trans_mu_name; ?>"/>&nbsp;
                </div>
            <label for="txtLocation">
                <input class="button" type="button" value="..." id="empMutualRepPopBtn" <?php echo $disabled; ?> />
            </label>
            </div>
            
            <br class="clear"/>

            <div class="leftCol">
                <label class="controlLabel"  for="txtLocationCode"><?php echo __("Effective Date") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <input id="datepicker" placeholder="<?php echo $dateDisplayHint; ?>" type="text" class="formInputText" name="effdate" value="<?php echo $transfer->trans_effect_date; ?>" <?php echo $disabled; ?> />
                <div style="display: none;" class="demo-description"></div>
            </div>
            <br class="clear"/>

            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Upload Letter") ?> </label>
            </div>
            <div class="centerCol">
                <input type="file" class="formInputText" value="Upload" name="letup" <?php echo $disabled; ?> />
            </div>
            <?php
             if ($update=="Update") {

                                $encryptObj = new EncryptionHandler();
            ?> <label  style="margin-left :80px;"><a  href="#" onclick="popupimage(link='<?php echo url_for('transfer/Imagepop?id='); ?><?php echo $encryptObj->encrypt($transfer->trans_id) ?>')"><?php if (strlen($transfer->TransferAttach->getTrans_attach_name())

                                    )echo __("View"); ?></a> <?php if($disabled==''){ ?> <a id="deletelink" onclick="return deletelink();" href="<?php echo url_for('transfer/DeleteImage?id=' . $encryptObj->encrypt($transfer->getTrans_id())) ?>">  <?php if (strlen($transfer->TransferAttach->getTrans_attach_name())

                                    )echo __("Delete"); ?> </a> <?php } ?></label><?php  } ?>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Comment") ?> </label>
            </div>
            <div class="centerCol">
                <textarea class="formTextArea"  name="comment" rows="8" cols="10" <?php echo $disabled; ?> ><?php echo $transfer->trans_comment; ?></textarea>
            </div>


            <br class="clear"/>

<?php if ($update!="Update") { ?>
                <div class="formbuttons">
                    <input type="hidden" value="" class="formInputText"  name="txtcserv" id="txtcserv" />

                    <input type="submit" id="editBtn" class = "plainbtn" value="<?php echo __("Save") ?>" />
                    <input type="reset" class = "plainbtn" id="btnClearUpdate"  value="<?php echo __("Reset") ?>"/>
                 
                    <input type="button" class="backbutton" id="btnBack"
                           value="<?php echo __("Back") ?>" />
                </div>
                <?php } else{?>
        <div class="formbuttons">
            <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton'; ?>" name="EditMain" id="editBtn"
                   value="<?php echo $editMode ? __("Edit") : __("Save"); ?>"
                   title="<?php echo $editMode ? __("Edit") : __("Save"); ?>"
                   onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
            <input type="reset" class="clearbutton" id="btnClear" 
                   onmouseover="moverButton(this);" onmouseout="moutButton(this);"	<?php echo $disabled; ?>
                   value="<?php echo __("Reset"); ?>" />
            <input type="button" class="backbutton" id="btnBack"
                   value="<?php echo __("Back") ?>"  onclick="goBack();"/>
        </div>
        <?php }?>
            <input type="hidden" name="trnid" id="trnid" value="<?php echo $transfer->trans_id;?>"/>

        </form>


    </div>

</div>


<script type="text/javascript">
    // <![CDATA[
    function popupimage(link){
                                    window.open(link, "myWindow",
                                    "status = 1, height = 300, width = 300, resizable = 0" )
                                }
    function deletelink(){

//                                    if($("#recordcheck").val()=="error"){
//                                        alert("You have put Later effetcive date to this Employee befor! This record is Locked");
//                                        return false;
//                                    }
                                
                                    answer = confirm("<?php echo __("Do you really want to Delete?") ?>");

                                    if (answer !=0)
                                    {

                                        return true;

                                    }
                                    else{
                                        return false;
                                    }
                                    
                                }                            
    
    function SelectEmployee(data){

        myArr = data.split('|');
        $("#txtEmpId").val(myArr[0]);
        getEmployeeData(myArr[0])
        LoadCurrentDep();
    }
     function SelectMuEmployee(data){

        myArr = data.split('|');
        $("#txtMutualEmpId").val(myArr[0]);
        getMuEmployeeData(myArr[0]);
//        LoadCurrentDep();
    }

    function getEmployeeData(gid){
        // post(file, data, callback, type); (only "file" is required)
        $.post(

        "<?php echo url_for('loan/ajaxloadGuarantorDetails') ?>", //Ajax file
        
        { gid: gid },  // create an object will all values

        //function that is called when server returns a value.
        function(data){          
            $("#txtEmployeeNo").val(data[0]);
            //            $("#txtEmployee").val(data[3]);
            $("#txtEmployee").val(data[1] + data[2]);       

        },
        //How you want the data formated when it is returned from the server.
        "json"

    );
    }
     function getMuEmployeeData(gid){
        // post(file, data, callback, type); (only "file" is required)
        $.post(

        "<?php echo url_for('loan/ajaxloadGuarantorDetails') ?>", //Ajax file

        { gid: gid },  // create an object will all values

        //function that is called when server returns a value.
        function(data){
         
            //            $("#txtEmployee").val(data[3]);
            $("#MTemployee").val(data[1] + data[2]);

        },
        //How you want the data formated when it is returned from the server.
        "json"

    );
    }
    
    //load Level DataList to DropDown Menu
    function getLevel(lid){
        // post(file, data, callback, type); (only "file" is required)
        $.post(

        "<?php echo url_for('transfer/ajaxloadSubLevel') ?>", //Ajax file

        { lid: lid },  // create an object will all values

        //function that is called when server returns a value.
        function(data){
         
            var selectbox="<select name='cmdLevel2' id='cmdLevel2' class='formSelect' style='width: 150px;' tabindex='4'";
            selectbox=selectbox +"<option value=''><?php echo __('--Select--') ?></option>";
            $.each(data, function(key, value) {
                
                selectbox=selectbox +"<option value="+key+">"+value+"</option>";
            });
            selectbox=selectbox +"</select>";
            $('#course').html(selectbox);

        },

        //How you want the data formated when it is returned from the server.
        "json"

    );
    }

         
    //get EmployeeNumber
    function getId(eid){
 
        $.post(

        "<?php echo url_for('transfer/getEmployeeId') ?>", //Ajax file

        { eid : eid },  // create an object will all values

        //function that is called when server returns a value.
        function(data){
            $("#txtEmployeeNo").val(data);
        },

        "json"

    );        
    }  
      
    //get Employee Current Department
    function LoadCurrentDep(){                                

        sendValue($("#txtEmpId").val());
    }

    //get Employee Current Department and hirachy Level
    function sendValue(str){
        // post(file, data, callback, type); (only "file" is required)
        $.post(

        "<?php echo url_for('transfer/ajaxCall') ?>", //Ajax file

        { sendValue: str },  // create an object will all values

        //function that is called when server returns a value.
        function(data){
            //$('#display').html(data.returnValue);

            if(data!=null){
                $("#txtCurDiv").val(data.Workstation);
            }
            $("#txtcurrentDiv").val(data.WorkstationId);
            //$('#hirachy').html(data.myval);

        },

        //How you want the data formated when it is returned from the server.
        "json"
    );

    }
    
    //date Validation
    function dateAjaxValidation(str,eid){

        // post(file, data, callback, type); (only "file" is required)
        $.post(

        "<?php echo url_for('transfer/DateValidation') ?>", //Ajax file

        { sendValue: str, empId:eid},  // create an object will all values

        //function that is called when server returns a value.
        function(data){                                 

            $("#datehiddne").val(data.message);


        },

        //How you want the data formated when it is returned from the server.
        "json"
    );

    }
    $(document).ready(function() {

        $("#datepicker").placeholder();
        buttonSecurityCommon(null,"editBtn",null,null);
        LoadCurrentDep();
        
<?php
    if($update=="Update"){ ?>
        sendValue("<?php echo $transfer->trans_emp_number; ?>");
   <?php      if ($editMode == true) { ?>
                                                $('#frmSave :input').attr('disabled', true);
                                                $('#editBtn').removeAttr('disabled');
                                                $('#btnBack').removeAttr('disabled');
                                    <?php } ?>
                                        
    <?php } ?>                                    


        $("#frmEmpJob").data('edit', <?php echo $editMode ? '1' : '0' ?>);
        $("#editBtn").click(function() {

            var editMode = $("#frmEmpJob").data('edit');
            
            if (editMode == 1) {

                // Set lock = 1 when requesting a table lock
                
                location.href="<?php echo url_for('transfer/SaveTransferDetail?update=yes&id=' . $encrypt->encrypt($transfer->trans_id) . '&lock=1') ?>";

        }
            else {

                $('#frmEmpJob').submit();

            }


        });


        $("#btnBack").click(function() {
            location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/TransferDetail')) ?>";
        });


        if($("#cmbReseons").val==2){
                                   
            $("#location").show();
        }
        else{
                                  
            $("#location").hide();
        }

        $("#cmbReseons").change(function () {
            var src = $("option:selected", this).val();
            if(src==2){
                                       
                $("#location").show();
            }
            else{
                                       
                $("#location").hide();
            }
        });

        //load Employee selection popup menu
        $('#empRepPopBtn').click(function() {

            var popup=window.open('<?php echo public_path('../../symfony/web/index.php/pim/searchEmployee?type=single&method=SelectEmployee'); ?>','Locations','height=450,width=800,resizable=1,scrollbars=1');

            //var popup=window.open('<?php echo public_path('../../templates/hrfunct/emppop.php?reqcode=REP&transfer=1'); ?>','Locations','height=450,width=800,resizable=1,scrollbars=1');
            if(!popup.opener) popup.opener=self;
            popup.focus();
        });
        //load Employee selection popup menu
        $('#empMutualRepPopBtn').click(function() {

            var popup=window.open('<?php echo public_path('../../symfony/web/index.php/pim/searchEmployee?type=single&method=SelectMuEmployee'); ?>','Locations','height=450,width=800,resizable=1,scrollbars=1');

            //var popup=window.open('<?php echo public_path('../../templates/hrfunct/emppop.php?reqcode=REP&transfer=1'); ?>','Locations','height=450,width=800,resizable=1,scrollbars=1');
            if(!popup.opener) popup.opener=self;
            popup.focus();
        });


        // var data	= <?php echo str_replace('&#039;', "'", $empJson) ?> ;

        $("#datepicker").datepicker({ dateFormat: '<?php echo $inputDate; ?>' });

        $("#datepicker").change(function () {
            dateAjaxValidation($("#datepicker").val(),$("#txtEmpId").val());
        });                         

        if ($(('input#isMutual')).attr("checked")) {
            $('input#MTemployee').removeAttr('disabled');
            $("#mname").show();
        }
        else{
            $('input#MTemployee').attr('disabled', true);
            $("#mname").hide();
        }

                               


        $('input#isMutual').change(function () {
            if ($(this).attr("checked")) {
                //do the stuff that you would do when 'checked'
                $("#mname").show();
                $('#MTemployee').removeAttr('disabled');

                return;
            }else{
                $("#mname").hide();
                $('#MTemployee').attr('disabled', true);
            }
            //Here do the stuff you want to do when 'unchecked'
        });

        jQuery.validator.addMethod("validateDate",
        function(value, element, params) {

            if($("#datehiddne").val()=="error1"){

                return false;
            }
            return true;

        }, ""
    );
        jQuery.validator.addMethod("validateJoinDate",
        function(value, element, params) {

            if($("#datehiddne").val()=="error"){

                return false;
            }
            return true;

        }, ""
    );

        jQuery.validator.addMethod("orange_date",
        function(value, element, params) {

            //var hint = params[0];
            var format = params[0];

            // date is not required
            if (value == '') {

                return true;
            }
            var d = strToDate(value, "<?php echo $format ?>");


            return (d != false);

        }, ""
    );


        //Validate the form
        $("#frmEmpJob").validate({

            rules: {
                txtEmployeeNo:{required:true},
                comment:{maxlength: 200,noSpecialCharsOnly: true},
                TLocation:{maxlength: 100,noSpecialCharsOnly: true},
                MTemployee:{required: true,maxlength: 100,noSpecialCharsOnly: true},
                txtLetterID: {required: true,maxlength: 50,noSpecialCharsOnly: true},
                txtEmployee:{required:true},
                cmbLevel:{required:true},
                cmbcourseid:{required:true},                                     
                effdate: { required: true,validateDate: true,validateJoinDate:true,orange_date:true },
                // reasons: { required: true },
                // txtLocation: { required: true },
                txtcurrentDivname: {required: true}

            },
            messages: {
                txtEmployeeNo: "<?php echo __('This field is required') ?>",
                comment:{maxlength: "<?php echo __('Maximum length should be 200 characters') ?>",noSpecialCharsOnly: "<?php echo __('No invalid characters are allowed') ?>"},
                TLocation: {maxlength: "<?php echo __('Maximum length should be 100 characters') ?>",noSpecialCharsOnly: "<?php echo __('No invalid characters are allowed') ?>"},
                MTemployee:{required: "<?php echo __('This field is required') ?>",maxlength: "<?php echo __('Maximum length should be 100 characters') ?>",noSpecialCharsOnly: "<?php echo __('No invalid characters are allowed') ?>"},
                txtLetterID: {required: "<?php echo __('This field is required') ?>",maxlength: "<?php echo __('Maximum length should be 50 characters') ?>",noSpecialCharsOnly: "<?php echo __('No invalid characters are allowed') ?>"},
                txtEmployee: {required: "<?php echo __('This field is required') ?>"},                                        
                effdate: {required:"<?php echo __('This field is required') ?>",validateDate:"<?php echo __('Greater Effective date Exist for this User') ?>",validateJoinDate:"<?php echo __('Effective Date Should be greater than the commencement date') ?>",orange_date: "<?php echo __("Please specify valid  date"); ?>"},
                cmbLevel: "<?php echo __('This field is required') ?>",
                cmbcourseid: "<?php echo __('This field is required') ?>",
                txtcurrentDivname:  "<?php echo __('This field is required') ?>"
                                        
            }
        });

        $("#btnClearUpdate").click(function() {

             location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/SaveTransferDetail')) ?>";
        });

        //When click reset buton
        $("#btnClear").click(function() {
            //document.forms[0].reset('');

            location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/SaveTransferDetail')) ?>";
        });

        //When Click back button
        $("#btnBack").click(function() {
            location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/TransferDetail')) ?>";
        });

    });

    function returnLocDet(){

        // TODO: Point to converted location popup
        var popup=window.open('<?php echo public_path('../../symfony/web/index.php/admin/listCompanyStructure?mode=select_subunit&method=mymethod'); ?>','Locations','height=450,resizable=1,scrollbars=1');
        if(!popup.opener) popup.opener=self;
    }
    function mymethod($id,$name){

        //alert('test');
        $("#cmbLocation").val($id);
        $("#txtLocation").val($name);

    }
    // ]]>
</script>
