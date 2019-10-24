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
    <div class="navigation">
        <style type="text/css">
            div.formpage4col input[type="text"]{
                width: 180px;
            }
        </style>
    </div>
    <div id="status"></div>
    <div id="emp_details">
        <table style="background-color:white; border: 2px solid #FAD163; width: 98%; margin-left: 15px; margin-bottom: 3px;">
            <tr>
                <td rowspan="2">

                    <span id="Currentimage">
                        <img id="currentImage" style="width:50px; height:50px; padding-left:5px; padding-bottom: 0px; border:none;" alt="Employee Photo"
                             src="<?php echo url_for("pim/viewPhoto?id=" . $encrypt->encrypt($_SESSION['PIM_EMPID'])); ?>" />
                    </span>
                </td>
                <td>

                    <?php
                    $Culture = $_SESSION['symfony/user/sfUser/culture'];
                    $ESSDao = new ESSDao();
                    $Employee = $ESSDao->readEmployee($_SESSION['PIM_EMPID']);
                    if ($Culture == "en") {
                        $EName = "getEmp_display_name";
                    } else {
                        $EName = "getEmp_display_name_" . $Culture;
                    }
                    if ($Employee->$EName() == null) {
                        $empName = $Employee->emp_display_name;
                    } else {
                        $empName = $Employee->$EName();
                    }
                    ?>
                    <b><?php echo __('Name') ?> </b>  : <?php echo $empName; ?>
                </td>
                <td style="">
                    <b><?php echo __('Employee Id') ?></b>  : <?php echo $Employee->employeeId; ?>
                </td>
                <td>
                    <b><?php echo __('NIC No') ?></b>  : <?php echo $Employee->emp_nic_no; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    if ($Culture == "en") {
                        $jobName = "name";
                    } else {
                        $jobName = "name_" . $Culture;
                    }
                    if ($Employee->jobTitle->$jobName == null) {
                        $joblanName = $Employee->jobTitle->name;
                    } else {
                        $joblanName = $Employee->jobTitle->$jobName;
                    }
                    ?>
                    <b><?php echo __('Designation') ?></b>  : <?php echo $joblanName; ?>
                </td>
                <td>

                    <?php
                    if ($Culture == "en") {
                        $unitName = "title";
                    } else {
                        $unitName = "title_" . $Culture;
                    }
                    if ($Employee->subDivision->$unitName == null) {
                        $UnitName = $Employee->subDivision->title;
                    } else {
                        $UnitName = $Employee->subDivision->$unitName;
                    }
                    ?>
                    <b><?php echo __('Work Station/Division') ?></b>  : <?php echo $UnitName; ?>
                </td>
                <td>

                    <?php
                    if ($Culture == "en") {
                        $unitName = "title";
                    } else {
                        $unitName = "title_" . $Culture;
                    }
                    if ($Employee->subDivision->$unitName == null) {
                        $UnitName = $Employee->subDivision->title;
                    } else {
                        $UnitName = $Employee->subDivision->$unitName;
                    }
                    ?>
                    <b><?php echo __('Date of join') ?></b>  : <?php echo $Employee->emp_com_date; ?>
                </td>

            </tr>
        </table>  
    </div>
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo __("Transfer Request") ?></h2></div>
        <?php echo message(); ?>
        <form name="frmSave" id="frmSave" method="post"  action="">
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Preferred Division/District/National") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol">
                <select name="cmbLevel" id="cmbLevel" class="formSelect" style="width: 160px;" tabindex="4" onchange="getLevel(this.value);">

                    <option value=""><?php echo __("--Select--") ?></option>
                    <?php foreach ($listLevel as $Level) {
                        ?>
                        <option value="<?php echo $Level->def_level ?>"<?php
                    if ($Level->def_level == $insid
                    )
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

                <select name="cmbLocationid" id="cmbLocationid" class="formSelect" style="width: 160px;" tabindex="4" onchange="isThere(this.value)">

                    <option value=""><?php echo __("--Select--") ?></option>
                    <?php foreach ($currentCourses as $clist) { ?>

                        <option value="<?php echo $clist->getLocationid() ?>" <?php if ($clist->getLocationid() == $cid)
                        echo "selected" ?>><?php
                        $abc = "getTd_course_name_" . $culture;
                        if ($clist->$abc() == "")
                            echo $clist->getTLocation_en(); else
                            echo $clist->$abc();
                        ?></option>
                    <?php } ?>

                </select>
            </div>
           
            <div id="parenthDep">
                 <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Select department") ?> <span class="required">*</span></label>
            </div>
            <div class="centerCol" id="hDep">

            
            </div>
            </div>
            <br class="clear"/>

            <div class="leftCol" id="headOfficeDevision1">

                <label class="controlLabel" for="txtLocationCode"><?php echo __("") ?> </label>
            </div>
            <div class="centerCol" id="headOfficeDevision">

                <select name="cmbcourseid" id="cmbcourseid" class="formSelect" style="width: 160px;" tabindex="4" onchange="isThere(this.value)">

                    <option value=""><?php echo __("--Select--") ?></option>
                    <?php foreach ($currentCourses as $clist) { ?>

                        <option value="<?php echo $clist->getTd_course_id() ?>" <?php if ($clist->getTd_course_id() == $cid)
                        echo "selected" ?>><?php
                        $abc = "getTd_course_name_" . $culture;
                        if ($clist->$abc() == "")
                            echo $clist->getTd_course_name_en(); else
                            echo $clist->$abc();
                        ?></option>
                    <?php } ?>

                </select>

            </div>
            <br class="clear"/>

            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Preference1") ?> </label>
            </div>
            <div class="centerCol">
                <input id="prf1"  name="prf1" type="text"  class="formInputText" value=""  maxlength="75" style="width: 250px;"/>
            </div>
            <br class="clear"/>
            <div class="leftCol">   
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Preference2") ?> </label>
            </div>
            <div class="centerCol">
                <input id="prf2"  name="prf2" type="text"  class="formInputText" value=""  maxlength="75" style="width: 250px;"/>
            </div>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtLocationCode"><?php echo __("Preference3") ?></label>
            </div>
            <div class="centerCol">
                <input id="prf3"  name="prf3" type="text"  class="formInputText" value="" maxlength="75" style="width: 250px;"/>
            </div>
            <br class="clear"/>
            <div class="leftCol">
                <label class="controlLabel" for="txtcomment"><?php echo __("Comment") ?> <span class="required"></span></label>
            </div>
            <div class="centerCol">
                <textarea class="formTextArea"  name="comment" style="width: 320px; height: 120px;"></textarea>
            </div>
            <?php if ($isAdmin == "Yes") { ?>
                <br class="clear"/>
                <div class="leftCol">
                    <label class="controlLabel" for="txtcomment"><?php echo __("Divisional Head Comment") ?> <span class="required"></span></label>
                </div>
                <div class="centerCol">
                    <textarea class="formTextArea"  name="admincomment"></textarea>
                </div>
            <?php } ?>


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

    function SelectEmployee(data){
                               
        myArr = data.split('|');
        $("#txtEmpId").val(myArr[0]);
        $("#txtEmployeeName").val(myArr[1]);
        getId(myArr[0]);
        comDateValidation(myArr[0]);
       
                   
    }

    function getLevel(lid){
        //        alert(lid);
        // post(file, data, callback, type); (only "file" is required)
        $.post(

        "<?php echo url_for('transfer/ajaxloadSubLevel') ?>", //Ajax file

        { lid: lid },  // create an object will all values

        //function that is called when server returns a value.
        function(data){
            
            var selectbox="<select name='cmdLevel2' id='cmdLevel2' class='formSelect' style='width: 160px;' tabindex='4' onchange=getHeadOfficeDevision(this.value)>";
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

    function getHeadOfficeDevision(id){

    
<?php $sysConf = OrangeConfig::getInstance()->getSysConf(); ?>


             $.post(

            "<?php echo url_for('transfer/CheckIsHeadOffice') ?>", //Ajax file

            { id: id },


            function(data){
                if(data==1){
                
                $("#parenthDep").show();
                       $.post(

        "<?php echo url_for('transfer/LoadHeadOfficeList') ?>", //Ajax file

        { id: id },  // create an object will all values

        //function that is called when server returns a value.
        function(data){

            var selectbox="<select name='cmbhDep' id='cmbhDep' class='formSelect' style='width: 160px;' tabindex='4' >";
            selectbox=selectbox +"<option value=''><?php echo __('--Select--') ?></option>";
            $.each(data, function(key, value) {

                selectbox=selectbox +"<option value="+key+">"+value+"</option>";
            });
            selectbox=selectbox +"</select>";
            $('#hDep').html(selectbox);

        },

        //How you want the data formated when it is returned from the server.
        "json"

    );


                }else{
                $('#hDep').html("");
                $("#parenthDep").hide();
                }
            },


            "json"

           );



    }
    
   
    
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
      
      
    function returnLocDet(){

        // TODO: Point to converted location popup
        var popup=window.open('<?php echo public_path('../../symfony/web/index.php/admin/listCompanyStructure?mode=select_subunit&method=mymethod'); ?>','Locations','height=450,resizable=1,scrollbars=1');
        if(!popup.opener) popup.opener=self;
    }
    function mymethod($id,$name){

        //alert('test');
        $("#hiddenDivID").val($id);
        $("#txtDivisionName").val($name);

    }
    function comDateValidation(empId){
                 
        // post(file, data, callback, type); (only "file" is required)
        $.post(

        "<?php echo url_for('transfer/comdateValidaiton') ?>", //Ajax file

        { sendValue: empId  },  // create an object will all values

        //function that is called when server returns a value.
        function(data){
            $("#hiddenMsg").val(data.message);
            if(data.message=="error"){
                alert("<?php echo __('Commencement date of this Employee is less than to the current Date.Not allowed to Sava/Edit Record') ?>");
						
                                                
                location.href = "<?php echo url_for('transfer/SaveTranserRequest') ?>";
            }
                                                     
                                                

        },

        //How you want the data formated when it is returned from the server.
        "json"
    );
                               
    }

    $(document).ready(function() {
    $("#parenthDep").hide();
        $('#headOfficeDevision').hide();
        $('#headOfficeDevision1').hide();
        $('#empRepPopBtn').click(function() {

            var popup=window.open('<?php echo public_path('../../symfony/web/index.php/pim/searchEmployee?type=single&method=SelectEmployee'); ?>','Locations','height=450,width=800,resizable=1,scrollbars=1');


            if(!popup.opener) popup.opener=self;
            popup.focus();
        });

        buttonSecurityCommon(null,null,"editBtn",null);
        
       

        //Disable all fields
        $("#hiddenMsg").val();
 
        $("#frmSave").validate({

            rules: {
                txtEmployeeNo: { required: true},
                txtEmployeeName: { required: true},
                cmbLevel: { required: true},
                cmdLevel2: { required: true },
                prf1:{ maxlength: 75, noSpecialCharsOnly: true},
                prf2:{ maxlength: 75, noSpecialCharsOnly: true},
                prf3:{ maxlength: 75, noSpecialCharsOnly: true},
                comment:{maxlength: 200, noSpecialCharsOnly: true},
                admincomment:{maxlength: 200, noSpecialCharsOnly: true}
            },
            messages: {
                txtEmployeeNo: {required: "<?php echo __('This field is required') ?>"},
                txtEmployee: {required: "<?php echo __('This field is required') ?>"},
                cmbLevel: {required: "<?php echo __('This field is required') ?>"},
                cmdLevel2: {required: "<?php echo __('This field is required') ?>"},
                prf1: {maxlength: "<?php echo __('Maximum length should be 75 characters') ?>", noSpecialCharsOnly: "<?php echo __('No invalid characters are allowed') ?>"},
                prf2: {maxlength: "<?php echo __('Maximum length should be 75 characters') ?>", noSpecialCharsOnly: "<?php echo __('No invalid characters are allowed') ?>"},
                prf3: {maxlength: "<?php echo __('Maximum length should be 75 characters') ?>", noSpecialCharsOnly: "<?php echo __('No invalid characters are allowed') ?>"},
                comment:{maxlength: "<?php echo __('Maximum length should be 200 characters') ?>", noSpecialCharsOnly: "<?php echo __('No invalid characters are allowed') ?>"},
                admincomment:{maxlength: "<?php echo __('Maximum length should be 200 characters') ?>", noSpecialCharsOnly: "<?php echo __('No invalid characters are allowed') ?>"}
                                        

            }
        });

			


        // When click edit button
        $("#editBtn").click(function() {
                                  
                                   					
            $('#frmSave').submit();

					
        });
        //When click reset buton
        $("#resetBtn").click(function() {
            document.forms[0].reset('');
        });

        //When click reset buton
        $("#resetBtn").click(function() {
            document.forms[0].reset('');
        });

        //When Click back button
        $("#btnBack").click(function() {
            location.href = "<?php echo url_for('transfer/TransferRequest') ?>";
        });

			
    });
</script>



