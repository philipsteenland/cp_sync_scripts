<?php
$website_id = 12;
$debug = false;

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');
include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\pmo.php');

$patterns      = array(
	'[',
	']'
);
$replacements  = array(
	'`',
	'`'
);
$replacements2 = array(
	':',
	''
);


$i=0;


$run = false;

if($argv){
 if($argv[1]){

	$run = $argv[1];	 
 }
}

print_r($run);


//BEGIN TABLES


$tables[$i]['table'] = 'sales_itemaccounts'; 
$tables[$i]['source_table'] = 'itemaccounts'; 
$tables[$i]['batchcount'] = 1000;

$tables[$i]['check_del_sql1'] = 'SELECT ID as :columnname FROM `sales_itemaccounts` WHERE website_id = '.$website_id; 
$tables[$i]['check_del_sql2'] = 'SELECT count(ID) FROM [510].[dbo].[ItemAccounts] (nolock) WHERE ID = :ID'; 
$tables[$i]['check_del_sql3'] = 'DELETE FROM `sales_itemaccounts` WHERE ID = :ID AND website_id = '.$website_id; 

$tables[$i]['check_ins_sql1'] = 'SELECT ID as \':ID\' FROM [510].[dbo].[ItemAccounts] (nolock)'; 
$tables[$i]['check_ins_sql2'] = 'SELECT count(*) as ID FROM `sales_itemaccounts` WHERE ID IN ('.implode(',',array_fill(0, $tables[$i]['batchcount'], '?')).') AND website_id = '.$website_id; 


$tables[$i]['timestamp_column'] = 'timestamp';
$tables[$i]['columns'] = array(
		'[ID]' ,
        '[ItemCode]' ,
        '[AccountCode]' ,
        '[crdnr]' ,
        '[MainAccount]' ,
        '[ItemCodeAccount]' ,
        '[EANCode]' ,
        '[PurchaseCurrency]' ,
        '[PurchasePrice]' ,
        '[PurchaseVATCode]' ,
        '[PurchaseVATPerc]' ,
        '[PurchaseVATIncl]' ,
        '[PurchaseUnit]' ,
        '[PurchasePackage]',
        '[PurchaseUnitToInternalUnitFactor]' ,
        '[PurchaseUnitToPurchasePackageFactor]' ,
        '[PurchaseOrderSize]' ,
        '[DiscountMargin]' ,
        '[SalesPriceRecommended]' ,
        '[SlsPkgsPerPurPkg]' ,
        '[DeliveryTimeInDays]' ,
        '[DeliverableFromStock]' ,
        '[DocumentID]' ,
        '[SupplierPreference]' ,
        '[StatisticalFactor]' ,
        '[Warranty]' ,
        '[CountryOfOrigin]' ,
        '[Division]' ,
        '[syscreated]' ,
        '[syscreator]' ,
        '[sysmodified]' ,
        '[sysmodifier]' ,
        '[sysguid]' ,
        '[timestamp]' ,
        '[DropShip]' ,
        '[CountryOfAssembly]' ,
        '[Manufacturer]' 
);

$tables[$i]['columns2update'] = array(        
        '[ItemCode]' ,
        '[AccountCode]' ,
        '[crdnr]' ,
        '[MainAccount]' ,
        '[ItemCodeAccount]' ,
        '[EANCode]' ,
        '[PurchaseCurrency]' ,
        '[PurchasePrice]' ,
        '[PurchaseVATCode]' ,
        '[PurchaseVATPerc]' ,
        '[PurchaseVATIncl]' ,
        '[PurchaseUnit]' ,
        '[PurchasePackage]',
        '[PurchaseUnitToInternalUnitFactor]' ,
        '[PurchaseUnitToPurchasePackageFactor]' ,
        '[PurchaseOrderSize]' ,
        '[DiscountMargin]' ,
        '[SalesPriceRecommended]' ,
        '[SlsPkgsPerPurPkg]' ,
        '[DeliveryTimeInDays]' ,
        '[DeliverableFromStock]' ,
        '[DocumentID]' ,
        '[SupplierPreference]' ,
        '[StatisticalFactor]' ,
        '[Warranty]' ,
        '[CountryOfOrigin]' ,
        '[Division]' ,
        '[syscreated]' ,
        '[syscreator]' ,
        '[sysmodified]' ,
        '[sysmodifier]' ,
        '[sysguid]' ,
        '[timestamp]' ,
        '[DropShip]' ,
        '[CountryOfAssembly]' ,
        '[Manufacturer]'  
);


