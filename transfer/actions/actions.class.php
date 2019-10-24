<?php

/**
 * Actions class for Transfer Module
 *
 * -------------------------------------------------------------------------------------------------------
 *  Author    - Jayanath Liyanage
 *  On (Date) - 1 Augest 2011 
 *  Comments  - Employee Transfer Functions 
 *  Version   - Version 1.0
 * -------------------------------------------------------------------------------------------------------
 * */
class transferActions extends sfActions {

    /**
     * Executes TransferReason action
     *
     * @param sfRequest $request A request object
     */
    public function executeTransferReason(sfWebRequest $request) {

        try {
            $this->Culture = $this->getUser()->getCulture();



            $transSearchService = new TransferSearchService();
            $this->transSearchService = $transSearchService;

            $this->sorter = new ListSorter('TransferReason', 'transfer', $this->getUser(), array('t.trans_reason_id', ListSorter::ASCENDING));

            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
//            if ($request->getParameter('mode') == 'search') {
//                if ($request->getParameter('searchMode') != 'all' && trim($request->getParameter('searchValue')) == '') {
//                    $this->setMessage('NOTICE', array('Select the field to search'));
//                    $this->redirect('recruitment/TransferReason');
//                }
//            }
            $this->searchMode = ($request->getParameter('searchMode') == '') ? 'all' : $request->getParameter('searchMode');
            $this->searchValue = ($request->getParameter('searchValue') == '') ? '' : $request->getParameter('searchValue');

            $this->sort = ($request->getParameter('sort') == '') ? 't.trans_reason_id' : $request->getParameter('sort');
            $this->order = ($request->getParameter('order') == '') ? 'DESC' : $request->getParameter('order');
            $res = $transSearchService->searchTransferReason($this->searchMode, $this->searchValue, $this->Culture, $request->getParameter('page'), $this->sort, $this->order);

            $this->listTransferReson = $res['data'];
            $this->pglay = $res['pglay'];
            $this->pglay->setTemplate('<a href="{%url}">{%page}</a>');
            $this->pglay->setSelectedTemplate('{%page}');
            if (count($res['data']) <= 0) {
                $this->setMessage('SUCCESS', array($this->getContext()->getI18N()->__("Sorry,Your Search did not Match any Records.", $args, 'messages')));
            }
        } catch (sfStopException $sf) {
            
        } catch (Exception $e) {
            $errMsg = new CommonException($e->getMessage(), $e->getCode());
            $this->setMessage('WARNING', $errMsg->display());
            $this->redirect('default/error');
        }
    }

    /**
     * Executes SaveTransferReason action
     *
     * @param sfRequest $request A request object
     */
    public function executeSaveTransferReason(sfWebRequest $request) {

        $encrypt = new EncryptionHandler();

        try {
            if (!strlen($request->getParameter('lock'))) {
                $this->mode = 0;
            } else {
                $this->mode = $request->getParameter('lock');
            }
            $ebLockid = $encrypt->decrypt($request->getParameter('id'));

            if (isset($this->mode)) {
                if ($this->mode == 1) {

                    $conHandler = new ConcurrencyHandler();

                    $recordLocked = $conHandler->setTableLock(' hs_hr_trans_reason', array($ebLockid), 1);

                    if ($recordLocked) {
                        // Display page in edit mode
                        $this->mode = 1;
                    } else {

                        $this->setMessage('WARNING', array($this->getContext()->getI18N()->__('Can not update. Record locked by another user.', $args, 'messages')), false);
                        $this->mode = 0;
                    }
                } else if ($this->mode == 0) {
                    $conHandler = new ConcurrencyHandler();
                    $recordLocked = $conHandler->resetTableLock(' hs_hr_trans_reason', array($ebLockid), 1);
                    $this->mode = 0;
                }
            }

            $this->Culture = $this->getUser()->getCulture();
            $transferService = new TransferService();

            $requestId = $request->getParameter('id');
            if (strlen($requestId)) {
                $requestId = $encrypt->decrypt($request->getParameter('id'));
                if (!strlen($this->mode)) {
                    $this->mode = 0;
                }
                $this->transferReasonGetById = $transferService->readTransferReasonbyId($requestId);

                if (!$this->transferReasonGetById) {
                    $this->setMessage('WARNING', array($this->getContext()->geti18n()->__('Record Not Found')));
                    $this->redirect('transfer/TransferReason');
                }
            } else {
                $this->mode = 1;
            }


            if ($request->isMethod('post')) {

                if (strlen($request->getParameter('txtTransferReasonCode'))) {
                    $transferReason = $transferService->readTransferReasonbyId($request->getParameter('txtTransferReasonCode'));
                } else {
                    $transferReason = new TransferReason();
                }
                $transferReason = $transferService->getTransReasonObject($request, $transferReason);

                $transferService->saveTransReason($transferReason);
                if (strlen($requestId)) {
                    $this->setMessage('SUCCESS', array($this->getContext()->geti18n()->__('Successfully Updated')));
                    $this->redirect('transfer/SaveTransferReason?id=' . $requestId . '&lock=0');
                } else {
                    $this->setMessage('SUCCESS', array($this->getContext()->geti18n()->__('Successfully Added')));
                    $this->redirect('transfer/TransferReason');
                }

                if (strlen($requestId)) {
                    $this->setMessage('SUCCESS', array($this->getContext()->geti18n()->__('Successfully Updated')));
                    $this->redirect('transfer/SaveTransferReason?id=' . $requestId . '&lock=0');
                } else {
                    $this->setMessage('SUCCESS', array($this->getContext()->geti18n()->__('Successfully Added')));
                    $this->redirect('transfer/TransferReason');
                }
            }
        } catch (Doctrine_Connection_Exception $e) {
            $errMsg = new CommonException($e->getPortableMessage(), $e->getPortableCode());
            $this->setMessage('WARNING', $errMsg->display());
            $this->redirect('transfer/TransferReason');
        } catch (sfStopException $sf) {
            $this->redirect('transfer/TransferReason');
        } catch (Exception $e) {
            $errMsg = new CommonException($e->getMessage(), $e->getCode());
            $this->setMessage('WARNING', $errMsg->display());
            $this->redirect('transfer/TransferReason');
        }
    }

    /**
     * Executes DeleteTransferReason action
     *
     * @param sfRequest $request A request object
     */
    public function executeDeleteTransferReason(sfWebRequest $request) {

        if (count($request->getParameter('chkLocID')) > 0) {
            $transferService = new TransferService();
            try {
                $conn = Doctrine_Manager::getInstance()->connection();
                $conn->beginTransaction();
                $ids = array();
                $ids = $request->getParameter('chkLocID');
                $countArr = array();
                $saveArr = array();
                for ($i = 0; $i < count($ids); $i++) {
                    $conHandler = new ConcurrencyHandler();
                    $isRecordLocked = $conHandler->isTableLocked('hs_hr_trans_reason', array($ids[$i]), 1);
                    if ($isRecordLocked) {
                        $countArr = $ids[$i];
                    } else {
                        $saveArr = $ids[$i];
                        $transferService->deleteReason($ids[$i]);
                        $conHandler->resetTableLock('hs_hr_trans_reason', array($ids[$i]), 1);
                    }
                }

                $conn->commit();
            } catch (Doctrine_Connection_Exception $e) {

                $conn->rollBack();
                $errMsg = new CommonException($e->getPortableMessage(), $e->getPortableCode());
                $this->setMessage('WARNING', $errMsg->display());
                $this->redirect('transfer/TransferReason');
            } catch (Exception $e) {
                $conn->rollBack();
                $errMsg = new CommonException($e->getMessage(), $e->getCode());
                $this->setMessage('WARNING', $errMsg->display());
                $this->redirect('transfer/TransferReason');
            }
            if (count($saveArr) > 0 && count($countArr) == 0) {
                $this->setMessage('SUCCESS', array($this->getContext()->getI18N()->__("Successfully Deleted", $args, 'messages')));
            } elseif (count($saveArr) > 0 && count($countArr) > 0) {
                $this->setMessage('WARNING', array($this->getContext()->getI18N()->__("Some records are can not be deleted as them  Locked by another user ", $args, 'messages')));
            } elseif (count($saveArr) == 0 && count($countArr) > 0) {
                $this->setMessage('WARNING', array($this->getContext()->getI18N()->__("Can not delete as them  Locked by another user ", $args, 'messages')));
            }
        } else {
            $this->setMessage('NOTICE', array('Select at least one record to delete'));
        }
        $this->redirect('transfer/TransferReason');
    }

