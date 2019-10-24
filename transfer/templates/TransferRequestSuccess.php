<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js') ?>"></script>

<div class="outerbox">
    <div class="maincontent">

        <div class="mainHeading"><h2><?php echo __("Transfer Requests Summary") ?></h2></div>
        <?php echo message() ?>
        <form name="frmSearchBox" id="frmSearchBox" method="post" action="" onsubmit="return validateform();">
            <input type="hidden" name="mode" value="search">
            <div class="searchbox">
                <label for="searchMode"><?php echo __("Search By") ?></label>
                <select name="searchMode" id="searchMode">
                    <option value="all"><?php echo __("--Select--") ?></option>
                    <option value="emp_display_name" <?php
        if ($searchMode == 'emp_display_name') {
            echo "selected";
        }
        ?>><?php echo __("Employee Name") ?></option>
                    <option value="date" <?php
                            if ($searchMode == 'date') {
                                echo "selected";
                            }
        ?>><?php echo __("Preferred Location") ?></option>
                    <option value="preferred_location" <?php
                            if ($searchMode == 'preferred_location') {
                                echo "selected";
                            }
        ?>><?php echo __("Destination") ?></option>
                    <option value="from" <?php
                            if ($searchMode == 'from') {
                                echo "selected";
                            }
        ?>><?php echo __("From") ?></option>
                </select>
                <label for="searchValue"><?php echo __("Search For") ?>:</label>
                <input type="text" size="20" name="searchValue" id="searchValue" value="<?php echo $searchValue ?>" />
                <input type="submit" class="plainbtn"
                       value="<?php echo __("Search") ?>" />
                <input type="reset" class="plainbtn"
                       value="<?php echo __("Reset") ?>" id="resetBtn" />
                <br class="clear"/>
            </div>
        </form>
        <div class="actionbar">
            <div class="actionbuttons">

                <input type="button" class="plainbtn" id="buttonAdd"
                       value="<?php echo __("Add") ?>" />


                <input type="button" class="plainbtn" id="buttonRemove"
                       value="<?php echo __("Delete") ?>" />

            </div>
            <div class="noresultsbar"></div>
            <div class="pagingbar"><?php echo is_object($pglay) ? $pglay->display() : ''; ?></div>
            <br class="clear" />
        </div>
        <br class="clear" />
        <form name="standardView" id="standardView" method="post" action="<?php echo url_for('transfer/DeleteTransferRequestAdmin') ?>">
            <input type="hidden" name="mode" id="mode" value=""/>
            <table cellpadding="0" cellspacing="0" class="data-table">
                <thead>
                    <tr>
                        <td width="50">