//NEXT

$i++;


$tables[$i]['table'] = 'sales_orsrg'; 
$tables[$i]['source_table'] = 'orsrg'; 
$tables[$i]['batchcount'] = 1000;

$tables[$i]['check_del_sql1'] = 'SELECT ID as :columnname FROM `sales_orsrg` WHERE aant_gelev = 0 AND website_id = '.$website_id; ; 
$tables[$i]['check_del_sql2'] = 'SELECT count(ID) FROM [510].[dbo].[orsrg] (nolock) WHERE ID = :ID'; 
$tables[$i]['check_del_sql3'] = 'DELETE FROM `sales_orsrg` WHERE ID = :ID AND website_id = '.$website_id;  

$tables[$i]['check_ins_sql1'] = 'SELECT ID as \':ID\' FROM [510].[dbo].[orsrg] (nolock)'; 
$tables[$i]['check_ins_sql2'] = 'SELECT count(*) as ID FROM `sales_orsrg` WHERE ID IN ('.implode(',',array_fill(0, $tables[$i]['batchcount'], '?')).') AND website_id = '.$website_id; 


$tables[$i]['timestamp_column'] = 'timestamp';
$tables[$i]['columns'] = array(
	'[ID]'
      ,'[ordernr]'
      ,'[regel]'
      ,'[afldat]'
      ,'[artcode]'
      ,'[ar_soort]'
      ,'[oms45]'
      ,'[oms45_f]'
      ,'[magcode]'
      ,'[afl_week]'
      ,'[uitgifte]'
      ,'[inkordernj]'
      ,'[aant_back]'
      ,'[esr_aantal]'
      ,'[aant_gelev]'
      ,'[aant_fakt]'
      ,'[reeds_fakt]'
      ,'[pakbon_afg]'
      ,'[btw_code]'
      ,'[prijslijst]'
      ,'[korting]'
      ,'[prijs83]'
      ,'[prijs_n]'
      ,'[gip]'
      ,'[vvp]'
      ,'[lengte]'
      ,'[breedte]'
      ,'[hoogte]'
      ,'[dimensie]'
      ,'[kstdrcode]'
      ,'[project]'
      ,'[pr_kstpl]'
      ,'[kstsrt]'
      ,'[projvrw_c]'
      ,'[maglok]'
      ,'[dummy5]'
      ,'[pr_bedr]'
      ,'[praf_reg]'
      ,'[prshisnr]'
      ,'[industrie]'
      ,'[kstplcode]'
      ,'[kort_flags]'
      ,'[explsrtart]'
      ,'[dummy10]'
      ,'[prod_order]'
      ,'[extra_pr]'
      ,'[statistnr]'
      ,'[taric]'
      ,'[landoorspr]'
      ,'[landherk]'
      ,'[landbest]'
      ,'[landabc]'
      ,'[land_iso]'
      ,'[transact_a]'
      ,'[transact_b]'
      ,'[vervoer]'
      ,'[plts_ll]'
      ,'[stelsel]'
      ,'[int_regio]'
      ,'[intra_lvcd]'
      ,'[trsshpm_cd]'
      ,'[container]'
      ,'[stateenh_i]'
      ,'[gewicht_bi]'
      ,'[boecode]'
      ,'[affiliates]'
      ,'[csacode]'
      ,'[regel_hfda]'
      ,'[qsrg_line]'
      ,'[serie_num]'
      ,'[aantal_ser]'
      ,'[unit]'
      ,'[av_ont_in]'
      ,'[koers]'
      ,'[bdr_ev_ed_val]'
      ,'[bdr_d_ev_val]'
      ,'[bdr_vat_val]'
      ,'[bdr_inv_d_val]'
      ,'[bdr_val]'
      ,'[instruction]'
      ,'[projectnr]'
      ,'[res_id]'
      ,'[purchaseordernr]'
      ,'[unitcode]'
      ,'[unitfactor]'
      ,'[ContractStartDate]'
      ,'[ContractEndDate]'
      ,'[TaxCode2]'
      ,'[TaxCode3]'
      ,'[TaxCode4]'
      ,'[TaxCode5]'
      ,'[TaxBasis2]'
      ,'[TaxBasis3]'
      ,'[TaxBasis4]'
      ,'[TaxBasis5]'
      ,'[TaxAmount1]'
      ,'[TaxAmount2]'
      ,'[TaxAmount3]'
      ,'[TaxAmount4]'
      ,'[TaxAmount5]'
      ,'[StatisticalFactor]'
      ,'[PlannedDate]'
      ,'[Requesteddate]'
      ,'[Originalplanneddate]'
      ,'[BlanketPoline]'
      ,'[reasoncode]'
      ,'[OriginalPoline]'
      ,'[Division]'
      ,'[PakbonNr]'
      ,'[Parent]'
      ,'[ImportationID]'
      ,'[IntrastatEnabled]'
      ,'[AllocationType]'
      ,'[QuantityReturn]'
      ,'[syscreated]'
      ,'[syscreator]'
      ,'[sysmodified]'
      ,'[sysmodifier]'
      ,'[sysguid]'
      ,'[timestamp]'
      ,'[csTxPlanned]'
      ,'[CSTxDelpropQty]'
      ,'[CSTxDelpropDate]'
      ,'[CSTxSetCode]'
      ,'[CSTxSetFactor]'
      ,'[CSScanner]'
      ,'[CSorsrgID]'
      ,'[CSExpTime]'
      ,'[CSImpTime]'
      ,'[CSKrat]'
      ,'[ChargeLineType]'
      ,'[LineCharges]'
      ,'[CSPickITCWOriginIsSalesDB]'
      ,'[CSPickITCWStatusCWDB]'
      ,'[CSPickITCWStatusSalesDB]'
      ,'[CSPickITCWSysGUIDCWDB]'
      ,'[CSPickITCWSysGUIDSalesDB]'
      ,'[CSPickITCWWarehouseCWDB]'
      ,'[CSPickITHandTerminalID]'
      ,'[CSPickITModifyQuantity]'
      ,'[CSPickITOrderPicker]'
      ,'[CSPickITState]'
      ,'[CSPickITQtyOrdered]'
      ,'[CSPickITQuantityPicked]'
      ,'[CSPickITToBackOrder]'
      ,'[CSPickITQuantityCrossDock]'
      ,'[CSPickITTransactionGUID]'
      ,'[CSPickITTransferLineID]'
      ,'[CSPickITShipmentSSCC]'
);