    /**
     * Executes DeleteTransferRequestAdmin action
     *
     * @param sfRequest $request A request object
     */
    public function executeDeleteTransferRequestAdmin(sfWebRequest $request) {

        $userType = $request->getParameter('userType');
        $transferService = new TransferService();
        try {

            $conn = Doctrine_Manager::getInstance()->connection();
            $conn->beginTransaction();
            $ids = array();
            $ids = $request->getParameter('chkLocID');

            $countArr = array();
            $saveArr = array();
            for ($i = 0; $i < count($ids); $i++) {
                $conHandler = new ConcurrencyHandler();
                $abc = explode("_", $ids[$i]);

                $isRecordLocked = $conHandler->isTableLocked('hs_hr_transfer_request', array(trim($abc[1]), $abc[0]), 2);
                if ($isRecordLocked) {
                    $countArr = $ids[$i];
                } else {
                    $saveArr = $ids[$i];


//                    if ($transferRecord == null) {
                    $transferService->deleteRequest($ids[$i]);
                    // $transferService->deleteWfMainAppPerson($ids[$i]);
                    //$transferService->deleteWfMain($ids[$i]);
//                    }


                    $conHandler->resetTableLock('hs_hr_transfer_request', array(trim($abc[1]), $abc[0]), 2);
                }
            }

            $conn->commit();
        } catch (Doctrine_Connection_Exception $e) {
            $conn->rollBack();
            $errMsg = new CommonException($e->getPortableMessage(), $e->getPortableCode());
            $this->setMessage('WARNING', $errMsg->display());



            $this->redirect('transfer/TransferRequest');
        } catch (sfStopException $sf) {
            
        } catch (Exception $e) {
            $conn->rollBack();
            $errMsg = new CommonException($e->getMessage(), $e->getCode());
            $this->setMessage('WARNING', $errMsg->display());
            $this->redirect('transfer/TransferRequest');
        }

        if (count($saveArr) > 0 && count($countArr) == 0) {
            $this->setMessage('SUCCESS', array($this->getContext()->getI18N()->__("Successfully Deleted", $args, 'messages')));
        } elseif (count($saveArr) > 0 && count($countArr) > 0) {
            $this->setMessage('WARNING', array($this->getContext()->getI18N()->__("Some records are can not be deleted as them  Locked by another user ", $args, 'messages')));
        } elseif (count($saveArr) == 0 && count($countArr) > 0) {
            $this->setMessage('WARNING', array($this->getContext()->getI18N()->__("Can not delete as them  Locked by another user ", $args, 'messages')));
        } else {
            $this->setMessage('NOTICE', array('Select at least one record to delete'));
        }
        $this->redirect('transfer/TransferRequest');
    }

    /**
     * Executes TransferRequestAdmin action
     *
     * @param sfRequest $request A request object
     */
    public function executeTransferRequestAdmin(sfWebRequest $request) {
        try {

            $user = $request->getParameter('user');

            $this->user = $user;
            $this->Culture = $this->getUser()->getCulture();

            $transSearchService = new TransferSearchService();
            $this->transSearchService = $transSearchService;

            $this->sorter = new ListSorter('TransferReason', 'transfer', $this->getUser(), array('tr.trans_req_id', ListSorter::ASCENDING));

            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));

//            if ($request->getParameter('mode') == 'search') {
//                if ($request->getParameter('searchMode') != 'all' && trim($request->getParameter('searchValue')) == '') {
//                    $this->setMessage('NOTICE', array('Select the field to search'));
//                    $this->redirect('transfer/TransferReqestAdmin');
//                }
//            }
            $this->searchMode = ($request->getParameter('searchMode') == '') ? 'all' : $request->getParameter('searchMode');
            $this->searchValue = ($request->getParameter('searchValue') == '') ? '' : $request->getParameter('searchValue');

            $this->sort = ($request->getParameter('sort') == '') ? 'tr.trans_req_id' : $request->getParameter('sort');
            $this->order = ($request->getParameter('order') == '') ? 'DESC' : $request->getParameter('order');

            $res = $transSearchService->searchTransferRequestAdmin($this->searchMode, $this->searchValue, $this->Culture, $request->getParameter('page'), $this->sort, $this->order, $this->user);
            $this->listTransferRequest = $res['data'];
            //die($this->listreasons);
            $this->pglay = $res['pglay'];
            $this->pglay->setTemplate('<a href="{%url}">{%page}</a>');
            $this->pglay->setSelectedTemplate('{%page}');
        } catch (sfStopException $sf) {
            
        } catch (Exception $e) {
            $errMsg = new CommonException($e->getMessage(), $e->getCode());
            $this->setMessage('WARNING', $errMsg->display());

            $this->redirect('default/error');
        }
    }

    /**
     * Executes SaveTransferRequestAdmin action
     *
     * @param sfRequest $request A request object
     */
    public function executeSaveTransferRequestAdmin(sfWebRequest $request) {

        $encrypt = new EncryptionHandler();
        $sysConf = OrangeConfig::getInstance()->getSysConf();
        $Headoffice = $sysConf->Headoffice;

        try {
            if (!strlen($request->getParameter('lock'))) {
                $this->mode = 0;
            } else {
                $this->mode = $request->getParameter('lock');
            }
            $ebLockid = $encrypt->decrypt($request->getParameter('id'));

            if (isset($this->mode)) {
                if ($this->mode == 1) {

                    $conHandler = new ConcurrencyHandler();

                    $recordLocked = $conHandler->setTableLock(' hs_hr_transfer_request', array($ebLockid), 1);

                    if ($recordLocked) {
                        // Display page in edit mode
                        $this->mode = 1;
                    } else {

                        $this->setMessage('WARNING', array($this->getContext()->getI18N()->__('Can not update. Record locked by another user.', $args, 'messages')), false);
                        $this->mode = 0;
                    }
                } else if ($this->mode == 0) {
                    $conHandler = new ConcurrencyHandler();
                    $recordLocked = $conHandler->resetTableLock(' hs_hr_transfer_request', array($ebLockid), 1);
                    $this->mode = 0;
                }
            }


            $this->Culture = $this->getUser()->getCulture();
            $transferService = new TransferService();

            $this->listLevel = $transferService->getLevelList();
            $requestId = $request->getParameter('id');
            if (strlen($requestId)) {
                $requestId = $encrypt->decrypt($request->getParameter('id'));
                if (!strlen($this->mode)) {
                    $this->mode = 0;
                }
                $this->transferRequestGetById = $transferService->readTransferReasonbyId($requestId);

                if (!$this->transferRequestGetById) {
                    $this->setMessage('WARNING', array($this->getContext()->geti18n()->__('Record Not Found')));
                    $this->redirect('transfer/TransferRequestAdmin');
                }
            } else {
                $this->mode = 1;
            }


            if ($request->isMethod('post')) {

              $transRequest=$transferService->saveAdminRequestSub($request);

                
                $transRequest->setTrans_req_status(0);

                $roleAdmin = 6;
                $roleSect = 12;

                $sysConf = OrangeConfig::getInstance()->getSysConf();

                 $WasamLevel = $sysConf->TransferWasamLevel;
                $ZonalLevel = $sysConf->TransferZonalLevel;
                $DivisionLevel = $sysConf->TransferDivisionLevel;
                $DistrictLevel = $sysConf->TransferDistrictLevel;
                $ProvinceLevel = $sysConf->TransferProvinceLevel;
                $NationalLevel = $sysConf->TransferNationalLevel;
                $DivisionSecretory = $sysConf->TransferDivisionSecretory;
                $DistrictSecretory = $sysConf->TransferDistrictSecretory;
                $ZonalManager = $sysConf->TransferZonalManager;
                $AssistantCommissioner = $sysConf->TransferAssistantCommissioner;
                $DirectorEstablishment = $sysConf->TransferDirectorEstablishment;
                $DirectorGeneral = $sysConf->TransferDirectorGeneral;
                $DirectorAdministration = $sysConf->TransferDirectorAdministration;
                $SamurdhiDevelopmentOfficer = $sysConf->TransferSamurdhiDevelopmentOfficer;
                $Manager = $sysConf->TransferManager;




                if (strlen($request->getParameter('txtEmpId'))) {
                    $employee = $request->getParameter('txtEmpId');
                }
                $currentEmpDiv=$transferService->readGetEmployeeId($employee)->work_station;
                $currentEmpDistrict=$transferService->readGetEmployeeId($employee)->hie_code_3;
                $prefedDivisionArr=$transferService->getPreferedDivisionDistrict($request->getParameter('cmdLevel2'));
                //if in a same District
                if(in_array($currentEmpDistrict, $prefedDivisionArr)) {
                   $isSameDistrict="1";
                }else{
                   $isSameDistrict="0";
                }

                $conn = Doctrine_Manager::getInstance()->connection();
                $conn->beginTransaction();
                $level2=$request->getParameter('cmdLevel2');
                //Current LogUser
//                $employee = $_SESSION['empNumber'];
                if (strlen($request->getParameter('cmbLevel'))) {
                    $levelCode = $request->getParameter('cmbLevel');
                }

                $wfDao = new workflowDao();
                if ($levelCode == $WasamLevel) {
                    //Intra Wasam Transfers

                    $transRequest = $this->saveWorkFlowTwoStepWithDis($employee, $DivisionSecretory, $ZonalManager, $transRequest, 8, 7,$isSameDistrict,20);
                } else if ($levelCode == $ZonalLevel) {
                    //Intra Zonal Transfers

                    $transRequest = $this->saveWorkFlowOneStep($employee, $DivisionSecretory, $transRequest, 8);
                } else if ($levelCode == $DivisionLevel) {
                    //Intra Divisional Transfers
                    $transRequest = $this->saveWorkFlowTwoStep($employee, $AssistantCommissioner, $DistrictSecretory, $transRequest, 10, 9);
                } else if ($levelCode == $DistrictLevel) {
                    //Intra District Transfers

                    $transRequest = $this->saveWorkFlowTwoStep($employee, $DirectorEstablishment, $DirectorGeneral, $transRequest, 12, 11);
                } else if ($levelCode == $ProvinceLevel) {
                    //Intra National Level Transfers
//die($levelCode);

                    $comDisplayCode=$transferService->getComCode($level2)->comp_code;
//                    die($level2);
                    if($comDisplayCode==$sysConf->headOfficeCode){


                    $IsManager = $transferService->isEmployeeInGroup($employee, $Manager);
                    $IsSamurdhiDevelopmentOfficer = $transferService->isEmployeeInGroup($employee, $SamurdhiDevelopmentOfficer);

                    if ($IsManager != null || $IsSamurdhiDevelopmentOfficer != null) {
                        //Manager or SDO

                        $transRequest = $this->saveWorkFlowTwoStep($employee, $DirectorEstablishment, $DirectorGeneral, $transRequest, 12, 11);
                    } else {

                        //others Employee
                        $transRequest = $this->saveWorkFlowTwoStep($employee, $DirectorAdministration, $DirectorGeneral, $transRequest, 14, 13);
                    }
                    }else{
                        $transRequest = $this->saveWorkFlowOneStep($employee, $DirectorGeneral, $transRequest, 15);
                    }

                }

                $transferService->saveTransferRequest($transRequest);
                $conn->commit();
                if (strlen($requestId)) {
                    $this->setMessage('SUCCESS', array($this->getContext()->geti18n()->__('Successfully Updated')));
                    $this->redirect('transfer/SaveTransferRequestAdmin?id=' . $requestId . '&lock=0');
                } else {
                    $this->setMessage('SUCCESS', array($this->getContext()->geti18n()->__('Successfully Added')));
                    $this->redirect('transfer/TransferRequestAdmin');
                }
            }
        } catch (Doctrine_Connection_Exception $e) {
            $errMsg = new CommonException($e->getPortableMessage(), $e->getPortableCode());
            $this->setMessage('WARNING', $errMsg->display());
            $this->redirect('transfer/TransferRequestAdmin');
        } catch (sfStopException $sf) {
            
        } catch (Exception $e) {
            $errMsg = new CommonException($e->getMessage(), $e->getCode());
            $this->setMessage('WARNING', $errMsg->display());
            $this->redirect('transfer/TransferRequestAdmin');
        }
    }

    /**
     * Executes TransferRequest action
     *
     * @param sfRequest $request A request object
     */
    public function executeTransferRequest(sfWebRequest $request) {
        try {

            $user = $request->getParameter('user');

            $this->user = $user;
            $this->Culture = $this->getUser()->getCulture();

            $transSearchService = new TransferSearchService();
            $this->transSearchService = $transSearchService;

            $this->sorter = new ListSorter('TransferReason', 'transfer', $this->getUser(), array('tr.trans_req_id', ListSorter::ASCENDING));

            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));

