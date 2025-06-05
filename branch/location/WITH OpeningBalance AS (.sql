WITH OpeningBalance AS (
                SELECT  
                    gl, 
                    MIN(date) AS first_date, 
                    SUM(opening_val) AS total_opening_value
                FROM 
                    erp_opening_closing_balance AS eocb
                WHERE 
                    company_id = 1 
                    AND branch_id = 1 
                    AND location_id = 7 
                    AND date = (
                        SELECT MIN(date) 
                        FROM erp_opening_closing_balance 
                        WHERE gl = eocb.gl 
                          AND company_id = 1 
                          AND branch_id = 1 
                          AND location_id = 7
                    )
                GROUP BY 
                    gl
            ),
            Debits AS (
                SELECT 
                    ed.glId AS gl, 
                    SUM(ed.debit_amount) AS total_debit_value
                FROM 
                    erp_acc_debit AS ed
                JOIN 
                    erp_acc_journal AS ej ON ed.journal_id = ej.id
                WHERE 
                    ej.postingDate >= (SELECT first_date FROM OpeningBalance WHERE gl = ed.glId) 
                    AND ej.postingDate < '2024-04-01' 
                    AND ej.company_id = 1 AND ej.branch_id = 1 AND ej.location_id = 7
                GROUP BY 
                    ed.glId
            ),
            Credits AS (
                SELECT 
                    ec.glId AS gl, 
                    SUM(ec.credit_amount) AS total_credit_value
                FROM 
                    erp_acc_credit AS ec
                JOIN 
                    erp_acc_journal AS ej ON ec.journal_id = ej.id
                WHERE 
                    ej.postingDate >= (SELECT first_date FROM OpeningBalance WHERE gl = ec.glId) 
                    AND ej.postingDate < '2024-04-01' 
                    AND ej.company_id = 1 AND ej.branch_id = 1 AND ej.location_id = 7
                GROUP BY 
                    ec.glId
            ),
            RangeDebits AS (
                SELECT 
                    ed.glId AS gl, 
                    SUM(ed.debit_amount) AS final_debit_value
                FROM 
                    erp_acc_debit AS ed
                JOIN 
                    erp_acc_journal AS ej ON ed.journal_id = ej.id
                WHERE 
                    ej.postingDate >= '2024-04-01' 
                    AND ej.postingDate <= '2024-02-29'
                    AND ej.company_id = 1 AND ej.branch_id = 1 AND ej.location_id = 7
                GROUP BY 
                    ed.glId
            ),
            RangeCredits AS (
                SELECT 
                    ec.glId AS gl, 
                    SUM(ec.credit_amount) AS final_credit_value
                FROM 
                    erp_acc_credit AS ec
                JOIN 
                    erp_acc_journal AS ej ON ec.journal_id = ej.id
                WHERE 
                    ej.postingDate >= '2024-04-01' 
                    AND ej.postingDate <= '2024-02-29'
                 AND ej.company_id = 1 AND ej.branch_id = 1 AND ej.location_id = 7
                GROUP BY 
                    ec.glId
            )
            SELECT 
                ob.gl, 
                coa.gl_code,
                coa.gl_label,
                (ob.total_opening_value + COALESCE(d.total_debit_value, 0) - COALESCE(c.total_credit_value, 0)) AS from_opening_value,
                COALESCE(rd.final_debit_value, 0) AS final_debit,
                COALESCE(rc.final_credit_value, 0) AS final_credit,
                ((ob.total_opening_value + COALESCE(d.total_debit_value, 0) - COALESCE(c.total_credit_value, 0)) + COALESCE(rd.final_debit_value, 0) - COALESCE(rc.final_credit_value, 0)) AS to_closing_value
            FROM 
                OpeningBalance AS ob
            LEFT JOIN 
                Debits AS d ON ob.gl = d.gl
            LEFT JOIN 
                Credits AS c ON ob.gl = c.gl
            LEFT JOIN 
                RangeDebits AS rd ON ob.gl = rd.gl
            LEFT JOIN 
                RangeCredits AS rc ON ob.gl = rc.gl
            LEFT JOIN 
                erp_acc_coa_1_table AS coa ON ob.gl = coa.id
            WHERE ob.gl = $gl_id
            ORDER BY 
                ob.gl