$tables[$i]['columns2update'] = array(
      '[ordernr]'
      ,'[regel]'
      ,'[afldat]'
      ,'[artcode]'
      ,'[ar_soort]'
      ,'[oms45]'
      ,'[oms45_f]'
      ,'[magcode]'
      ,'[afl_week]'
      ,'[uitgifte]'
      ,'[inkordernj]'
      ,'[aant_back]'
      ,'[esr_aantal]'
      ,'[aant_gelev]'
      ,'[aant_fakt]'
      ,'[reeds_fakt]'
      ,'[pakbon_afg]'
      ,'[btw_code]'
      ,'[prijslijst]'
      ,'[korting]'
      ,'[prijs83]'
      ,'[prijs_n]'
      ,'[gip]'
      ,'[vvp]'
      ,'[lengte]'
      ,'[breedte]'
      ,'[hoogte]'
      ,'[dimensie]'
      ,'[kstdrcode]'
      ,'[project]'
      ,'[pr_kstpl]'
      ,'[kstsrt]'
      ,'[projvrw_c]'
      ,'[maglok]'
      ,'[dummy5]'
      ,'[pr_bedr]'
      ,'[praf_reg]'
      ,'[prshisnr]'
      ,'[industrie]'
      ,'[kstplcode]'
      ,'[kort_flags]'
      ,'[explsrtart]'
      ,'[dummy10]'
      ,'[prod_order]'
      ,'[extra_pr]'
      ,'[statistnr]'
      ,'[taric]'
      ,'[landoorspr]'
      ,'[landherk]'
      ,'[landbest]'
      ,'[landabc]'
      ,'[land_iso]'
      ,'[transact_a]'
      ,'[transact_b]'
      ,'[vervoer]'
      ,'[plts_ll]'
      ,'[stelsel]'
      ,'[int_regio]'
      ,'[intra_lvcd]'
      ,'[trsshpm_cd]'
      ,'[container]'
      ,'[stateenh_i]'
      ,'[gewicht_bi]'
      ,'[boecode]'
      ,'[affiliates]'
      ,'[csacode]'
      ,'[regel_hfda]'
      ,'[qsrg_line]'
      ,'[serie_num]'
      ,'[aantal_ser]'
      ,'[unit]'
      ,'[av_ont_in]'
      ,'[koers]'
      ,'[bdr_ev_ed_val]'
      ,'[bdr_d_ev_val]'
      ,'[bdr_vat_val]'
      ,'[bdr_inv_d_val]'
      ,'[bdr_val]'
      ,'[instruction]'
      ,'[projectnr]'
      ,'[res_id]'
      ,'[purchaseordernr]'
      ,'[unitcode]'
      ,'[unitfactor]'
      ,'[ContractStartDate]'
      ,'[ContractEndDate]'
      ,'[TaxCode2]'
      ,'[TaxCode3]'
      ,'[TaxCode4]'
      ,'[TaxCode5]'
      ,'[TaxBasis2]'
      ,'[TaxBasis3]'
      ,'[TaxBasis4]'
      ,'[TaxBasis5]'
      ,'[TaxAmount1]'
      ,'[TaxAmount2]'
      ,'[TaxAmount3]'
      ,'[TaxAmount4]'
      ,'[TaxAmount5]'
      ,'[StatisticalFactor]'
      ,'[PlannedDate]'
      ,'[Requesteddate]'
      ,'[Originalplanneddate]'
      ,'[BlanketPoline]'
      ,'[reasoncode]'
      ,'[OriginalPoline]'
      ,'[Division]'
      ,'[PakbonNr]'
      ,'[Parent]'
      ,'[ImportationID]'
      ,'[IntrastatEnabled]'
      ,'[AllocationType]'
      ,'[QuantityReturn]'
      ,'[syscreated]'
      ,'[syscreator]'
      ,'[sysmodified]'
      ,'[sysmodifier]'
      ,'[sysguid]'
      ,'[timestamp]'
      ,'[csTxPlanned]'
      ,'[CSTxDelpropQty]'
      ,'[CSTxDelpropDate]'
      ,'[CSTxSetCode]'
      ,'[CSTxSetFactor]'
      ,'[CSScanner]'
      ,'[CSorsrgID]'
      ,'[CSExpTime]'
      ,'[CSImpTime]'
      ,'[CSKrat]'
      ,'[ChargeLineType]'
      ,'[LineCharges]'
      ,'[CSPickITCWOriginIsSalesDB]'
      ,'[CSPickITCWStatusCWDB]'
      ,'[CSPickITCWStatusSalesDB]'
      ,'[CSPickITCWSysGUIDCWDB]'
      ,'[CSPickITCWSysGUIDSalesDB]'
      ,'[CSPickITCWWarehouseCWDB]'
      ,'[CSPickITHandTerminalID]'
      ,'[CSPickITModifyQuantity]'
      ,'[CSPickITOrderPicker]'
      ,'[CSPickITState]'
      ,'[CSPickITQtyOrdered]'
      ,'[CSPickITQuantityPicked]'
      ,'[CSPickITToBackOrder]'
      ,'[CSPickITQuantityCrossDock]'
      ,'[CSPickITTransactionGUID]'
      ,'[CSPickITTransferLineID]'
      ,'[CSPickITShipmentSSCC]'
);