<!--                            <input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />-->

                        </td>
                        <td scope="col">
                            <?php
                            if ($Culture == "en") {
                                $feild = "e.emp_display_name";
                            } else {
                                $feild = "e.emp_display_name_" . $Culture;
                            }
                            ?>
                            <?php echo $sorter->sortLink($feild, __('Employee Name'), '@transr_request', ESC_RAW); ?>
                        </td>
                        <td scope="col">
                            <?php echo $sorter->sortLink('tr.id', __('Preferred Division'), '@transr_request', ESC_RAW); ?>
                        </td>
                        <td scope="col">
                            <?php echo $sorter->sortLink('tr.trans_req_location_pref1', __('Preferred Location'), '@transr_request', ESC_RAW); ?>
                        </td>

                        <td scope="col">
                            <?php echo $sorter->sortLink('e.work_station', __('Current Division'), '@transr_request', ESC_RAW); ?>
                        </td>
                        <td scope="col">
                            <?php echo $sorter->sortLink('e.emp_com_date', __('Working Experience'), '@transr_request', ESC_RAW); ?>
                        </td>

                        <td scope="col">
                            <?php echo __('User Comment') ?>

                        </td>
                        <td scope="col">
                            <?php echo __('Status') ?>

                        </td>
                        <td scope="col">
                            <?php echo __('Divisional Head Comment') ?>

                        </td>
                    </tr>      

                    </tr>
                </thead>

                <tbody>
                    <?php
                    $row = 0;
                    $encryptObj = new EncryptionHandler();
                    foreach ($listTransferRequest as $request) {
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                        $row = $row + 1;
                        ?>
                        <tr class="<?php echo $cssClass ?>">
                            <td>
                                <?php if ($request->trans_req_isapproved != "1") { ?>
                                    <input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' id="chkLoc" value='<?php echo $request->getTrans_req_id() ?>' />
                                <?php } ?>
                            </td>

                            <td class="">
                                <?php
                                if ($Culture == 'en') {
                                    $abc = "getEmp_display_name";
                                } else {
                                    $abc = "getEmp_display_name_" . $Culture;
                                }

                                $dd = $request->Employee->$abc();
                                $rest = substr($request->Employee->$abc(), 0, 100);
                                if ($request->Employee->$abc() == "") {
                                    $dd = $request->Employee->getEmp_display_name();
                                    $rest = substr($request->Employee->getEmp_display_name(), 0, 100);

                                    if (strlen($dd) > 100) {
                                        echo $rest
                                        ?>.<span title="<?php echo $dd ?>">...</span> <?php
                        } else {
                            echo $rest;
                        }
                    } else {


                        if (strlen($dd) > 100) {
                            echo $rest
                                        ?>.<span  title="<?php echo $dd ?>">...</span> <?php
                        } else {
                            echo $rest;
                        }
                    }
                                ?>
                            </td>
                            <td class="">
                                <?php
                                if ($Culture == 'en') {
                                    $abc = "getTitle";
                                } else {
                                    $abc = "getTitle_" . $Culture;
                                }

                                $dd = $request->CompanyStructure->$abc();
                                $rest = substr($request->CompanyStructure->$abc(), 0, 100);
                                if ($request->CompanyStructure->$abc() == "") {
                                    $dd = $request->CompanyStructure->getTitle();
                                    $rest = substr($request->CompanyStructure->getTitle(), 0, 100);

                                    if (strlen($dd) > 100) {
                                        echo $rest
                                        ?>.<span title="<?php echo $dd ?>">...</span> <?php
                        } else {
                            echo $rest;
                        }
                    } else {


                        if (strlen($dd) > 100) {
                            echo $rest
                                        ?>.<span  title="<?php echo $dd ?>">...</span> <?php
                        } else {
                            echo $rest;
                        }
                    }
                                ?>
                            </td>
                            <td class="">
                                <?php
                                $dd = $request->getTrans_req_location_pref1();
                                $rest = substr($request->getTrans_req_location_pref1(), 0, 100);



                                if (strlen($dd) > 100) {
                                    echo $rest
                                    ?>.<span title="<?php echo "1. " . $dd ?>">...</span> <?php
                        } else {
                            echo "1. " . $rest;
                        }
                                ?><br/>
                                <?php
                                //echo "2. " . /* $listtransrequest->getTrans_req_location_pref2(); */
                                $dd = $request->getTrans_req_location_pref2();
                                $rest = substr($dd, 0, 100);


                                if (strlen($dd) > 100) {
                                    echo $rest
                                    ?>.<span title="<?php echo "2. " . $dd ?>">...</span> <?php
                        } else {
                            echo "2. " . $rest;
                        }
                                ?><br/>
                                <?php
                                //echo "3. " . /* $listtransrequest->getTrans_req_location_pref3(); */
                                $dd = $request->getTrans_req_location_pref3();
                                $rest = substr($request->getTrans_req_location_pref3(), 0, 100);


                                if (strlen($dd) > 100) {
                                    echo $rest
                                    ?>.<span title="<?php echo "3. " . $dd ?>">...</span> <?php
                        } else {
                            echo "3. " . $rest;
                        }
                                ?><br/>
                            </td>
                            </td>
                            <td class="">
                                <?php
                                if ($Culture == 'en') {
                                    $abc = "getTitle";
                                } else {
                                    $abc = "getTitle_" . $Culture;
                                }

                                $dd = $request->Employee->subDivision->$abc();
                                $rest = substr($request->Employee->subDivision->$abc(), 0, 100);
                                if ($request->Employee->subDivision->$abc() == "") {
                                    $dd = $request->Employee->subDivision->getTitle();
                                    $rest = substr($request->Employee->subDivision->getTitle(), 0, 100);

                                    if (strlen($dd) > 100) {
                                        echo $rest
                                        ?>.<span title="<?php echo $dd ?>">...</span> <?php
                        } else {
                            echo $rest;
                        }
                    } else {


                        if (strlen($dd) > 100) {
                            echo $rest
                                        ?>.<span  title="<?php echo $dd ?>">...</span> <?php
                        } else {
                            echo $rest;
                        }
                    }
                                ?>
                            </td>
                            <td class="">
                                <?php
                                $date1 = $request->Employee->getEmp_com_date();

                                $date2 = strtotime(time());

                                $date1 = new DateTime($date1);
                                $date2 = new DateTime($date2);
                                $interval = $date1->diff($date2);
                                if ($Culture == 'en') {
                                    echo $interval->y . " " . __("Year(s)") . "," . $interval->m . " " . __("Month(s)") . " , " . $interval->d . " " . __("Day(s)");
                                } else {
                                    echo __("Year(s)") . " " . $interval->y . "," . __("Month(s)") . " " . $interval->m . " , " . __("Day(s)") . " " . $interval->d;
                                }
                                ?>
                            </td>
                            <td class="">
                                <?php
                                //echo $listtransrequest->getTrans_req_usercommnet();
                                $dd = $request->getTrans_req_usercommnet();
                                $rest = substr($request->getTrans_req_usercommnet(), 0, 100);


                                if (strlen($dd) > 100) {
                                    echo $rest
                                    ?>.<span title="<?php echo $dd ?>">...</span> <?php
                        } else {
                            echo $rest;
                        }
                                ?>
                            </td>                          
                            <td class="" style="width: 50px;">
                                <?php
//                                if ($Culture == 'en') {
//                                    if ($request->getTrans_req_status() == "-1") {
//                                        echo "Rejected";
//                                    } else if ($request->getTrans_req_status() == "0") {
//                                        echo "Pending";
//                                    } else if ($request->getTrans_req_status() == "1") {
//                                        echo "Submited";
//                                    } else if ($request->getTrans_req_status() == "2") {
//                                        echo "Submited";
//                                    } else if ($request->getTrans_req_status() == "3") {
//                                        echo "Approved";
//                                    }
//                                } else if ($Culture == 'si') {
//                                    if ($request->getTrans_req_status() == "-1") {
//                                        echo "අනුමත නොකල";
//                                    } else if ($request->getTrans_req_status() == "0") {
//                                        echo "ඉල්ලුම් නොකල";
//                                    } else if ($request->getTrans_req_status() == "1") {
//                                        echo "ඉල්ලුම් කල";
//                                    } else if ($request->getTrans_req_status() == "2") {
//                                        echo "ඉල්ලුම් කල";
//                                    } else if ($request->getTrans_req_status() == "3") {
//                                        echo "අනුමත කළ";
//                                    }
//                                } else {
//                                    if ($request->getTrans_req_status() == "-1") {
//                                        echo "Rejected";
//                                    } else if ($request->getTrans_req_status() == "0") {
//                                        echo "Pending";
//                                    } else if ($request->getTrans_req_status() == "1") {
//                                        echo "Submited";
//                                    } else if ($request->getTrans_req_status() == "2") {
//                                        echo "Submited";
//                                    } else if ($request->getTrans_req_status() == "3") {
//                                        echo "Approved";
//                                    }
//                                }





                                if ($request->trans_req_isapproved == "1") {
                                    echo __("Approved");
                                } elseif ($request->trans_req_isapproved == "-1") {
                                    echo __("Rejected");
                                } elseif ($request->WfMain->wfmain_approving_emp_number == "") {
                                    echo __("Pending");
                                } else {
                                    $transDao = new TransferDao();
                                    $lastAppEmp = $transDao->getLastApprovedLevel($request->WfMain->wfmain_id);
                                    $employee = $transDao->getEmployeeId($lastAppEmp);

                                    if ($Culture == "en") {
                                        $feild = "emp_display_name";
                                    } else {
                                        $feild = "emp_display_name_" . $Culture;
                                    }

                                    echo $employee->$feild . __("Approved. Pending for next Approval");
                                }
//                            if($request->trans_req_isapproved == ""){
//                                echo "Submited";
//                            }
                                ?> 
                            </td>
                            <td class="">
                                <?php

                                    $transDao=new TransferDao();
                               $DivisonaHeadCommentsObj=$transDao->getDivisionHeadComments($request->wfmain_id);
                                foreach($DivisonaHeadCommentsObj as $key=>$value){
                                    echo $value['wfmain_comments']."<br/>";


                                }

                                 $transDao=new TransferDao();
                               $DivisonaHeadCommentsObj1=$transDao->getDivisionHeadComments1($request->wfmain_id);
                                foreach($DivisonaHeadCommentsObj1 as $key=>$value){
                                    echo $value['wfmain_comments']."<br/>";
                                }



                                ?>
                            </td>
                        </tr>
                            <?php } ?>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script type="text/javascript">

    function validateform(){

        if($("#searchValue").val()=="")
        {

            alert("<?php echo __('Please enter search value') ?>");
            return false;

        }
        if($("#searchMode").val()=="all"){
            alert("<?php echo __('Please select the search mode') ?>");
            return false;
        }
        else{
            $("#frmSearchBox").submit();
        }

    }

    $(document).ready(function() {
    
        buttonSecurityCommon("buttonAdd",null,null,"buttonRemove");

        var answer=0;
        $("#buttonRemove").click(function() {
            $("#mode").attr('value', 'delete');
            if($('input[name=chkLocID[]]').is(':checked')){
                answer = confirm("<?php echo __("Do you really want to Delete?") ?>");
            }


            else{
                alert("<?php echo __("Select at least one check box to delete") ?>");

            }

            if (answer !=0)
            {

                $("#standardView").submit();

            }
            else{
                return false;
            }   

        });


        //When click add button
        $("#buttonAdd").click(function() {
            location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/SaveTransferRequest')) ?>";
        });
        $("#resetBtn").click(function(){
            location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/TransferRequest')) ?>";
        });

        // When Click Main Tick box
        $("#allCheck").click(function() {
            if ($('#allCheck').attr('checked')){

                $('.innercheckbox').attr('checked','checked');
            }else{
                $('.innercheckbox').removeAttr('checked');
            }
        });

        $(".innercheckbox").click(function() {
            if($(this).attr('checked'))
            {

            }else
            {
                $('#allCheck').removeAttr('checked');
            }
        });





    });


</script>