//            if ($request->getParameter('mode') == 'search') {
//                if ($request->getParameter('searchMode') != 'all' && trim($request->getParameter('searchValue')) == '') {
//                    $this->setMessage('NOTICE', array('Select the field to search'));
//                    $this->redirect('transfer/TransferReqestAdmin');
//                }
//            }
            $this->searchMode = ($request->getParameter('searchMode') == '') ? 'all' : $request->getParameter('searchMode');
            $this->searchValue = ($request->getParameter('searchValue') == '') ? '' : $request->getParameter('searchValue');

            $this->sort = ($request->getParameter('sort') == '') ? 'tr.trans_req_id' : $request->getParameter('sort');
            $this->order = ($request->getParameter('order') == '') ? 'DESC' : $request->getParameter('order');

            $res = $transSearchService->searchTransferRequest($this->searchMode, $this->searchValue, $this->culture, $request->getParameter('page'), $this->sort, $this->order, $this->user);
            $this->listTransferRequest = $res['data'];
            //die($this->listreasons);
            $this->pglay = $res['pglay'];
            $this->pglay->setTemplate('<a href="{%url}">{%page}</a>');
            $this->pglay->setSelectedTemplate('{%page}');
        } catch (sfStopException $sf) {
            
        } catch (Exception $e) {
            $errMsg = new CommonException($e->getMessage(), $e->getCode());
            $this->setMessage('WARNING', $errMsg->display());

            $this->redirect('default/error');
        }
    }

    /**
     * Executes SaveTransferRequest action
     *
     * @param sfRequest $request A request object
     */
    public function executeSaveTransferRequest(sfWebRequest $request) {

        $encrypt = new EncryptionHandler();
        $sysConf = OrangeConfig::getInstance()->getSysConf();
        $Headoffice = $sysConf->Headoffice;

        if ($_SESSION['user'] == "USR001") {
            $this->setMessage('WARNING', array($this->getContext()->getI18N()->__("Admin Not Allow to enter a Transfer Request.", $args, 'messages')));
            $this->redirect('transfer/TransferRequest');
        }

        try {
            if (!strlen($request->getParameter('lock'))) {
                $this->mode = 0;
            } else {
                $this->mode = $request->getParameter('lock');
            }
            $ebLockid = $encrypt->decrypt($request->getParameter('id'));

            if (isset($this->mode)) {
                if ($this->mode == 1) {

                    $conHandler = new ConcurrencyHandler();

                    $recordLocked = $conHandler->setTableLock(' hs_hr_transfer_request', array($ebLockid), 1);

                    if ($recordLocked) {
                        // Display page in edit mode
                        $this->mode = 1;
                    } else {

                        $this->setMessage('WARNING', array($this->getContext()->getI18N()->__('Can not update. Record locked by another user.', $args, 'messages')), false);
                        $this->mode = 0;
                    }
                } else if ($this->mode == 0) {
                    $conHandler = new ConcurrencyHandler();
                    $recordLocked = $conHandler->resetTableLock(' hs_hr_transfer_request', array($ebLockid), 1);
                    $this->mode = 0;
                }
            }


            $this->Culture = $this->getUser()->getCulture();
            $transferService = new TransferService();
            $this->currentEmpId = $_SESSION['empNumber'];

            $this->listLevel = $transferService->getLevelList();
            $requestId = $request->getParameter('id');
            if (strlen($requestId)) {
                $requestId = $encrypt->decrypt($request->getParameter('id'));
                if (!strlen($this->mode)) {
                    $this->mode = 0;
                }
                $this->transferRequestGetById = $transferService->readTransferReasonbyId($requestId);

//                if (!$this->transferRequestGetById) {
//                    $this->setMessage('WARNING', array($this->getContext()->geti18n()->__('Record Not Found')));
//                    $this->redirect('transfer/TransferRequest');
//                }
            } else {
                $this->mode = 1;
            }

            if ($request->isMethod('post')) {

               $transRequest=$transferService->saveTransferRequestSub($request);

               
                $transRequest->setTrans_req_status(2);

                $roleAdmin = 6;
                $roleSect = 12;

                $sysConf = OrangeConfig::getInstance()->getSysConf();

                $WasamLevel = $sysConf->TransferWasamLevel;
                $ZonalLevel = $sysConf->TransferZonalLevel;
                $DivisionLevel = $sysConf->TransferDivisionLevel;
                $DistrictLevel = $sysConf->TransferDistrictLevel;
                $ProvinceLevel = $sysConf->TransferProvinceLevel;
                $NationalLevel = $sysConf->TransferNationalLevel;
                $DivisionSecretory = $sysConf->TransferDivisionSecretory;
                $DistrictSecretory = $sysConf->TransferDistrictSecretory;
                $ZonalManager = $sysConf->TransferZonalManager;
                $AssistantCommissioner = $sysConf->TransferAssistantCommissioner;
                $DirectorEstablishment = $sysConf->TransferDirectorEstablishment;
                $DirectorGeneral = $sysConf->TransferDirectorGeneral;
                $DirectorAdministration = $sysConf->TransferDirectorAdministration;
                $SamurdhiDevelopmentOfficer = $sysConf->TransferSamurdhiDevelopmentOfficer;
                $Manager = $sysConf->TransferManager;






                if (strlen($request->getParameter('txtEmpId'))) {
                    $employeeID = $request->getParameter('txtEmpId');
                }

                $conn = Doctrine_Manager::getInstance()->connection();
                $conn->beginTransaction();
                $level2=$request->getParameter('cmdLevel2');
                //Current LogUser
                $employee = $_SESSION['empNumber'];
                //new start here
                $currentEmpDiv=$transferService->readGetEmployeeId($employee)->work_station;
                $currentEmpDistrict=$transferService->readGetEmployeeId($employee)->hie_code_3;
                $prefedDivisionArr=$transferService->getPreferedDivisionDistrict($level2);
                //if in a same District
                if(in_array($currentEmpDistrict, $prefedDivisionArr)) {
                   $isSameDistrict="1";
                }else{
                   $isSameDistrict="0";
                }
               
                //new end here
                if (strlen($request->getParameter('cmbLevel'))) {
                    $levelCode = $request->getParameter('cmbLevel');
                }
                /*
                $wfDao = new workflowDao();
                if ($levelCode == $WasamLevel) {
                    //Intra Wasam Transfers
                    
                    $transRequest = $this->saveWorkFlowTwoStepWithDis($employee, $DivisionSecretory, $ZonalManager, $transRequest, 8, 7,$isSameDistrict,20);
                } else if ($levelCode == $ZonalLevel) {
                    //Intra Zonal Transfers
                 
                    $transRequest = $this->saveWorkFlowOneStep($employee, $DivisionSecretory, $transRequest, 8);
                } else if ($levelCode == $DivisionLevel) {
                    //Intra Divisional Transfers
                    $transRequest = $this->saveWorkFlowTwoStep($employee, $AssistantCommissioner, $DistrictSecretory, $transRequest, 10, 9);
                } else if ($levelCode == $DistrictLevel) {
                    //Intra District Transfers
                   
                    $transRequest = $this->saveWorkFlowTwoStep($employee, $DirectorEstablishment, $DirectorGeneral, $transRequest, 12, 11);
                } else if ($levelCode == $ProvinceLevel) {
                    //Intra National Level Transfers
//die($levelCode);

                    $comDisplayCode=$transferService->getComCode($level2)->comp_code;
//                    die($level2);
                    if($comDisplayCode==$sysConf->headOfficeCode){
                  
                    
                    $IsManager = $transferService->isEmployeeInGroup($employee, $Manager);
                    $IsSamurdhiDevelopmentOfficer = $transferService->isEmployeeInGroup($employee, $SamurdhiDevelopmentOfficer);
               
                    if ($IsManager != null || $IsSamurdhiDevelopmentOfficer != null) {
                        //Manager or SDO
                      
                        $transRequest = $this->saveWorkFlowTwoStep($employee, $DirectorEstablishment, $DirectorGeneral, $transRequest, 12, 11);
                    } else {
                       
                        //others Employee
                        $transRequest = $this->saveWorkFlowTwoStep($employee, $DirectorAdministration, $DirectorGeneral, $transRequest, 14, 13);
                    }
                    }else{
                        $transRequest = $this->saveWorkFlowOneStep($employee, $DirectorGeneral, $transRequest, 15);
                    }

                } */

                $transferService->saveTransferRequest($transRequest);
                $conn->commit();
                if (strlen($requestId)) {
                    $this->setMessage('SUCCESS', array($this->getContext()->geti18n()->__('Successfully Updated')));
                    $this->redirect('transfer/SaveTransferRequest?id=' . $requestId . '&lock=0');
                } else {
                    $this->setMessage('SUCCESS', array($this->getContext()->geti18n()->__('Successfully Added')));
                    $this->redirect('transfer/TransferRequest');
                }
            }
        } catch (Doctrine_Connection_Exception $e) {
            $errMsg = new CommonException($e->getPortableMessage(), $e->getPortableCode());
            $this->setMessage('WARNING', $errMsg->display());
            $this->redirect('transfer/TransferRequest');
        } catch (sfStopException $sf) {
            //$this->redirect('transfer/TransferRequest');
        } catch (Exception $e) {
            $errMsg = new CommonException($e->getMessage(), $e->getCode());
            $this->setMessage('WARNING', $errMsg->display());
            $this->redirect('transfer/TransferRequest');
        }
    }

    /**
     * Executes TransferDetail action
     *
     * @param sfRequest $request A request object
     */
    public function executeTransferDetail(sfWebRequest $request) {
        try {
            $this->Culture = $this->getUser()->getCulture();

            $transSearchService = new TransferSearchService();
            $this->transSearchService = $transSearchService;

            $this->sorter = new ListSorter('TransferReason', 'transfer', $this->getUser(), array('t.trans_id', ListSorter::ASCENDING));

            $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));