//NEXT


$i++;



$tables[$i]['table'] = 'sales_orkrg'; 
$tables[$i]['source_table'] = 'orkrg'; 

$tables[$i]['batchcount'] = 1000;

$tables[$i]['check_del_sql1'] = 'SELECT ID as :columnname FROM `sales_orkrg` WHERE afgehandld = 0 AND website_id = '.$website_id; 
$tables[$i]['check_del_sql2'] = 'SELECT count(ID) FROM [510].[dbo].[orkrg] WHERE ID = :ID'; 
$tables[$i]['check_del_sql3'] = 'DELETE FROM `sales_orkrg` WHERE ID = :ID AND website_id = '.$website_id; 
 
$tables[$i]['check_ins_sql1'] = 'SELECT ID as \':ID\' FROM [510].[dbo].[orkrg]'; 
$tables[$i]['check_ins_sql2'] = 'SELECT count(*) as ID FROM `sales_orkrg` WHERE ID IN ('.implode(',',array_fill(0, $tables[$i]['batchcount'], '?')).') AND website_id = '.$website_id; 
 
 
 
$tables[$i]['timestamp_column'] = 'timestamp';
$tables[$i]['columns'] = array(
	'[ID]',
	'[ordernr]',
	'[debnr]',
	'[fakdebnr]',
	'[verzdebnr]',
	'[naldebnr]',
	'[einddebnr]',
	'[refer]',
	'[refer1]',
	'[refer2]',
	'[refer3]',
	'[orddat]',
	'[afldat]',
	'[afl_week]',
	'[ordbv_afdr]',
	'[magcode]',
	'[selcode]',
	'[ex_artcode]',
	'[kstplcode]',
	'[routecode]',
	'[ord_soort]',
	'[fiattering]',
	'[ordbv_afgd]',
	'[afgehandld]',
	'[nettoprijs]',
	'[inv_in_vv]',
	'[btw_code]',
	'[btw_cd_ord]',
	'[valcode]',
	'[koers]',
	'[betcond]',
	'[levwijze]',
	'[stat_code]',
	'[pakbon_afg]',
	'[paklst_afg]',
	'[vrachtkost]',
	'[orderkost]',
	'[order_kort]',
	'[colli]',
	'[bruto_gew]',
	'[netto_gew]',
	'[tot_bdr]',
	'[user_id]',
	'[krgtext]',
	'[industrie]',
	'[betaald]',
	'[faknr]',
	'[pakbon_nr]',
	'[pakbon_dat]',
	'[status]',
	'[prod_order]',
	'[type_prod]',
	'[offerte_nr]',
	'[iso_taalcd]',
	'[iso_taalcd_f]',
	'[fakt_code]',
	'[vrz_adrcd]',
	'[vrz_adrnr]',
	'[nal_adrcd]',
	'[nal_adrnr]',
	'[represent_id]',
	'[eca_type]',
	'[cntr_id]',
	'[ecaordernr]',
	'[notesunid]',
	'[productline]',
	'[productversion]',
	'[productrelease]',
	'[int_vrw]',
	'[represent_id2]',
	'[represent_ideca]',
	'[crdnr]',
	'[klantn_lev]',
	'[calc_meth_pc]',
	'[calc_incl_vat]',
	'[fakcrdnr]',
	'[bdr_vat_ord_val]',
	'[bdr_vat_ship_val]',
	'[bdr_kb_val]',
	'[bdr_disc_val]',
	'[bdr_val]',
	'[bstwijze]',
	'[bdr_ev_val]',
	'[bdr_vat_val]',
	'[docnumber]',
	'[paymentmethod]',
	'[projectnr]',
	'[DocAttachmentID]',
	'[ord_debtor_name]',
	'[ord_AddressLine1]',
	'[ord_AddressLine2]',
	'[ord_AddressLine3]',
	'[ord_PostCode]',
	'[ord_City]',
	'[ord_StateCode]',
	'[ord_landcode]',
	'[ord_Phone]',
	'[ord_Fax]',
	'[ord_contactperson]',
	'[ord_predcode]',
	'[ord_cnt_job_desc]',
	'[ord_Initials]',
	'[del_debtor_name]',
	'[del_AddressLine1]',
	'[del_AddressLine2]',
	'[del_AddressLine3]',
	'[del_PostCode]',
	'[del_City]',
	'[del_StateCode]',
	'[del_landcode]',
	'[del_Phone]',
	'[del_Fax]',
	'[del_contactperson]',
	'[del_predcode]',
	'[del_cnt_job_desc]',
	'[del_Initials]',
	'[inv_debtor_name]',
	'[inv_AddressLine1]',
	'[inv_AddressLine2]',
	'[inv_AddressLine3]',
	'[inv_PostCode]',
	'[inv_City]',
	'[inv_StateCode]',
	'[inv_landcode]',
	'[inv_Phone]',
	'[inv_Fax]',
	'[inv_contactperson]',
	'[inv_predcode]',
	'[inv_cnt_job_desc]',
	'[inv_Initials]',
	'[Approver]',
	'[Approved]',
	'[DocumentID]',
	'[resulttype]',
	'[ServiceCall_ID]',
	'[invoice_method]',
	'[ord_contactemail]',
	'[del_contactemail]',
	'[inv_contactemail]',
	'[freefield1]',
	'[freefield2]',
	'[freefield3]',
	'[freefield4]',
	'[freefield5]',
	'[Picked]',
	'[Division]',
	'[ImATD]',
	'[ImETA]',
	'[ImATA]',
	'[ApplyLinkItem]',
	'[syscreated]',
	'[syscreator]',
	'[sysmodified]',
	'[sysmodifier]',
	'[sysguid]',
	'[timestamp]',
	'[CSStatus]',
	'[del_County]',
	'[AvalaraAddressValidated]',
	'[UseAvalaraTaxation]',
	'[IncoTermConfirmPrices]',
	'[IncoTermAcknowledgeOrder]',
	'[IncoTermCode]',
	'[IncoTermProperty]',
	'[IsEDI]',
	'[ApplyShippingCharges]',
	'[CSPickITCWDeliveryNoteCWDB]',
	'[CSPickITCWSysGUIDCWDB]',
	'[CSPickITCWSysGUIDSalesDB]',
	'[CSPickITCWWarehouseSalesDB]',
	'[CSPickITCWIsPOInSalesDB]',
	'[CSPickITOrderPicker]',
	'[CSPickITYourReference]',
	'[CSPICKITCWLatestTryInService]',
	'[CSPickITLastReceiptDate]'
);

