<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js') ?>"></script>

<div class="outerbox">
    <div class="maincontent">

        <div class="mainHeading"><h2><?php echo __("Transfer Details Summary") ?></h2></div>
        <?php echo message() ?>
        <form name="frmSearchBox" id="frmSearchBox" method="post" action="" onsubmit="return validateform();">
            <input type="hidden" name="mode" value="search"/>
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
        ?>><?php echo __("Date") ?></option>
                    <option value="trans_reason" <?php
                            if ($searchMode == 'Reason') {
                                echo "selected";
                            }
        ?>><?php echo __("Preferred Location") ?></option>
                    <option value="trans_currentdiv_id" <?php
                            if ($searchMode == 'trans_currentdiv_id') {
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
                <input type="text" size="20" name="searchValue" id="searchValue" value="" />
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
        <form name="standardView" id="standardView" method="post" action="<?php echo url_for('transfer/DeleteTransfer') ?>">
            <input type="hidden" name="mode" id="mode" value=""/>
            <table cellpadding="0" cellspacing="0" class="data-table">
                <thead>
                    <tr>
                        <td width="50">

                            <input type="checkbox" class="checkbox" name="allCheck" value="" id="allCheck" />

                        </td>

                        <td scope="col">
                            <?php
                            if ($Culture == "en") {
                                $feild = "e.emp_display_name";
                            } else {
                                $feild = "e.emp_display_name_" . $Culture;
                            }
                            ?>
                            <?php echo $sorter->sortLink($feild, __('Employee Name'), '@transfer', ESC_RAW); ?>

                        </td>
                        <td scope="col">
                            <?php echo $sorter->sortLink('t.trans_effect_date', __('Effective Date'), '@transfer', ESC_RAW); ?>

                        </td>
                        <td scope="col">
                            <?php
                            if ($Culture == "en") {
                                $feild = "r.trans_reason_en";
                            } else {
                                $feild = "r.trans_reason_" . $Culture;
                            }
                            ?>
                            <?php echo $sorter->sortLink($feild, __('Transfer Reason'), '@transfer', ESC_RAW); ?>

                        </td>
                        <td scope="col">
                            <?php echo $sorter->sortLink('t.trans_currentdiv_id', __('From'), '@transfer', ESC_RAW); ?>

                        </td>
                        <td scope="col">
                            <?php echo $sorter->sortLink('t.trans_div_id', __('Destination'), '@transfer', ESC_RAW); ?>

                        </td>



                    </tr>
                </thead>

                <tbody>
                    <?php
                        $encryptObj = new EncryptionHandler();
                    $row = 0;
                    foreach ($listTransferDetail as $detail) {
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                        $row = $row + 1;
                        ?><?php
                        ?>
                        <tr class="<?php echo $cssClass ?>">
                            <td >
                                <input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' id="chkLoc" value='<?php echo $detail->trans_id ?>' />
                            </td>

                            <td class="">
                                <?php
                                if ($Culture == 'en') {
                                    $abc = "getEmp_display_name";
                                } else {
                                    $abc = "getEmp_display_name_" . $Culture;
                                }

                                $dd = $detail->Employee->$abc();
                                $rest = substr($detail->Employee->$abc(), 0, 100);
                                if ($detail->Employee->$abc() == "") {
                                    $dd = $detail->Employee->getEmp_display_name();
                                    $rest = substr($detail->Employee->getEmp_display_name(), 0, 100);

                                    if (strlen($dd) > 100) {
                                        echo $rest
                                        ?>.<span title="<?php echo $dd ?>">...</span> <?php
                        } else {
                            echo $rest;
                        }
                    } else {


                        if (strlen($dd) > 100) {
                            echo $rest
                                        ?>.<span  title=" <?php echo $dd ?>">...</span> <?php
                        } else {
                            echo $rest;
                        }
                    }
                                ?> 
                            </td>
                            <td class="">
                                <a href="<?php echo url_for('transfer/SaveTransferDetail?update=yes&id=' . $encryptObj->encrypt($detail->trans_id)) ?>">
                                        <?php echo LocaleUtil::getInstance()->formatDate($detail->trans_effect_date); ?></a>
                            </td>
                            <td class="">
                                <a href="<?php echo url_for('transfer/SaveTransferDetail?update=yes&id=' . $encryptObj->encrypt($detail->trans_id)) ?>"><?php
                            $abcd = "getTrans_reason_" . $Culture;

                            $dd = $detail->TransferReason->$abcd();
                            $rest = substr($detail->TransferReason->$abcd(), 0, 100);

                            if ($detail->TransferReason->$abcd() == "") {
                                $dd = $detail->TransferReason->getTrans_reason_en();
                                $rest = substr($detail->TransferReason->getTrans_reason_en(), 0, 100);

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
                                ?></a>
                            </td>
                            <td class="">
                                <a href="<?php echo url_for('transfer/SaveTransferDetail?update=yes&id=' . $encryptObj->encrypt($detail->trans_id)) ?>"><?php
                                if ($Culture == 'en') {
                                    $abc = "getTitle";
                                } else {
                                    $abc = "getTitle_" . $Culture;
                                }

                                $dd = $detail->CompanyStructure->$abc();
                                $rest = substr($detail->CompanyStructure->$abc(), 0, 100);
                                if ($detail->CompanyStructure->$abc() == "") {
                                    $dd = $detail->CompanyStructure->getTitle();
                                    $rest = substr($detail->CompanyStructure->getTitle(), 0, 100);

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
                                ?></a>
                            </td>
                            <td class="">
                                <a href="<?php echo url_for('transfer/SaveTransferDetail?id=' . $encryptObj->encrypt($detail->trans_id)) ?>"><?php
                                if ($Culture == 'en') {
                                    $abc = "getTitle";
                                } else {
                                    $abc = "getTitle_" . $Culture;
                                }

                                $dd = $detail->TransferCompanyStructure->$abc();
                                $rest = substr($detail->TransferCompanyStructure->$abc(), 0, 100);
                                if ($detail->TransferCompanyStructure->$abc() == "") {
                                    $dd = $detail->TransferCompanyStructure->getTitle();
                                    $rest = substr($detail->TransferCompanyStructure->getTitle(), 0, 100);

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
                                ?></a>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        </form>
    </div>
</div>

<script type="text/javascript">
    function disableAnchor(obj, disable){
        if(disable){
            var href = obj.getAttribute("href");
            if(href && href != "" && href != null){
                obj.setAttribute('href_bak', href);
            }
            obj.removeAttribute('href');
            obj.style.color="gray";
        }
        else{
            obj.setAttribute('href', obj.attributes
            ['href_bak'].nodeValue);
            obj.style.color="blue";
        }
    }
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

    function confirmdelet(){
        
        return false;
        /*answer = confirm("Do you really want to Delete?");
                if (answer !=0)
                {
                    return true;
                }
                else{
                    return false;
                }*/
    }


    $(document).ready(function() {


        buttonSecurityCommon("buttonAdd",null,null,"buttonRemove");
                

        var answer=0;
        //When click add button
        $("#buttonAdd").click(function() {
            location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/SaveTransferDetail')) ?>";

        });

        $("#buttonAdd").click(function() {
            location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/SaveTransferDetail')) ?>";

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

        //When click Save Button
        $("#buttonRemove").click(function() {
            $("#mode").attr('value', 'save');
            //$("#standardView").submit();
        });
        $("#resetBtn").click(function(){
            location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/TransferDetail')) ?>";
        });


    });


</script>