//            if ($request->getParameter('mode') == 'search') {
//                if ($request->getParameter('searchMode') != 'all' && trim($request->getParameter('searchValue')) == '') {
//                    $this->setMessage('NOTICE', array('Select the field to search'));
//                    $this->redirect('transfer/TransferDetail');
//                }
//            }
            $this->searchMode = ($request->getParameter('searchMode') == '') ? 'all' : $request->getParameter('searchMode');
            $this->searchValue = ($request->getParameter('searchValue') == '') ? '' : $request->getParameter('searchValue');

            $this->sort = ($request->getParameter('sort') == '') ? 't.trans_id' : $request->getParameter('sort');
            $this->order = ($request->getParameter('order') == '') ? 'ASC' : $request->getParameter('order');

            $res = $transSearchService->searchTransferDetails($this->searchMode, $this->searchValue, $this->culture, $request->getParameter('page'), $this->sort, $this->order);
            $this->listTransferDetail = $res['data'];

            //die($this->listreasons);
            $this->pglay = $res['pglay'];
            $this->pglay->setTemplate('<a href="{%url}">{%page}</a>');
            $this->pglay->setSelectedTemplate('{%page}');
        } catch (sfStopException $sf) {
            
        } catch (Exception $e) {
            $errMsg = new CommonException($e->getMessage(), $e->getCode());
            $this->setMessage('WARNING', $errMsg->display());
            $this->redirect('default/error');
        }
    }

    /**
     * Executes SaveTransferDetail action
     *
     * @param sfRequest $request A request object
     */
    public function executeSaveTransferDetail(sfWebRequest $request) {


        
        $this->Culture = $this->getUser()->getCulture();
        $transferService = new TransferService();
        $encrypt = new EncryptionHandler();
        $this->listLevel = $transferService->getLevelList();
        $transfer = new Transfer();
        $transattach = new TransferAttach();
        $this->transResList = $transferService->fetchTransferReason();
        
        if($request->getParameter('update')=="yes"){
        if (!strlen($request->getParameter('lock'))) {
            $this->lockMode = 0;
        } else {
            $this->lockMode = $request->getParameter('lock');
        }

        
        
        $requestId = $encrypt->decrypt($request->getParameter('id'));
        //if (strlen($requestId)){
            
        if (isset($this->lockMode)) {
            if ($this->lockMode == 1) {

                $conHandler = new ConcurrencyHandler();

                $recordLocked = $conHandler->setTableLock('hs_hr_transfer', array($requestId), 1);

                if ($recordLocked) {
                    // Display page in edit mode
                    $this->lockMode = 1;
                } else {
                    $this->setMessage('WARNING', array($this->getContext()->getI18N()->__('Can not update. Record locked by another user.', $args, 'messages')), false);
                    $this->lockMode = 0;
                }
            } else if ($this->lockMode == 0) {
                $conHandler = new ConcurrencyHandler();
                $recordLocked = $conHandler->resetTableLock('hs_hr_transfer', array($requestId), 1);
                $this->lockMode = 0;
            }
            
         $this->transfer = $transferService->readTranfer($requestId);
         $transfer=$this->transfer;
        if (!$this->transfer) {
            $this->setMessage('WARNING', array($this->getContext()->getI18N()->__('Can not update. Record has been Deleted', $args, 'messages')));
            $this->redirect('transfer/TransferDetail');
        }
           $this->update="Update";            
         $this->DivList = $transferService->loadSubList($transfer->TransferPreferCompanyStructure->def_level);
         
         
        
        }
        
        //}
        
        }else{
            $this->update="";
            $this->transfer = new Transfer();
            //$this->DivList=null;
        }        

        if ($request->isMethod('post')) {//die(print_r($_POST));

               try { 
                if($this->update=="Update"){ 
                    $transfer = $transferService->readTranfer($request->getParameter('trnid'));
                }else{
                    $transfer = new Transfer();
                }

                $sysConfinst = OrangeConfig::getInstance()->getSysConf();
                $sysConfs = new sysConf();

                if (array_key_exists('letup', $_FILES)) {
                    foreach ($_FILES as $file) {

                        if ($file['tmp_name'] > '') {
                            if (!in_array(end(explode(".", strtolower($file['name']))), $sysConfs->allowedExtensions)) {
                                throw new Exception("Invalid File Type", 8);
                            }
                        }
                    }
                } else {
                    throw new Exception("Invalid File Type", 6);
                }

                $fileName = $_FILES['letup']['name'];
                $tmpName = $_FILES['letup']['tmp_name'];
                $fileSize = $_FILES['letup']['size'];
                $fileType = $_FILES['letup']['type'];


                $maxsize = $sysConfs->getMaxFilesize2();

                if ($fileSize > $maxsize) {
                    throw new Exception("Maxfile size  Should be less than 5MB", 5);
                }

                $fp = fopen($tmpName, 'r');
                $content = fread($fp, filesize($tmpName));
                $content = addslashes($content);
         
                fclose($fp); //echo "<pre>-".$request->getParameter('txtLetterID')."-";die;



            $employee = $transferService->getJoinedDate($request->getParameter('txtEmpId'));
            $this->employee = $employee;

            //$currentWorkStation = $employee->work_station;
            $currentWorkStation = $request->getParameter('txtcurrentDiv');
            $joindate = $employee->getEmp_com_date();
            $jointimest = strtotime(date($joindate));
            $transtimest = strtotime(LocaleUtil::getInstance()->convertToStandardDateFormat($request->getParameter('effdate')));

            if ($transtimest < $jointimest) {
                $this->setMessage('WARNING', array($this->getContext()->getI18N()->__('EffectiveDate should be Greater than or equal to joinedDate', $args, 'messages')));
                $this->redirect('transfer/TransferDetail');
            }
         
                $transfer->setTrans_emp_number((int) $request->getParameter('txtEmpId'));

                if ($currentWorkStation != null) {
                    $transfer->setTrans_currentdiv_id($currentWorkStation);
                } else {
                    $transfer->setTrans_currentdiv_id();
                }
                if (strlen($request->getParameter('cmbLocation'))) {
                    $transfer->setTrans_div_id($request->getParameter('cmbLocation'));
                } else {
                    $transfer->setTrans_div_id();
                }
                if (strlen($request->getParameter('txtLetterID'))) {
                    $transfer->setTrans_letter_ld($request->getParameter('txtLetterID'));
                } else {
                    $transfer->setTrans_letter_ld(null);
                }
                if (strlen($request->getParameter('reasons'))) {
                    $transfer->setTrans_reason_id((int) $request->getParameter('reasons'));
                } else {
                    $transfer->setTrans_reason_id();
                }
                if ((int) $request->getParameter('TLocation') == 2) {
                    $transfer->setTrans_location($request->getParameter('TLocation'));
                }
                $transfer->setTrans_Mutual($request->getParameter('isMutual'));
                if($request->getParameter('txtMutualEmpId')!=null){
                $transfer->setTrans_mu_name($request->getParameter('txtMutualEmpId'));
                }else{
                 $transfer->setTrans_mu_name(null);   
                }
                if ($request->getParameter('effdate') != null) {
                    $transfer->setTrans_effect_date(LocaleUtil::getInstance()->convertToStandardDateFormat($request->getParameter('effdate')));
                } else {
                    $transfer->setTrans_effect_date(null);
                }

                $transfer->setTrans_comment($request->getParameter('comment'));
                if($request->getParameter('cmdLevel2')){
                $transfer->setTrans_prefer_div_id($request->getParameter('cmdLevel2'));
                }else{
                $transfer->setTrans_prefer_div_id(null);    
                }

          
                $conn = Doctrine_Manager::getInstance()->connection();
                $conn->beginTransaction();

                //die(print_r($transfer->getData()));
                $transferService->saveNewTransfer($transfer);
               
                $lasttransid = $transferService->getLastTransferID();
                $transattach->setTrans_attach_name($fileName);
                $transattach->setTrans_attach_type($fileType);
                $transattach->setTrans_attach_content($content);
                $transattach->setTrans_id($lasttransid[0]['MAX']);
                $transferService->saveNewAttachment($transattach);
               

                $empMaster = $transferService->readGetEmployeeId($request->getParameter('txtEmpId'));
                if (strlen($request->getParameter('cmbLocation'))) {
                    $abc = $request->getParameter('cmbLocation');
                } else {
                    $abc = null;
                }
                if (strlen($abc)) {
                    $empMaster->setWork_station($abc);
                    $transferService->updateEmpMaster($empMaster);
                }

                $conn->commit();
                 //$this->lastid = $transferService->getLastTransferID();
            if($request->getParameter('trnid')!=null){     
            $this->setMessage('SUCCESS', array($this->getContext()->getI18N()->__('Successfully Updated', $args, 'messages')));
            $this->redirect('transfer/SaveTransferDetail?update=yes&id=' . $encrypt->encrypt($request->getParameter('trnid')));
            }else{
            $this->setMessage('SUCCESS', array($this->getContext()->getI18N()->__('Successfully Added', $args, 'messages')));
            $this->redirect('transfer/TransferDetail');
            }
            
             }catch (Doctrine_Connection_Exception $e) {
                $conn->rollBack();
                $errMsg = new CommonException($e->getPortableMessage(), $e->getPortableCode());
                $this->setMessage('WARNING', $errMsg->display());
                $this->redirect('transfer/TransferDetail');
            } catch (Exception $e) {
                $conn->rollBack();
                $errMsg = new CommonException($e->getMessage(), $e->getCode());
                $this->setMessage('WARNING', $errMsg->display());
//                die("error");
                $this->redirect('transfer/TransferDetail');
            }
        
                  
            
        }
    }

    
    public function executeUpdateTransfer(sfWebRequest $request) {

        try{
            $sysConfinst = OrangeConfig::getInstance()->getSysConf();
                $sysConfs = new sysConf();


        if (!strlen($request->getParameter('lock'))) {
            $this->lockMode = 0;
        } else {
            $this->lockMode = $request->getParameter('lock');
        }
        $encryptObj=new EncryptionHandler();

        $transPrid = $encryptObj->decrypt($request->getParameter('id'));
        $this->transferId=$transPrid;
//        echo ($transPrid);die;
        if (isset($this->lockMode)) {
            if ($this->lockMode == 1) {

                $conHandler = new ConcurrencyHandler();

                $recordLocked = $conHandler->setTableLock('hs_hr_transfer', array($transPrid), 1);

                if ($recordLocked) {
                    // Display page in edit mode
                    $this->lockMode = 1;
                } else {
                    //$this->setMessage('WARNING', array('Can not update. Record locked by another user.'),false);
                    $this->setMessage('WARNING', array($this->getContext()->getI18N()->__('Can not update. Record locked by another user.', $args, 'messages')), false);
                    $this->lockMode = 0;
                }
            } else if ($this->lockMode == 0) {

                $conHandler = new ConcurrencyHandler();
                $recordLocked = $conHandler->resetTableLock('hs_hr_transfer', array($transPrid), 1);
                $this->lockMode = 0;
            }
        }
        }
          catch (Exception $e) {
                $errMsg = new CommonException($e->getMessage(), $e->getCode());
                $this->setMessage('WARNING', $errMsg->display());
                 $this->redirect('transfer/TransferDetail');
            }


        $this->culture = $this->getUser()->getCulture();

     
         $transferService = new TransferService();
//         die($request->getParameter('id'));
        $transfer = $transferService->readTranfer($transPrid);
                if(!$transfer){
            $this->setMessage('WARNING', array($this->getContext()->getI18N()->__('Can not update. Record has been Deleted', $args, 'messages')));
            $this->redirect('transfer/TransferDetail');
        }


        $transattach = new TransferAttach();
        $this->transfer = $transfer;
        $this->transferDao = $transDao;


        if ($request->isMethod('post')) {


            try {

                if (array_key_exists('letup', $_FILES)) {
                     foreach ($_FILES as $file) {

                   if ($file['tmp_name'] > '') {
      if (!in_array(end(explode(".",strtolower($file['name']))),$sysConfs->allowedExtensions)) {
           throw new Exception("Invalid File Type", 8);

      }
    }
               }
                }

              else{
                  throw new Exception("Invalid File Type", 6);
              }


                $fileName = $_FILES['letup']['name'];
                $tmpName = $_FILES['letup']['tmp_name'];
                $fileSize = $_FILES['letup']['size'];
                $fileType = $_FILES['letup']['type'];

                 $sysConfinst = OrangeConfig::getInstance()->getSysConf();
                $sysConfs = new sysConf();


                $maxsize = $sysConfs->getMaxFilesize2();

                if ($fileSize > $maxsize) {

                    throw new Exception("Maxfile size  Should be less than 5MB", 5);
                }
            } catch (Exception $e) {
                $errMsg = new CommonException($e->getMessage(), $e->getCode());
                $this->setMessage('WARNING', $errMsg->display());
                $this->redirect('Transfer/UpdateTransfer?id=' . $transfer->getTrans_id() . '&lock=0');
            }

            $fp = fopen($tmpName, 'r');
            //die(filesize($tmpName));
            $content = fread($fp, filesize($tmpName));
            $content = addslashes($content);


            fclose($fp);

        $transfer->setTrans_emp_number((int) $request->getParameter('txtEmpId'));

                if ($currentWorkStation != null) {
                    $transfer->setTrans_currentdiv_id($currentWorkStation);
                } else {
                    $transfer->setTrans_currentdiv_id(null);
                }
                if (strlen($request->getParameter('cmdLevel2'))) {
                    $transfer->setTrans_div_id($request->getParameter('cmdLevel2'));
                } else {
                    $transfer->setTrans_div_id();
                }
                if (strlen($request->getParameter('reasons'))) {
                    $transfer->setTrans_reason_id((int) $request->getParameter('reasons'));
                } else {
                    $transfer->setTrans_reason_id();
                }
                if ((int) $request->getParameter('reasons') == 2) {
                    $transfer->setTrans_location($request->getParameter('TLocation'));
                }
                $transfer->setTrans_Mutual($request->getParameter('isMutual'));
                $transfer->setTrans_mu_name($request->getParameter('MTemployee'));
                if ($request->getParameter('effdate') != null) {
                    $transfer->setTrans_effect_date(LocaleUtil::getInstance()->convertToStandardDateFormat($request->getParameter('effdate')));
                } else {
                    $transfer->setTrans_effect_date(null);
                }

                $transfer->setTrans_comment($request->getParameter('comment'));
           


            try {

                $conn = Doctrine_Manager::getInstance()->connection();
                $conn->beginTransaction();

                $transDao->saveNewTransfer($transfer);
                $lasttransid = $transDao->getLastTransferID();
                if (strlen($content)) {



                    $attachId = $transfer->TransferAttach->getTrans_attach_id();

                    //die($attachId);
                    $transDao->deleteAttachment($request->getParameter('id'));

                    $transattach->setTrans_attach_name($fileName);
                    $transattach->setTrans_attach_type($fileType);
                    $transattach->setTrans_attach_content($content);
                    $transattach->setTrans_id($request->getParameter('id'));
                    $transDao->saveNewAttachment($transattach);
                }
                $conn->commit();
            }catch (Doctrine_Connection_Exception $e) {
                         $conn->rollBack();
                $errMsg = new CommonException($e->getPortableMessage(), $e->getPortableCode());
                $this->setMessage('WARNING',$errMsg->display());
                $this->redirect('transfer/TransferDetail');
            } catch (Exception $e) {
                $conn->rollBack();
                $errMsg = new CommonException($e->getMessage(), $e->getCode());
                $this->setMessage('WARNING', $errMsg->display());

                 $this->redirect('transfer/TransferDetail');
            }

            $empMaster=$transDao->readEmployeeByID($request->getParameter('txtEmpId'));
                if(strlen($request->getParameter('cmbLocation'))){
                    $abc=$request->getParameter('cmbLocation');
                }else{
                    $abc=null;
                }
                if(strlen($abc)){
                $empMaster->setWork_station($abc);
                $transDao->updateEmpMaster($empMaster);
                }

            $this->setMessage('SUCCESS', array($this->getContext()->getI18N()->__("Successfully Updated", $args, 'messages')));

            //$this->redirect('Transfer/NewTransfer');

            $this->redirect('Transfer/UpdateTransfer?id=' . $transfer->getTrans_id() . '&lock=0');
        }




        $this->transResList = $transferService->fetchTransferReason();
    }



    /**
     * Executes TransferRequestSummary action
     *
     * @param sfRequest $request A request object
     */
    public function executeTransferRequestSummary(sfWebRequest $request) {
        $this->forward('default', 'module');
    }

    /**
     * Executes GetEmployeeId Ajax function
     *
     * @param sfRequest $request A request object
     */
    public function executeGetEmployeeId(sfWebRequest $request) {

        //$loanService = new LoanService();
        $transferDao= new TransferDao();
        //$this->loanService = $loanService;

        $id = $request->getParameter('eid');
        $employee = $transferDao->getEmployeeId($id);
        $employeeID=$employee->employeeId;
        echo json_encode(array('EID' => $employeeID));
        die;
    }

    /**
     * Executes ComdateValidaiton Ajax function
     *
     * @param sfRequest $request A request object
     */
    public function executeComdateValidaiton(sfWebRequest $request) {

        $transDao = new TransferDao();
        $this->empid = $request->getParameter('sendValue');
        $nowTime = time();

        $comdate = $transDao->getComDate($this->empid);
        $comdate = $comdate[0]['emp_com_date'];
        $timeStampComdate = strtotime($comdate);

        if ($nowTime < $timeStampComdate) {
            $message = "error";
        } else {
            $message = "ok";
        }

        echo json_encode(array('message' => $message));
        die;
    }

    /**
     * Executes AjaxloadSubLevel Ajax function
     *
     * @param sfRequest $request A request object
     */
    public function executeAjaxloadSubLevel(sfWebRequest $request) {

        $this->Culture = $this->getUser()->getCulture();
        $Culture = $this->Culture;
        $id = $request->getParameter('lid');

        $transferService = new TransferService();
        $this->sublist = $transferService->loadSubList($id);
        $arr = Array();


        foreach ($this->sublist as $row) {
            $n = "title_" . $Culture;
            if ($row[$n] == null) {
                $n = "title";
            } else {
                $n = "title_" . $Culture;
            }
            $arr[$row['id']] = $row[$n];
        }

        echo json_encode($arr);
        die;
    }

    /**
     * Executes AjaxloadHeadOfficeDevision Ajax function
     *
     * @param sfRequest $request A request object
     */
    public function executeAjaxloadHeadOfficeDevision(sfWebRequest $request) {

        $this->Culture = $this->getUser()->getCulture();
        $Culture = $this->Culture;
        $id = $request->getParameter('gid');

        $transferService = new TransferService();
        $this->sublist = $transferService->loadHeadOfficeDevision($id);
        $arr = Array();

        foreach ($this->sublist as $row) {
            $n = "title_" . $Culture;
            if ($row[$n] == null) {
                $n = "title";
            } else {
                $n = "title_" . $Culture;
            }
            $arr[$row['id']] = $row[$n];
        }
        echo json_encode($arr);
        die;
    }

    /**
     * Executes SaveWorkFlowApprove action
     *
     * @param sfRequest $request A request object
     */
    public function executeSaveWorkFlowApprove(sfWebRequest $request) {
        $this->Culture = $this->getUser()->getCulture();
        $encryption = new EncryptionHandler();
        $this->wfID = $encryption->decrypt($request->getParameter('wfID'));
        if (strlen($this->wfID)) {
            $transferService = new TransferService();
            $this->transferReqest = $transferService->readWFRecord($this->wfID);
        }
    }

    /**
     * Executes WorkFlowApprove action
     *
     * @param sfRequest $request A request object
     */
    public function executeWorkFlowApprove(sfWebRequest $request) {

        $wfDao = new workflowDao();
        $transferService = new TransferService();
        $status = $request->getParameter('hiddenStatus');
        $wfID = $request->getParameter('hiddenWfMainID');
        $Comment = $request->getParameter('comment');
        
        try {
            $transferRequest = $transferService->readWFRecord($wfID);
            $conn = Doctrine_Manager::getInstance()->connection();
            $conn->beginTransaction();
            if ($status == 1) {
                $returnWF = $wfDao->approveApplication($wfID, $Comment);
                $WFRecord = $wfDao->getWorkFlowRecord($wfID);

                if ($returnWF == 1) {
                    if ($WFRecord[0][wftype_code] == 7 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(1);
                    } else if ($WFRecord[0][wftype_code] == 7 && $WFRecord[0][wfmain_sequence] == 2) {
                        $transferRequest->setTrans_req_status(2);
                    } else if ($WFRecord[0][wftype_code] == 8 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(3);
                    } else if ($WFRecord[0][wftype_code] == 9 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(3);
                    } else if ($WFRecord[0][wftype_code] == 10 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(1);
                    } else if ($WFRecord[0][wftype_code] == 10 && $WFRecord[0][wfmain_sequence] == 2) {
                        $transferRequest->setTrans_req_status(2);
                    } else if ($WFRecord[0][wftype_code] == 11 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(3);
                    } else if ($WFRecord[0][wftype_code] == 12 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(2);
                    } else if ($WFRecord[0][wftype_code] == 12 && $WFRecord[0][wfmain_sequence] == 2) {
                        $transferRequest->setTrans_req_status(3);
                    } else if ($WFRecord[0][wftype_code] == 13 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(3);
                    } else if ($WFRecord[0][wftype_code] == 14 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(3);
                    }
                    $transferService->saveTransferRequest($transferRequest);
                    $conn->commit();
                    $this->setMessage('SUCCESS', array('Successfully Approved'));
                    $this->redirect('workflow/ApprovalSummary');
                } else {
                    $this->setMessage('WARNING', array('Not Approved'));
                    $this->redirect('workflow/ApprovalSummary');
                }
            } else {
                $returnRWF = $wfDao->directApprovalReject($wfID, $Comment);
                if ($returnRWF == 1) {

                    if ($WFRecord[0][wftype_code] == 3 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(1);
                    } else if ($WFRecord[0][wftype_code] == 3 && $WFRecord[0][wfmain_sequence] == 2) {
                        $transferRequest->setTrans_req_status(2);
                    } else if ($WFRecord[0][wftype_code] == 3 && $WFRecord[0][wfmain_sequence] == 3) {
                        $transferRequest->setTrans_req_status(3);
                    } else if ($WFRecord[0][wftype_code] == 3 && $WFRecord[0][wfmain_sequence] == 4) {
                        $transferRequest->setTrans_req_status(4);
                    } else if ($WFRecord[0][wftype_code] == 4 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(2);
                    } else if ($WFRecord[0][wftype_code] == 4 && $WFRecord[0][wfmain_sequence] == 2) {
                        $transferRequest->setTrans_req_status(3);
                    } else if ($WFRecord[0][wftype_code] == 4 && $WFRecord[0][wfmain_sequence] == 3) {
                        $transferRequest->setTrans_req_status(4);
                    } else if ($WFRecord[0][wftype_code] == 5 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(3);
                    } else if ($WFRecord[0][wftype_code] == 5 && $WFRecord[0][wfmain_sequence] == 2) {
                        $transferRequest->setTrans_req_status(4);
                    } else if ($WFRecord[0][wftype_code] == 6 && $WFRecord[0][wfmain_sequence] == 1) {
                        $transferRequest->setTrans_req_status(4);
                    }
                    $transferService->saveTransferRequest($transferRequest);
                    $conn->commit();
                    $this->setMessage('SUCCESS', array('Successfully Rejected'));
                    $this->redirect('workflow/ApprovalSummary');
                } else {
                    $this->setMessage('WARNING', array('Not Rejected'));
                    $this->redirect('workflow/ApprovalSummary');
                }
            }
        } catch (Doctrine_Connection_Exception $e) {
            $conn->rollback();
            $errMsg = new CommonException($e->getPortableMessage(), $e->getPortableCode());
            $this->setMessage('WARNING', $errMsg->display());
            $this->redirect('workflow/ApprovalSummary');
        } catch (Exception $e) {
            $conn->rollback();
            $errMsg = new CommonException($e->getMessage(), $e->getCode());
            $this->setMessage('WARNING', $errMsg->display());

            $this->redirect('workflow/ApprovalSummary');
        }
        die;
    }

    /**
     * Executes saveWorkFlowOneStep action
     *
     * @param sfRequest $request A request object
     */
    public function saveWorkFlowOneStep($employee, $SecuGroupIdOne, $transRequest, $wfStep) {

        $wfDao = new workflowDao();
        $transferService = new TransferService();
        $CurentUserOne = $transferService->isEmployeeInGroup($employee, $SecuGroupIdOne);
//       die(print_r($CurentUserOne));

        if ($CurentUserOne != null) {
            $transRequest->setTrans_req_status('3');
            $transRequest->setTrans_req_isapproved('1');
//            $transRequest->setWfmain_id(1);
//            $transRequest->setWfmain_sequence(1);
        } else {
            $transRequest->setTrans_req_status('1');
            $returnWF = $wfDao->startWorkFlow($wfStep, $employee);
            if ($returnWF == null || $returnWF[0] == "-1") {
                throw new CommonException(null, 105);
            } else {
                $transRequest->setWfmain_id($returnWF[0]);
                $transRequest->setWfmain_sequence(1);
            }
        }
        return $transRequest;
    }



    public function saveWorkFlowTwoStep($employee, $SecuGroupIdOne, $SecuGroupIdTwo, $transRequest, $wfStepOne, $wfStepTwo) {

        $wfDao = new workflowDao();
        $transferService = new TransferService();



        $CurentUserOne = $transferService->isEmployeeInGroup($employee, $SecuGroupIdOne);

        if ($CurentUserOne != null) {

            $transRequest->setTrans_req_status('3');
            $transRequest->setTrans_req_isapproved('1');
//            $transRequest->setWfmain_id(1);
//            $transRequest->setWfmain_sequence(1);
        } else {
            $CurentUserTwo = $transferService->isEmployeeInGroup($employee, $SecuGroupIdTwo);
            if ($CurentUserTwo != null) {
                //type 8 get Call

                $transRequest->setTrans_req_status('2');
                $returnWF = $wfDao->startWorkFlow($wfStepOne, $employee);
            } else {
                //type 7 get Call
                $transRequest->setTrans_req_status('1');
                $returnWF = $wfDao->startWorkFlow($wfStepTwo, $employee);
            }
            if ($returnWF == null || $returnWF[0] == "-1") {
                throw new CommonException(null, 105);
            } else {
                $transRequest->setWfmain_id($returnWF[0]);
                $transRequest->setWfmain_sequence(1);
            }
        }
        return $transRequest;
    }

    //Save Workflow Two with Distrcit
    /**
     * Executes saveWorkFlowTwoStep action
     *
     * @param sfRequest $request A request object
     */
    public function saveWorkFlowTwoStepWithDis($employee, $SecuGroupIdOne, $SecuGroupIdTwo, $transRequest, $wfStepOne, $wfStepTwo,$isSameDistrict,$wfStepThree) {

        $wfDao = new workflowDao();
        $transferService = new TransferService();

        $CurentUserOne = $transferService->isEmployeeInGroup($employee, $SecuGroupIdOne);
        if($isSameDistrict=="0"){

           $returnWF = $wfDao->startWorkFlow($wfStepThree, $employee);
            if ($returnWF == null || $returnWF[0] == "-1") {
                throw new CommonException(null, 105);
            } else {
                $transRequest->setWfmain_id($returnWF[0]);
                $transRequest->setWfmain_sequence(1);
            }
        }else{
        if ($CurentUserOne != null) {

           
                $transRequest->setTrans_req_status('3');
                $transRequest->setTrans_req_isapproved('1');
           

        } else {
            $CurentUserTwo = $transferService->isEmployeeInGroup($employee, $SecuGroupIdTwo);

                if ($CurentUserTwo != null) {
                    //type 8 get Call
                   
                         $transRequest->setTrans_req_status('2');
                         $returnWF = $wfDao->startWorkFlow($wfStepOne, $employee);
                  
                } else {
                    //type 7 get Call
                   
                    $transRequest->setTrans_req_status('1');
                    $returnWF = $wfDao->startWorkFlow($wfStepTwo, $employee);
                  
                }

          
            if ($returnWF == null || $returnWF[0] == "-1") {
                throw new CommonException(null, 105);
            } else {
                $transRequest->setWfmain_id($returnWF[0]);
                $transRequest->setWfmain_sequence(1);
            }
        }
      }
        return $transRequest;
    }

    /**
     * Executes AjaxCall ajax function
     *
     * @param sfRequest $request A request object
     */
    public function executeAjaxCall(sfWebRequest $request) {
        $this->culture = $this->getUser()->getCulture();
        $transferService = new TransferService();
        $this->value = $request->getParameter('sendValue');

        $ajaxdata = $transferService->ajaxData($this->value);


        if ($this->culture == "en") {
            $title = "title";
        } elseif ($this->culture == "si") {
            $title = "title_si";
        } elseif ($this->culture == "ta") {
            $title = "title_ta";
        }

        $this->title = $ajaxdata[0]['subDivision'][$title];
        if ($this->title == "") {
            $this->title = $ajaxdata[0]['subDivision']['title'];
        }
        $this->id = $ajaxdata[0]["work_station"];
        $this->path = $transferService->get_path($this->id, $this->culture);
        echo json_encode(array("Workstation"=> $this->path,"WorkstationId"=>$this->id));
        die;
    }

    /**
     * Executes DateValidation ajax function
     *
     * @param sfRequest $request A request object
     */
    public function executeDateValidation(sfWebRequest $request) {

        try {
            $transService = new TransferService();
            $employee = $transService->getJoinedDate($request->getParameter('empId'));
            $this->employee = $employee;
            $this->message;
            $joindate = $employee->getEmp_com_date();
            $jointimest = strtotime(date($joindate));
            $transtimest = strtotime(LocaleUtil::getInstance()->convertToStandardDateFormat($request->getParameter('sendValue')));

            $lasteffectiveDate = $transService->getLastEffectiveDate($request->getParameter('empId'));
            $lasteffectivetimestamp = strtotime($lasteffectiveDate[0]['MAX']);

            if ($transtimest <= $lasteffectivetimestamp) {
                $this->message = "error1";
            } elseif ($transtimest < $jointimest) {
                $this->message = "error";
            } else {
                $this->message = "ok";
            }
        } catch (Exception $e) {
            $errMsg = new CommonException($e->getMessage(), $e->getCode());
            $this->setMessage('WARNING', $errMsg->display());
            $this->redirect('transfer/SaveTransferDetail');
        }
    }

    /**
     * Executes AjaxloadTransferEmpDetails ajax function
     *
     * @param sfRequest $request A request object
     */
    public function executeAjaxloadTransferEmpDetails(sfWebRequest $request) {

        $this->Culture = $this->getUser()->getCulture();
        $Culture = $this->Culture;
        $id = $request->getParameter('gid');

        $transferService = new TransferService();
        $this->transferEmployee = $transferService->readGetEmployeeId($id);
        $arr = Array();

        foreach ($this->transferEmployee as $row) {

            $arr[0] = $row['emp_nic_no'];
            $firstName = "firstName_" . $Culture;
            if ($row[$firstName] == null) {
                $firstName = "firstName";
            } else {
                $firstName = "firstName_" . $Culture;
            }
            $lastName = "lastName_" . $Culture;
            if ($row[$lastName] == null) {
                $lastName = "lastName";
            } else {
                $lastName = "lastName_" . $Culture;
            }
            $arr[1] = $row[$firstName];
            $arr[2] = $row[$lastName];
            $arr[3] = $row['empNumber'];
        }
        echo json_encode($arr);
        die;
    }

     /**
     * Executes DeleteTransferReason action
     *
     * @param sfRequest $request A request object
     */
    public function executeDeleteTransfer(sfWebRequest $request) {

        if (count($request->getParameter('chkLocID')) > 0) {
            $transferService = new TransferService();
            try {
                $conn = Doctrine_Manager::getInstance()->connection();
                $conn->beginTransaction();
                $ids = array();
                $ids = $request->getParameter('chkLocID');
                $countArr = array();
                $saveArr = array();
                for ($i = 0; $i < count($ids); $i++) {
                    $conHandler = new ConcurrencyHandler();
                    $isRecordLocked = $conHandler->isTableLocked('hs_hr_transfer', array($ids[$i]), 1);
                    if ($isRecordLocked) {
                        $countArr = $ids[$i];
                    } else {
                        $saveArr = $ids[$i];
                        $transferService->deleteTransfer($ids[$i]);
                        $transferService->deleteTransferAttachement($ids[$i]);
                        $conHandler->resetTableLock('hs_hr_transfer', array($ids[$i]), 1);
                    }
                }

                $conn->commit();
            } catch (Doctrine_Connection_Exception $e) {

                $conn->rollBack();
                $errMsg = new CommonException($e->getPortableMessage(), $e->getPortableCode());
                $this->setMessage('WARNING', $errMsg->display());
                $this->redirect('transfer/TransferDetail');
            } catch (Exception $e) {
                $conn->rollBack();
                $errMsg = new CommonException($e->getMessage(), $e->getCode());
                $this->setMessage('WARNING', $errMsg->display());
                $this->redirect('transfer/TransferDetail');
            }
            if (count($saveArr) > 0 && count($countArr) == 0) {
                $this->setMessage('SUCCESS', array($this->getContext()->getI18N()->__("Successfully Deleted", $args, 'messages')));
            } elseif (count($saveArr) > 0 && count($countArr) > 0) {
                $this->setMessage('WARNING', array($this->getContext()->getI18N()->__("Some records are can not be deleted as them  Locked by another user ", $args, 'messages')));
            } elseif (count($saveArr) == 0 && count($countArr) > 0) {
                $this->setMessage('WARNING', array($this->getContext()->getI18N()->__("Can not delete as them  Locked by another user ", $args, 'messages')));
            }
        } else {
            $this->setMessage('NOTICE', array('Select at least one record to delete'));
        }
        $this->redirect('transfer/TransferDetail');
    }
    public function executeCheckIsHeadOffice(sfWebRequest $request){



                    $DivisionId=$request->getParameter('id');
                    $transferService = new TransferService();
                    $comDisplayCode=$transferService->getComCode($DivisionId)->comp_code;
                   
                    $sysConf=new sysConf();
//                     die($sysConf->headOfficeCode);
                    if($comDisplayCode===$sysConf->headOfficeCode){
                         $msg="1";
                    }else{
                        $msg="0";
                    }
                    echo json_encode($msg);
                    die;

       
    
    

        

   }
     public function executeLoadHeadOfficeList(sfWebRequest $request){


         $this->Culture = $this->getUser()->getCulture();
        $Culture = $this->Culture;
        $id = $request->getParameter('id');

        $transferService = new TransferService();
        $this->sublist = $transferService->LoadHeadOfficeList($id,$Culture);


        echo json_encode($this->sublist);
        die;
    }
  


    public function executeImagepop(sfWebRequest $request) {
 $TransferDao = new TransferDao();
        $encryption=new EncryptionHandler();
        $attachment = $TransferDao->getAttachdetails($encryption->decrypt($request->getParameter('id')));
        $outname = stripslashes($attachment[0]['trans_attach_content']);
        $type = stripslashes($attachment[0]['trans_attach_type']);
        $name = stripslashes($attachment[0]['trans_attach_name']);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header('Content-Description: File Transfer');           
        header("Content-type:" . $type);
        header('Content-disposition: attachment; filename=' . $name);
        echo($outname);
        die;
    }
    
    public function executeDeleteImage(sfWebRequest $request) {
        
        $encryption=new EncryptionHandler();
        $id = $encryption->decrypt($request->getParameter('id'));
        $TransferDao = new TransferDao();
        $encryption=new EncryptionHandler();
        

            $TransferDao->deleteImageAttach($id);

        $this->setMessage('SUCCESS', array($this->getContext()->getI18N()->__('Successfully Deleted', $args, 'messages')));
        $this->redirect('transfer/SaveTransferDetail?update=yes&id=' . $encryption->encrypt($id). '&lock=1');
    
        die;
    }


    /**
     * Set message
     */
    public function setMessage($messageType, $message = array()) {
        $this->getUser()->setFlash('messageType', $messageType);
        $this->getUser()->setFlash('message', $message);
    }

}
