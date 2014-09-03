<?php

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();


//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);

$rs = $Mag_Mssql->Mssql->Execute("
SELECT InvoiceNumber AS [invoice_id],
       Debtornumber AS [customer_id],
       InvoiceDate AS [date],
       DueDate AS [purge_date],
       BankTransactions.Description AS [name],      
        (InvoiceAmount + ISNULL(DiscSurc, 0))  AS [total],
		
		ISNULL( InvoiceAmount,0 ) AS InvoiceAmount ,
        ISNULL( DiscSurc,0 ) AS DiscSurc ,
        ISNULL( Other,0 ) AS Other ,
        ISNULL( ReceiptPaid,0 ) AS ReceiptPaid ,
        ISNULL( (( ISNULL(InvoiceAmount, 0) + ISNULL(Other, 0) + ISNULL(DiscSurc, 0) )
          - ISNULL(ReceiptPaid, 0)),0 ) AS Saldo ,
		
		     
       (
           CASE 
                WHEN ISNULL(ReceiptPaid, 0) <> 0
           AND (
                   CASE 
                        WHEN (ROUND(ISNULL(OtherInvoice, 0), 2) = 0.0) THEN 
                             ReceiptPaid * -1
                        ELSE (ISNULL(OtherInvoice, 0)) -(
                                 CASE 
                                      WHEN (
                                               ROUND(ISNULL(ABS(ReceiptPaid), 0), 2) 
                                               > (ROUND(ISNULL(OtherInvoice, 0), 2))
                                               AND ROUND(ReceiptPaid, 2) <> 0.00
                                           ) THEN (ISNULL(OtherInvoice, 0))
                                      ELSE ISNULL(ABS(Receiptpaid), 0)
                                 END
                             )
                   END
               ) = 0 
               THEN 1 ELSE 0 END
       ) AS [state],
	   
	   
	   
      BacoDiscussions.Document AS [invoice_data]
FROM   (
           (
               -- Query 1 form clause start 
               (
                   -- Query 2 sum and max from the 2 subqueries, start 
                   SELECT MIN(InvoiceDate) AS InvoiceDate,
                          MAX(ActiveDate) AS ActiveDate,
                          InvoiceNumber,
                          MAX(SupplierInvoiceNumber) AS SupplierInvoiceNumber,
                          MAX(a.Description) AS DESCRIPTION,
                          MAX(DueDate) AS DueDate,
                          SUM(InvoiceAmount) AS InvoiceAmount,
                          SUM(ReceiptPaid) AS ReceiptPaid,
                          SUM(Other) AS Other,
                          SUM(DiscSurc) AS DiscSurc,
                          SUM(
                              ISNULL(InvoiceAmount, 0) + ISNULL(Other, 0) + 
                              ISNULL(DiscSurc, 0)
                          ) AS OtherInvoice,
                          MAX(PaymentType) AS PaymentType,
                          MIN(TransactionType) AS TransactionType,
                          MAX(Creditline) AS Creditline,
                          DebtorNumber,
                          CreditorNumber,
                          OffsetName,
                          CicmpyCode,
                          SUM(AmountTC) AS AmountTC,
                          TCCode,
                          MAX(ExchangeRate) AS ExchangeRate
                   FROM   (
                              (
                                  (
                                      -- Query 3 start, look for all imbalance S term 
                                      SELECT T.ValueDate AS InvoiceDate,
                                             NULL AS ActiveDate,
                                             ISNULL(T.InvoiceNumber, T.InvoiceNumber) AS 
                                             InvoiceNumber,
                                             '' AS SupplierInvoiceNumber,
                                             (CONVERT(VARCHAR(25), T.Description)) AS 
                                             DESCRIPTION,
                                             T.DueDate AS DueDate,
                                             NULL AS InvoiceAmount,
                                             T.AmountDC AS ReceiptPaid,
                                             NULL AS Other,
                                             NULL AS DiscSurc,
                                             T.PaymentType AS PaymentType,
                                             T.transactiontype AS 
                                             Transactiontype,
                                             T.OffsetLedgerAccountNumber,
                                             T.EntryNumber,
                                             T.OffsetReference,
                                             T.Ordernumber,
                                             T.CreditorNumber,
                                             T.DebtorNumber,
                                             ci.cmp_name AS OffsetName,
                                             T.AmountTC,
                                             T.TCCode,
                                             ci.debcode AS CicmpyCode,
                                             creditline,
                                             NULL AS ExchangeRate
                                      FROM   BankTransactions T
                                             LEFT OUTER JOIN cicmpy ci
                                                  ON  DebtorNumber = ci.debnr
                                                  AND DebtorNumber IS NOT NULL
                                                  AND ci.debnr IS NOT NULL
                                             LEFT JOIN (
                                                      SELECT btx.MatchID,
                                                             ROUND(SUM(ROUND(btx.AmountDC, 2)), 2) AS 
                                                             AmountDC
                                                      FROM   BankTransactions 
                                                             btx
                                                      WHERE  btx.Type = 'W'
                                                             AND btx.Status IN ('C', 'A', 'P', 'J')
                                                             AND (NOT ISNULL(btx.EntryNumber, '') = '')
                                                      GROUP BY
                                                             btx.MatchID
                                                      HAVING btx.MatchID IS NOT 
                                                             NULL
                                                  ) AS bts
                                                  ON  bts.MatchID = T.ID
                                      WHERE  T.Type = 'S'
                                             AND T.Status <> 'V'
                                             AND ABS(ROUND(ISNULL(T.AmountDC, 0), 2)) 
                                                 <> ABS(ROUND(ISNULL(bts.AmountDC, 0), 2))
                                             AND T.OffsetLedgerAccountNumber IN (SELECT 
                                                                                        reknr
                                                                                 FROM   
                                                                                        grtbk
                                                                                 WHERE  
                                                                                        omzrek IN ('D', 'C'))
                                             AND ISNULL(ci.debcode, '') <> ''
                                             AND ci.cmp_type = 'C'
                                             AND (
                                                     T.CreditorNumber IS NULL
                                                     OR T.CreditorNumber NOT IN ('421212')
                                                 )
                                             AND (
                                                     ci.debcode >= 
                                                     '              100000'
                                                     AND ci.debcode <= 
                                                         '              200000'
                                                 )
                                                 -- Query 3 end.
                                  ) 
                                  UNION ALL 
                                  (
                                      -- Query 4 start, find all W term that having gbkmut, entrynumber is not null. 
                                      SELECT InvoiceDate,
                                             ISNULL(
                                                 (
                                                     SELECT TOP 1 ValueDate
                                                     FROM   BankTransactions c
                                                     WHERE  c.ID = t.MatchID
                                                 ),
                                                 {d '2012-11-02'}
                                             ) AS ActiveDate,
                                             InvoiceNumber AS InvoiceNumber,
                                             SupplierInvoiceNumber AS 
                                             SupplierInvoiceNumber,
                                             (CONVERT(VARCHAR(25), T.Description)) AS 
                                             DESCRIPTION,
                                             T.DueDate AS DueDate,
                                             (
                                                 CASE 
                                                      WHEN (
                                                               T.Transactiontype IN ('C', 'K', 'T', 'Q', 'W')
                                                               AND ISNULL(T.StatementType, '') 
                                                                   <> 'F'
                                                           )
                                                 OR (
                                                        T.TransactionType IN ('K', 'T', 'D', 'C', 'Q')
                                                        AND ISNULL(T.StatementType, '') 
                                                            = 'F'
                                                    ) THEN T.AmountDC ELSE NULL 
                                                    END
                                             ) AS InvoiceAmount,
                                             (
                                                 CASE 
                                                      WHEN (
                                                               T.MatchID IS NOT 
                                                               NULL
                                                               AND T.TransactionType 
                                                                   NOT IN ('Y', 'Z')
                                                           ) THEN T.AmountDC
                                                      ELSE CASE 
                                                                WHEN (
                                                                         T.TransactionType IN ('C', 'Q')
                                                                         AND 
                                                                             ISNULL(StatementType, '') 
                                                                             = 
                                                                             'F'
                                                                         AND T.MatchID 
                                                                             IS 
                                                                             NULL
                                                                     ) THEN T.AmountDC 
                                                                     * -1
                                                                ELSE NULL
                                                           END
                                                 END
                                             ) AS ReceiptPaid,
                                             (
                                                 CASE 
                                                      WHEN (
                                                               T.Transactiontype 
                                                               NOT IN ('C', 'K', 'T', 'Q', 'W', 'Y', 'Z', 'F', 'U')
                                                               AND ISNULL(T.StatementType, '') 
                                                                   <> 'F'
                                                           )
                                                 OR (T.TransactionType IN ('Y', 'Z') AND T.MatchID IS NULL)
                                                 OR (
                                                        T.TransactionType IN ('N')
                                                        OR (
                                                               T.TransactionType IN ('F', 'U')
                                                               AND ISNULL(T.StatementType, '') 
                                                                   = 'F'
                                                           )
                                                    ) THEN T.AmountDC ELSE NULL 
                                                    END
                                             ) AS Other,
                                             (
                                                 CASE 
                                                      WHEN (
                                                               T.TransactionType IN ('F', 'U')
                                                               AND ISNULL(T.StatementType, '') 
                                                                   <> 'F'
                                                           ) THEN T.AmountDC
                                                      ELSE NULL
                                                 END
                                             ) AS DiscSurc,
                                             T.PaymentType AS PaymentType,
                                             (
                                                 CASE 
                                                      WHEN (
                                                               T.TransactionType IN ('C', 'K', 'T', 'Q', 'W')
                                                               OR (
                                                                      T.TransactionType IN ('F', 'U')
                                                                      AND ISNULL(T.StatementType, '') 
                                                                          = 'F'
                                                                  )
                                                           ) THEN T.TransactionType
                                                      ELSE NULL
                                                 END
                                             ) AS TransactionType,
                                             T.OffsetLedgerAccountNumber,
                                             T.EntryNumber,
                                             T.OffsetReference,
                                             T.Ordernumber,
                                             T.CreditorNumber,
                                             T.DebtorNumber,
                                             ci.cmp_name AS OffsetName,
                                             T.AmountTC,
                                             T.TCCode,
                                             ci.debcode AS CicmpyCode,
                                             creditline,
                                             (
                                                 CASE 
                                                      WHEN T.ExchangeRate = 0 THEN 
                                                           T.ExchangeRate
                                                      ELSE (1 / ExchangeRate)
                                                 END
                                             ) AS ExchangeRate
                                      FROM   BankTransactions T
                                             LEFT OUTER JOIN cicmpy ci
                                                  ON  DebtorNumber = ci.debnr
                                                  AND DebtorNumber IS NOT NULL
                                                  AND ci.debnr IS NOT NULL
                                      WHERE  T.Type = 'W'
                                             AND T.Status IN ('C', 'A', 'P', 'J')
                                             AND (NOT ISNULL(T.EntryNumber, '') = '')
                                             AND NOT (T.TransactionType IN ('Y', 'Z') AND T.MatchID IS NOT NULL)
                                             AND ISNULL(ci.debcode, '') <> ''
                                             AND ci.cmp_type = 'C'
                                             AND (
                                                     T.CreditorNumber IS NULL
                                                     OR T.CreditorNumber NOT IN ('421212')
                                                 )
                                             AND (
                                                     ci.debcode >= 
                                                     '              100000'
                                                     AND ci.debcode <= 
                                                         '              200000'
                                                 )
                                                 -- Query 4 end
                                  )
                              )
                          ) a
                   GROUP BY
                          InvoiceNumber,
                          EntryNumber,
                          DebtorNumber,
                          CreditorNumber,
                          OffsetName,
                          CicmpyCode,
                          TCCode 
                          -- Query 2 sum and max from the 2 subqueries, end
               )
               UNION ALL 
               (
                   -- Query 5 start, find imbalance S term  
                   SELECT MIN(T.ValueDate) AS InvoiceDate,
                          MAX(T.ValueDate) AS ActiveDate,
                          T.InvoiceNumber AS InvoiceNumber,
                          '' AS SupplierInvoiceNumber,
                          MAX((CONVERT(VARCHAR(25), T.Description))) AS 
                          DESCRIPTION,
                          MAX(T.DueDate) AS DueDate,
                          NULL AS InvoiceAmount,
                          SUM(T.AmountDC - bts.AmountDC) AS ReceiptPaid,
                          NULL AS Other,
                          NULL AS DiscSurc,
                          NULL AS OtherInvoice,
                          MAX(T.PaymentType) AS PaymentType,
                          MAX(T.TransactionType) AS TransactionType,
                          MAX(T.CreditorNumber) AS CreditorNumber,
                          MAX(T.DebtorNumber) AS DebtorNumber,
                          MAX(ci.cmp_name) AS OffsetName,
                          MAX(ci.debcode) AS CicmpyCode,
                          MAX(creditline),
                          SUM(AmountTC) AS AmountTC,
                          TCCode,
                          NULL AS ExchangeRate
                   FROM   BankTransactions T
                          LEFT OUTER JOIN cicmpy ci
                               ON  DebtorNumber = ci.debnr
                               AND DebtorNumber IS NOT NULL
                               AND ci.debnr IS NOT NULL
                          INNER JOIN (
                                   SELECT btx.MatchID,
                                          ROUND(SUM(ROUND(btx.AmountDC, 3)), 3) AS 
                                          AmountDC
                                   FROM   BankTransactions btx
                                   WHERE  btx.Type = 'W'
                                          AND btx.Status IN ('C', 'A', 'P', 'J')
                                          AND (NOT ISNULL(btx.EntryNumber, '') = '')
                                   GROUP BY
                                          btx.MatchID
                                   HAVING btx.MatchID IS NOT NULL
                               ) AS bts
                               ON  bts.MatchID = T.ID
                   WHERE  T.Type = 'S'
                          AND T.Status <> 'V'
                          AND (NOT ISNULL(T.EntryNumber, '') = '')
                          AND ISNULL(ci.debcode, '') <> ''
                          AND ci.cmp_type = 'C'
                          AND (
                                  T.CreditorNumber IS NULL
                                  OR T.CreditorNumber NOT IN ('421212')
                              )
                          AND (
                                  ci.debcode >= '              100000'
                                  AND ci.debcode <= '              200000'
                              )
                   GROUP BY
                          T.ID,
                          T.InvoiceNumber,
                          T.EntryNumber,
                          TCCode
                   HAVING (
                              ROUND(SUM(ISNULL(T.AmountDC, 0) - ISNULL(bts.AmountDC, 0)), 2) 
                              <> 0
                          )
                          -- Query 5 End
               ) 
               -- Query 1 from clause end
           )
       ) banktransactions
 LEFT JOIN BacoDiscussions (NOLOCK) ON  BacoDiscussions.OurRef = InvoiceNumber AND RIGHT(BacoDiscussions.[Filename],3)='pdf'
WHERE ABS(InvoiceAmount + ISNULL(DiscSurc, 0))  > 0  ORDER BY
       Invoicenumber DESC");
				
	
	
	
	
if($rs && $rs->_numOfRows > 0){			
	while (!$rs->EOF){
					
		$eav_attribute_option = $DBTransip->Execute("INSERT INTO `wc_invoices` (`invoice_id`,`customer_id`,`date`,`purge_date`,`name`,`total`,`amount_invoice`,`amount_discsurc`,`amount_other`, `amount_paid` , `amount_due`,`state`,`invoice_data`) 
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `invoice_data`=VALUES(`invoice_data`), 
`total`= VALUES(`total`),
`amount_invoice`= VALUES(`amount_invoice`),
`amount_discsurc`= VALUES(`amount_discsurc`),
`amount_other`= VALUES(`amount_other`),
`amount_paid`= VALUES(`amount_paid`),
`amount_due`= VALUES(`amount_due`),
`state`= VALUES(`state`),
`name`= VALUES(`name`) ,
`purge_date`= VALUES(`purge_date`),
`date`= VALUES(`date`)

",$rs->fields);
		
		//print_r($rs->fields);
		
		
		
		echo $DBTransip->ErrorMsg();
		echo '.';
				
		$rs->MoveNext();
	}	
	
	
	eventlog('magentodb_cp_invoices', 'job succeded :-)');	
	
	
}
else{
	$result .= "Nothing to download";
}




	

?>