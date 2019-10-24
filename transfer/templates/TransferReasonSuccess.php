<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js') ?>"></script>

<div class="outerbox">
    <div class="maincontent">

        <div class="mainHeading"><h2><?php echo __("Transfer Reasons Summary") ?></h2></div>
        <?php echo message() ?>
        <form name="frmSearchBox" id="frmSearchBox" method="post" action="" onsubmit="return validateform();">
            <input type="hidden" name="mode" value="search">
            <div class="searchbox">
                <label for="searchMode"><?php echo __("Search By") ?></label>


                <select name="searchMode" id="searchMode">
                    <option value="all"><?php echo __("--Select--") ?></option>
                    <?php
                    if ($searchMode == 'trans_reason_id') {
                        echo "selected";
                    }
                    ?>><?php echo __("ID") ?></option>
                    <option value="trans_reason_en" <?php
                    if ($searchMode == 'trans_reason_en') {
                        echo "selected";
                    }
                    ?>><?php echo __("Transfer Reason") ?></option>

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
        <form name="standardView" id="standardView" method="post" action="<?php echo url_for('transfer/DeleteTransferReason') ?>">
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
                                $feild = "trans_reason_en";
                            } else {
                                $feild = "trans_reason_" . $Culture;
                            }
                            ?>
                            <?php echo $sorter->sortLink($feild, __('Transfer Reason'), '@trans_reason', ESC_RAW); ?>

                        </td>

                    </tr>
                </thead>

                <tbody>
                    <?php
                    $row = 0;
                    $encryptObj = new EncryptionHandler();
                    foreach ($listTransferReson as $reasons) {
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                        $row = $row + 1;
                        ?>
                        <tr class="<?php echo $cssClass ?>">
                            <td>
                                <input type='checkbox' class='checkbox innercheckbox' name='chkLocID[]' id="chkLoc" value='<?php echo $reasons->getTrans_reason_id() ?>' />
                            </td>

                            <td class="">
                                <a href="<?php echo url_for('transfer/SaveTransferReason?id=' . $encryptObj->encrypt($reasons->trans_reason_id)) ?>">
                                    <?php
                                    $abc = "getTrans_reason_" . $Culture;
                                    if ($reasons->$abc() == "") {
                                        echo $reasons->getTrans_reason_en();
                                    } else {
                                        echo $reasons->$abc();
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
            location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/SaveTransferReason')) ?>";
        });
        $("#resetBtn").click(function(){
            location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/transfer/TransferReason')) ?>";
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