$tables[$i]['columns2update'] = array(
	'[ordernr]',
	'[debnr]',
	'[fakdebnr]',
	'[verzdebnr]',
	'[naldebnr]',
	'[einddebnr]',
	'[refer]',
	'[refer1]',
	'[refer2]',
	'[refer3]',
	'[orddat]',
	'[afldat]',
	'[afl_week]',
	'[ordbv_afdr]',
	'[magcode]',
	'[selcode]',
	'[ex_artcode]',
	'[kstplcode]',
	'[routecode]',
	'[ord_soort]',
	'[fiattering]',
	'[ordbv_afgd]',
	'[afgehandld]',
	'[nettoprijs]',
	'[inv_in_vv]',
	'[btw_code]',
	'[btw_cd_ord]',
	'[valcode]',
	'[koers]',
	'[betcond]',
	'[levwijze]',
	'[stat_code]',
	'[pakbon_afg]',
	'[paklst_afg]',
	'[vrachtkost]',
	'[orderkost]',
	'[order_kort]',
	'[colli]',
	'[bruto_gew]',
	'[netto_gew]',
	'[tot_bdr]',
	'[user_id]',
	'[krgtext]',
	'[industrie]',
	'[betaald]',
	'[faknr]',
	'[pakbon_nr]',
	'[pakbon_dat]',
	'[status]',
	'[prod_order]',
	'[type_prod]',
	'[offerte_nr]',
	'[iso_taalcd]',
	'[iso_taalcd_f]',
	'[fakt_code]',
	'[vrz_adrcd]',
	'[vrz_adrnr]',
	'[nal_adrcd]',
	'[nal_adrnr]',
	'[represent_id]',
	'[eca_type]',
	'[cntr_id]',
	'[ecaordernr]',
	'[notesunid]',
	'[productline]',
	'[productversion]',
	'[productrelease]',
	'[int_vrw]',
	'[represent_id2]',
	'[represent_ideca]',
	'[crdnr]',
	'[klantn_lev]',
	'[calc_meth_pc]',
	'[calc_incl_vat]',
	'[fakcrdnr]',
	'[bdr_vat_ord_val]',
	'[bdr_vat_ship_val]',
	'[bdr_kb_val]',
	'[bdr_disc_val]',
	'[bdr_val]',
	'[bstwijze]',
	'[bdr_ev_val]',
	'[bdr_vat_val]',
	'[docnumber]',
	'[paymentmethod]',
	'[projectnr]',
	'[DocAttachmentID]',
	'[ord_debtor_name]',
	'[ord_AddressLine1]',
	'[ord_AddressLine2]',
	'[ord_AddressLine3]',
	'[ord_PostCode]',
	'[ord_City]',
	'[ord_StateCode]',
	'[ord_landcode]',
	'[ord_Phone]',
	'[ord_Fax]',
	'[ord_contactperson]',
	'[ord_predcode]',
	'[ord_cnt_job_desc]',
	'[ord_Initials]',
	'[del_debtor_name]',
	'[del_AddressLine1]',
	'[del_AddressLine2]',
	'[del_AddressLine3]',
	'[del_PostCode]',
	'[del_City]',
	'[del_StateCode]',
	'[del_landcode]',
	'[del_Phone]',
	'[del_Fax]',
	'[del_contactperson]',
	'[del_predcode]',
	'[del_cnt_job_desc]',
	'[del_Initials]',
	'[inv_debtor_name]',
	'[inv_AddressLine1]',
	'[inv_AddressLine2]',
	'[inv_AddressLine3]',
	'[inv_PostCode]',
	'[inv_City]',
	'[inv_StateCode]',
	'[inv_landcode]',
	'[inv_Phone]',
	'[inv_Fax]',
	'[inv_contactperson]',
	'[inv_predcode]',
	'[inv_cnt_job_desc]',
	'[inv_Initials]',
	'[Approver]',
	'[Approved]',
	'[DocumentID]',
	'[resulttype]',
	'[ServiceCall_ID]',
	'[invoice_method]',
	'[ord_contactemail]',
	'[del_contactemail]',
	'[inv_contactemail]',
	'[freefield1]',
	'[freefield2]',
	'[freefield3]',
	'[freefield4]',
	'[freefield5]',
	'[Picked]',
	'[Division]',
	'[ImATD]',
	'[ImETA]',
	'[ImATA]',
	'[ApplyLinkItem]',
	'[syscreated]',
	'[syscreator]',
	'[sysmodified]',
	'[sysmodifier]',
	'[sysguid]',
	'[timestamp]',
	'[CSStatus]',
	'[del_County]',
	'[AvalaraAddressValidated]',
	'[UseAvalaraTaxation]',
	'[IncoTermConfirmPrices]',
	'[IncoTermAcknowledgeOrder]',
	'[IncoTermCode]',
	'[IncoTermProperty]',
	'[IsEDI]',
	'[ApplyShippingCharges]',
	'[CSPickITCWDeliveryNoteCWDB]',
	'[CSPickITCWSysGUIDCWDB]',
	'[CSPickITCWSysGUIDSalesDB]',
	'[CSPickITCWWarehouseSalesDB]',
	'[CSPickITCWIsPOInSalesDB]',
	'[CSPickITOrderPicker]',
	'[CSPickITYourReference]',
	'[CSPICKITCWLatestTryInService]',
	'[CSPickITLastReceiptDate]',
);





