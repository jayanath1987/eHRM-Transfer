<?php
if ($lockMode == '1') {
    $editMode = false;
    $disabled = '';
} else {
    $editMode = true;
    $disabled = 'disabled="disabled"';
}
?>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery-ui.min.js') ?>"></script>
<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery-ui.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js') ?>"></script>
<link href="../../themes/orange/css/style.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/time.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery.placeholder.js') ?>"></script>
<?php

                    $sysConf = OrangeConfig::getInstance()->getSysConf();
                    $inputDate = $sysConf->getDateInputHint();
                    $dateDisplayHint = $sysConf->dateDisplayHint;
                    $format = LocaleUtil::convertToXpDateFormat($sysConf->getDateFormat());
?>


<div class="formpage4col">
    <div class="navigation">

        <?php echo message() ?>

    </div>
    <div id="status"></div>
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo __("Update Transfer") ?></h2></div>
        <form enctype="multipart/form-data" action="" method="POST" id="frmEmpJob" name="frmEmpJob">
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Employee Name") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <?php
                if ($culture == "en") {
                    $EName = "getEmp_display_name";
                } else {
                    $EName = "getEmp_display_name_" . $culture;
                }
                if ($transfer->Employee->$EName() == null) {
                    $empName = $transfer->Employee->getEmp_display_name();
                } else {
                    $empName = $transfer->Employee->$EName();
                }
                ?>
                <input type="text" name="txtEmployee"
                       id="txtEmployee" class="formInputText" value="<?php echo $empName; ?>" readonly="readonly" />
                <input type="hidden" name="txtEmpId" id="txtEmpId" value="<?php echo $transfer->getTrans_emp_number() ?>"/>&nbsp;
        <!--        <input class="button" type="button" value="..." id="empRepPopBtn" <?php echo $disabled; ?> />-->
            </div>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Current Division") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <?php
                if ($culture == "en") {
                    $unit = "title";
                } else {
                    $unit = "title_" . $culture;
                }
                if ($transfer->Employee->subDivision->$unit == null) {
                    $unitName = $transfer->Employee->subDivision->title;
                } else {
                    $unitName = $transfer->Employee->subDivision->$unit;
                }
                ?>
                <input type="text" value="<?php echo $unitName; ?>" class="formInputText" name="txtcurrentDivname" id="txtCurDiv" readonly="readonly" />
                <input type="hidden" name="txtcurrentDiv" id="txtcurrentDivid" value="<?php echo $transfer->getTrans_currentdiv_id(); ?>">
                <input type="hidden" name="transID" id="transID" value="<?php echo $transfer->getTrans_id(); ?>">
            </div>
            <label for="hirachy" style="width:250px;" id="hirachy">
                <?php echo $p ?>
            </label>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtLatterId"><?php echo __("Reference Number") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <input type="text" value="<?php echo $transfer->getTrans_letter_ld(); ?>" class="formInputText" name="txtLetterID" id="txtLetterID" maxlength="100" <?php echo $disabled; ?> />
            </div>
            <br class="clear"/>

            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Transfer Reason") ?> </label>
            </div>
            <div class="centerCol">
                <select name="reasons" id="cmbReseons" class="formSelect" style="width: 150px;" <?php echo $disabled; ?>>
                    <option value="">--Select--</option>
                    <?php foreach ($transResList as $transreslist) {
                    ?>
                        <option value="<?php echo $transreslist->getTrans_reason_id() ?>"<?php if ($transreslist->getTrans_reason_id() == $transfer->getTrans_reason_id())
                            echo "selected" ?>><?php
                            $abc = "getTrans_reason_" . $culture;
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
                <div class="centerCol">
                    <input type="hidden" name="cmbLocation" id="cmbLocation" value="<?php echo $transfer->getTrans_div_id(); ?>" readonly="readonly" />
                    <input type="text" name="txtLocation" id="txtLocation" class="formInputText" readonly="readonly"
                           value="<?php
                        if ($culture == "en") {
                            echo $transfer->CompanyStructure->getTitle();
                        } else {
                            $abc = "getTitle_" . $culture;
                            echo $transfer->CompanyStructure->$abc();
                        }
                    ?>" />
             </div>
             <label for="txtLocation">
                 <input type="button" name="popLoc" value="..." onclick="returnLocDet()" <?php echo $disabled; ?> class="button" <?php echo $disabled; ?> />
             </label>
             <br class="clear" />
             <div id="location">
                 <div class="leftCol">
                     <label for="txtLocation"><?php echo __("Location"); ?></label>
                 </div>
                 <div class="centerCol">
                     <input type="text" name="TLocation" id="TLocation" value="<?php echo $transfer->getTrans_location() ?>" class="formInputText" maxlength="100" <?php echo $disabled; ?>/>
                 </div>
                 <br class="clear"/>
             </div>
             <div class="leftCol">
                 <label class="controlLabel" for="txtLocationCode"><?php echo __("Mutual Transfer") ?> </label>
             </div>
             <div class="centerCol">
                 <input type="checkbox"  name="isMutual" id="isMutual" class="formCheckbox" value="true" <?php if ($transfer->getTrans_mutual() == "true"

                            )echo "checked" ?> <?php echo $disabled; ?>/>
                 </div>
                 <br class="clear"/>

                 <div id="mname">
                     <div class="leftCol">
                         <label class="controlLabel" for="txtLocationCode"><?php echo __("Mutual Transferred Employee Name") ?> <span class="required">*</span></label>
                     </div>
                     <div class="centerCol">
                         <input type="text" value="<?php echo $transfer->getTrans_mu_name() ?>" class="formInputText" name="MTemployee" id="MTemployee" maxlength="100" <?php echo $disabled; ?> />
                     </div>
                     <br class="clear"/>
                 </div>
                 <div class="leftCol">
                     <label class="controlLabel" for="txtLocationCode"><?php echo __("Effective Date") ?><span class="required">*</span></label>
                 </div>
                 <div class="centerCol">
                     <input id="datepicker" placeholder="<?php echo  $dateDisplayHint; ?>" class="formInputText" type="text" name="effdate" value="<?php echo LocaleUtil::getInstance()->formatDate($transfer->getTrans_effect_date()) ?>" <?php echo $disabled; ?>>

                     <div style="display: none;" class="demo-description"></div>
                 </div>

                 <br class="clear"/>
                 <div class="leftCol">
                     <label class="controlLabel" for="txtLocationCode"><?php echo __("Upload Letter") ?></label>
                 </div>
                 <div class="centerCol">
                     <input type="file" class="formInputText" value="Upload" name="letup" <?php echo $disabled; ?> >
                 </div>


            <?php
                            if (!$editMode) {

                                $encryptObj = new EncryptionHandler();
            ?> <label  style="margin-left :80px;"><a href="#" onclick="popupimage(link='<?php echo url_for('Transfer/imagepop?id='); ?><?php echo $encryptObj->encrypt($transfer->TransferAttach->getTrans_attach_id()) ?>')"><?php if (strlen($transfer->TransferAttach->getTrans_attach_name())

                                    )echo __("View"); ?></a>  <a id="deletelink" onclick="return deletelink();" href="<?php echo url_for('Transfer/Deleteimage?id=' . $transfer->getTrans_id()) ?>">  <?php if (strlen($transfer->TransferAttach->getTrans_attach_name())

                                    )echo __("Delete"); ?> </a></label><?php } ?>
                                              <!--<img src="http://localhost/commonhrm/symfony/web/images/image.php?image_id=59"/>-->

                <br class="clear"/>
                <div class="leftCol">
                    <label  class="controlLabel" for="txtLocationCode"><?php echo __("Comment") ?> </label>
                </div>
                <div class="centerCol">
                    <textarea class="formTextArea" name="comment" style="width:350px; height: 90px;" <?php echo $disabled; ?>><?php echo $transfer->getTrans_comment(); ?></textarea>

                </div>
                <br class="clear"/>


                <div class="formbuttons">
                    <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton'; ?>" name="EditMain" id="editBtn"
                           value="<?php echo $editMode ? __("Edit") : __("Save"); ?>"
                           title="<?php echo $editMode ? __("Edit") : __("Save"); ?>"
                           onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                    <input type="reset" class="clearbutton" id="btnClear"
                           onmouseover="moverButton(this);" onmouseout="moutButton(this);"	<?php echo $disabled; ?>
                           value="<?php echo __("Reset"); ?>" />
                    <input type="button" class="backbutton" id="btnBack"
                           value="<?php echo __("Back") ?>" />
                </div>

                                                                 <!-- <input type="button" value="New" class = "plainbtn" onclick="window.location.href='<?php echo url_for('transfer/new'); ?>'">!-->




                <input type="hidden"  id="datehiddne1" value=""/>
                <input type="hidden" name="datehiddne" id="datehiddne" value=""/>
                <input type="hidden" name="recordcheck" id="recordcheck" value=""/>

            </form>


        </div>

    </div>
<?php
                            require_once '../../lib/common/LocaleUtil.php';
                            $sysConf = OrangeConfig::getInstance()->getSysConf();
                            $sysConf = new sysConf();
                            $inputDate = $sysConf->dateInputHint;
                            $format = LocaleUtil::convertToXpDateFormat($sysConf->getDateFormat());

//$format=$sysConf->dateFormat;
?>

                            <script type="text/javascript">

                                function SelectEmployee(data){
                                    //alert(data);

                                    myArr = data.split('|');
                                    $("#txtEmpId").val(myArr[0]);
                                    $("#txtEmployee").val(myArr[1]);
                                    LoadCurrentDep();
                                }



                                function deletelink(){

                                    if($("#recordcheck").val()=="error"){
                                        alert("You have put Later effetcive date to this Employee befor! This record is Locked");
                                        return false;
                                    }

                                    answer = confirm("<?php echo __("Do you really want to Delete?") ?>");

                                    if (answer !=0)
                                    {

                                        return true;

                                    }
                                    else{
                                        return false;
                                    }

                                }

                                function popupimage(link){
                                    window.open(link, "myWindow",
                                    "status = 1, height = 300, width = 300, resizable = 0" )
                                }

                                function formValidation(){
                                    if($("#datepicker").val()==""){
                                        alert("Please Enter Date");
                                        return false;
                                    }
                                    if($("#cmbLocation").val()==""){
                                        alert("please Select the Transferd Division");
                                        return false;
                                    }
                                    if($("#txtEmpId").val()==""){
                                        alert("please Select the Employee Name");
                                        return false;
                                    }
                                }

                                function LoadCurrentDep(){
                                    //alert($("#txtEmpId").val());

                                    sendValue($("#txtEmpId").val());
                                }

                                function sendValue(str){

                                    // post(file, data, callback, type); (only "file" is required)
                                    $.post(

                                    "<?php echo url_for('Transfer/AjaxCall') ?>", //Ajax file

                                    { sendValue: str },  // create an object will all values

                                    //function that is called when server returns a value.
                                    function(data){
                                        //$('#display').html(data.returnValue);

                                        $("#txtCurDiv").val(data.returnValue);
                                        $("#txtcurrentDivid").val(data.id);
                                        $('#hirachy').html(data.myval);

                                    },

                                    //How you want the data formated when it is returned from the server.
                                    "json"
                                );

                                }
                                function recordcheck(eid,date){


                                    // post(file, data, callback, type); (only "file" is required)
                                    $.post(

                                    "<?php echo url_for('Transfer/RecordCheck') ?>", //Ajax file

                                    { empid: eid, Date: date},  // create an object will all values

                                    //function that is called when server returns a value.
                                    function(data){
                                        //alert(data.message);
                                        $("#recordcheck").val(data.message);


                                        //$("#datehiddne1").val(data.message);
                                    },

                                    //How you want the data formated when it is returned from the server.
                                    "json"

                                );

                                }




                                function dateAjaxValidation(str,eid){

                                    // post(file, data, callback, type); (only "file" is required)
                                    $.post(

                                    "<?php echo url_for('Transfer/DateValidation') ?>", //Ajax file

                                    { sendValue: str, empId:eid},  // create an object will all values

                                    //function that is called when server returns a value.
                                    function(data){
                                        //$('#display').html(data.returnValue);

                                        $("#datehiddne").val(data.message);


                                    },

                                    //How you want the data formated when it is returned from the server.
                                    "json"
                                );

                                }

                                function formValidation(){
                                    if($("#datepicker").val=="")
                                    {
                                        alert("Please Enter Effective Date");
                                        return false;
                                    }
                                }

                                $(document).ready(function() {

                                    buttonSecurityCommon(null,null,"editBtn",null);
                                    $("#datepicker").placeholder();
                                    var datevalue=$("#datepicker").val();
                                    //alert(datevalue);
                                    $("#datehiddne").val(datevalue);

                                    // alert($("#datehiddne").val());

                                    // When click edit button
                                    ////////////////////////////////////////////////////////////////////////////////////////////////

                                    recordcheck($("#txtEmpId").val(),$("#datepicker").val());

                                    // When click edit button
                                    $("#frmEmpJob").data('edit', <?php echo $editMode ? '1' : '0' ?>);

                                    $("#editBtn").click(function() {

                                        var editMode = $("#frmEmpJob").data('edit');
                                        if (editMode == 1) {
                                            recordcheck($("#txtEmpId").val(),$("#datepicker").val());
                                            if($("#recordcheck").val()=="error"){
                                                alert("You have put Later effetcive date to this Employee befor! This record is Locked");
                                                return false;
                                            }
                                            // Set lock = 1 when requesting a table lock
                                            location.href="<?php echo url_for('transfer/UpdateTransfer?id=' . $transferId . '&lock=1') ?>";
                                        }
                                        else {

                                            $('#frmEmpJob').submit();

                                        }


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
                                            comment:{maxlength: 200,noSpecialChars: true},
                                            TLocation:{maxlength: 100,noSpecialChars: true},
                                            MTemployee:{required:true,maxlength: 100,noSpecialChars: true},
                                            txtLetterID: {maxlength: 50,noSpecialCharsOnly: true},
                                            txtEmployee:{required:true},
                                            transfertype:{required:true},
                                            txtcurrentDivname: {required: true},
                                            effdate: { required: true,validateDate: true,validateJoinDate:true,orange_date:true }
                                            //reasons: { required: true },
                                            //txtLocation: { required: true }
                                        },
                                        messages: {
                                            comment:{maxlength: "<?php echo __('Maximum length should be 200 characters') ?>",noSpecialChars: "<?php echo __('No invalid characters are allowed') ?>"},
                                            TLocation: {maxlength: "<?php echo __('Maximum length should be 100 characters') ?>",noSpecialChars: "<?php echo __('No invalid characters are allowed') ?>"},
                                            MTemployee: {required:"<?php echo __('This field is required') ?>",maxlength: "<?php echo __('Maximum length should be 100 characters') ?>",noSpecialChars: "<?php echo __('No invalid characters are allowed') ?>"},
                                            txtLetterID: {maxlength: "<?php echo __('Maximum length should be 50 characters') ?>",noSpecialCharsOnly: "<?php echo __('No invalid characters are allowed') ?>"},
                                            txtEmployee: {required: "<?php echo __('This field is required') ?>"},
                                            transfertype: {required: "<?php echo __('This field is required') ?>"},
                                            txtcurrentDivname:  "<?php echo __('This field is required') ?>",
                                            effdate: {required:"<?php echo __('This field is required') ?>",validateDate:"<?php echo __('Greater Effective date Exist for this User') ?>",validateJoinDate:"<?php echo __('Effective Date Should be greater than the commencement date') ?>",orange_date: "<?php echo __("Please specify valid  date"); ?>"}
                                            //reasons: "<?php echo __('This field is required') ?>",
                                            //txtLocation: "<?php echo __('This field is required') ?>"


                                        }
                                    });






                                    $("#btnBack").click(function() {
                                        location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/Transfer/NewTransfer')) ?>";
                                    });
                                    $("#btnClear").click(function() {

                                        // Set lock = 0 when resetting table lock
                                        location.href="<?php echo url_for('Transfer/UpdateTransfer?id=' . $transfer->getTrans_id() . '&lock=0') ?>";
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
                                    $('#empRepPopBtn').click(function() {
                                        //var popup=window.open('<?php echo public_path('../../templates/hrfunct/emppop.php?reqcode=REP&transfer=1'); ?>','Locations','height=450,width=800,resizable=1,scrollbars=1');
                                        var popup=window.open('<?php echo public_path('../../symfony/web/index.php/pim/searchEmployee?type=single&method=SelectEmployee'); ?>','Locations','height=450,width=800,resizable=1,scrollbars=1');
                                        if(!popup.opener) popup.opener=self;
                                        popup.focus();
                                    });


                                    // var data	= <?php echo str_replace('&#039;', "'", $empJson) ?> ;

                                    //$("input#txtEmployee").change(alert("change"));


                                    $("#datepicker").datepicker({ dateFormat: '<?php echo $inputDate; ?>'});
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


                                });

                                function returnLocDet(){

                                    // TODO: Point to converted location popup
                                    var popup=window.open('<?php echo public_path('../../symfony/web/index.php/admin/listCompanyStructure?mode=select_subunit&method=mymethod'); ?>','Locations','height=450,resizable=1,scrollbars=1');
        if(!popup.opener) popup.opener=self;
    }
    function mymethod($id,$name){


        $("#cmbLocation").val($id);
        $("#txtLocation").val($name);

    }

    //When click reset buton

</script>