$pmo=new PMO;
$Mag=new Mag;


if($run){	
	$table = $tables[$run];
	$tables = array();
	$tables[]=$table;
}



foreach($tables as $t){
	
	
	
	
	$timestamp = 0;
	
	$table = $t['table'];
	
	echo 'START:'.$table;
	
	
	$source_table = $t['source_table'];
	$timestamp_column = $t['timestamp_column']; 
	$columns =  $t['columns'];
	$columns2update = $t['columns2update'];
	$check_del_sql1 = $t['check_del_sql1'];
	$check_del_sql2 = $t['check_del_sql2'];
 	$check_del_sql3 = $t['check_del_sql3'];
	
	$check_ins_sql1 = $t['check_ins_sql1'];
 	$check_ins_sql2 = $t['check_ins_sql2'];
	$bc = $t['batchcount'];
	
	//CEHCK FOR RECORDS IF STILL EXISTS
	
	$del1 = $pmo->pmo->prepare($check_del_sql1);	
	$del2 = $pmo->db[0]->prepare($check_del_sql2);
	$del3 = $pmo->pmo->prepare($check_del_sql3);
	
	
	$del1->execute(array(':columnname'=>':ID'));
	
	
	while ($column = $del1->fetch(PDO::FETCH_ASSOC)) {	
		if ($debug)
			echo print_r($column) . "\n";
				
		$del2->execute($column);	
		
		$count = $del2->fetchColumn();
		
		if($count!=1){				
			$del3->execute($column);			
			print_r($column);							
		}
		echo 'd';
	}
	

	//INSERT NEW RECORDS

	$Mag->Mag_Timestamp("C:\\xampp\\htdocs\\shop\\jobscripts\\PHP.v1.Orders.$table.txt");
		
	$rs = $pmo->db[0]->prepare("SELECT " . implode(", ", $columns) . " FROM [510].[dbo].[$source_table] (nolock) WHERE CONVERT(INT, $timestamp_column) > ".$Mag->timestamp." ORDER BY $timestamp_column");
			
	$update = array();
	foreach ($columns2update as $v) {
		$v        = str_replace($patterns, $replacements, $v);
		$update[] = "$v=VALUES($v)";
	}
	
	$inserts = array();
	foreach ($columns as $v) {
		$v         = str_replace($patterns, $replacements2, $v);
		$inserts[] = $v;
	}

	$pmo->pmo->beginTransaction();


	$insert = $pmo->pmo->prepare("INSERT INTO $table (website_id, " . str_replace($patterns, $replacements, implode(", ", $columns)) . ") 
								  VALUES (" . $website_id . ", " . implode(',', $inserts) . ") ON DUPLICATE KEY UPDATE " . implode(',', $update));
	
	
	$res = $rs->execute();
	
	
	
	
	while ($row = $rs->fetchObject()) {		
		foreach ($row as $k => $v) {
			
			if ($debug)
				echo $k . ":" . $v . "\n";
			
			$insert->bindValue(':' . $k, $v);	
		}	
		
		
		
		if ($debug)
			print_r($insert);
		
		try {
			$insert->execute();
		}
		catch (Exception $e) {
			die(print_r($e->getMessage()));
		}
		
		$timestamp = $row->{$timestamp_column};
		
		
		echo 'i';
	}
	
	$dbErr = $insert->errorInfo();
    if ( $dbErr[0] != '00000' &&  $debug ==true) {
         print_r($insert->errorInfo());     
    }
		
	if($pmo->pmo->commit()){
		//UPDATE TIMESTAMP
		if(hexdec($timestamp)> 0){
			$Mag->Mag_TimestampUpdate(hexdec($timestamp));	
		}	
	}
		
	//CHECK IF EVERYTHING IS EXPORTED	
	$check1 = $pmo->db[0]->prepare($check_ins_sql1);	
	$check2 = $pmo->pmo->prepare($check_ins_sql2);
		
	$check1->execute();
	
	$c=array();
	$i=1;
		
	while ($column = $check1->fetch(PDO::FETCH_ASSOC)) {	
		
		if ($debug)
			echo print_r($column) . "\n";
				
		
		
		$c[] = $column[':ID'];
		
		
		if($i==$bc){
			echo 'e';	
			$check2->execute($c);	
		
			$count = $check2->fetchColumn();
		
			if($count!=$bc){				
				
				echo implode(',',$c);			
				echo "\n".$count."/$bc"."\n";
				//exit;
			}
			
			$i=0;
			$c=array();		
		}
		
		$i++;
		
		echo 'c';		
	}
	
	echo "\n".'c = ok';


}

eventlog('magentodb_cp_orkrg', 'job succeded :-)');








?>