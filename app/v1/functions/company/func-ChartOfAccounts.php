<?php
//*************************************/INSERT/******************************************//
// function importDefaltChartOfAccounts()
// {
//   global $dbCon;
//   global $company_id;
//   global $created_by;
//   global $updated_by;
//   $returnData = [];

//   $instcosdata = "INSERT INTO `" . ERP_ACC_CHART_OF_ACCOUNTS . "` (`id`, `ordering`, `lock_status`, `txn_status`, `company_id`, `glSt`, `glStType`, `typeAcc`, `p_id`, `sp_id`, `lvl`, `gl_code`, `gl_label`, `remark`, `created_by`, `updated_by`, `status`) VALUES
//   (5, 0, 0, 0, " . $company_id . ", '', 'group', '1', 1, 1, 1, '', 'Non-current Asset', 'Non-current Asset', '" . $created_by . "', '" . $updated_by . "','active'),
//   (6, 0, 0, 0, " . $company_id . ", '', 'group', '1', 5, 5, 2, '', 'Property, Plant and Equipment [and Intangible assets]', 'Property, Plant and Equipment and Intangible assets', '" . $created_by . "', '" . $updated_by . "','active'),
//   (7, 0, 0, 0, " . $company_id . ", '', 'group', '1', 6, 6, 3, '', 'Property, Plant & Equipment', 'Property, Plant & Equipment', '" . $created_by . "', '" . $updated_by . "','active'),
//   (8, 0, 0, 1, " . $company_id . ", '', 'group', '1', 7, 7, 4, '', 'Land & Building', 'Land & Building', '" . $created_by . "', '" . $updated_by . "','active'),
//   (9, 0, 0, 1, " . $company_id . ", '', 'account', '1', 8, 8, 5, '10001', 'Land', 'Land', '" . $created_by . "', '" . $updated_by . "','active'),
//   (10, 0, 0, 0, " . $company_id . ", '', 'account', '1', 8, 8, 5, '10002', 'Building', 'Building', '" . $created_by . "', '" . $updated_by . "','active'),
//   (11, 0, 0, 0, " . $company_id . ", '', 'group', '1', 7, 7, 4, '', 'Plant & Equipments', 'Plant & Equipments', '" . $created_by . "', '" . $updated_by . "','active'),
//   (12, 0, 0, 0, " . $company_id . ", '', 'account', '1', 11, 11, 5, '10003', 'Plant', 'Plant available in Factories setup', '" . $created_by . "', '" . $updated_by . "','active'),
//   (13, 0, 0, 0, " . $company_id . ", '', 'account', '1', 11, 11, 5, '10004', 'Equipements', 'Equipements used in Plant', '" . $created_by . "', '" . $updated_by . "','active'),
//   (15, 0, 0, 1, " . $company_id . ", '', 'group', '1', 7, 7, 4, '', 'Furnitures & Fixtures', 'Furnitures & Fixtures', '" . $created_by . "', '" . $updated_by . "','active'),
//   (16, 0, 0, 0, " . $company_id . ", '', 'account', '1', 15, 15, 5, '10005', 'Furnitures', 'Furnitures', '" . $created_by . "', '" . $updated_by . "','active'),
//   (17, 0, 0, 0, " . $company_id . ", '', 'account', '1', 15, 15, 5, '10006', 'Fixtures', 'Fixtures', '" . $created_by . "', '" . $updated_by . "','active'),
//   (21, 0, 0, 0, " . $company_id . ", '', 'group', '1', 7, 7, 4, '', 'Vehicles', 'Vehicles', '" . $created_by . "', '" . $updated_by . "','active'),
//   (22, 0, 0, 0, " . $company_id . ", '', 'account', '1', 21, 21, 5, '10007', 'Automobiles', 'Related to Automobiles', '" . $created_by . "', '" . $updated_by . "','active'),
//   (23, 0, 0, 0, " . $company_id . ", '', 'account', '1', 21, 21, 5, '10008', 'Others', 'Other than Automobiles', '" . $created_by . "', '" . $updated_by . "','active'),
//   (26, 0, 0, 0, " . $company_id . ", '', 'group', '1', 7, 7, 4, '', 'Office Equipments', 'Office Equipments', '" . $created_by . "', '" . $updated_by . "','active'),
//   (27, 0, 0, 1, " . $company_id . ", '', 'account', '1', 26, 26, 5, '10009', 'Office Equipements', 'Office Equipements', '" . $created_by . "', '" . $updated_by . "','active'),
//   (32, 0, 0, 0, " . $company_id . ", '', 'group', '1', 6, 6, 3, '', 'Intangible assets', 'Intangible assets', '" . $created_by . "', '" . $updated_by . "','active'),
//   (33, 0, 0, 0, " . $company_id . ", '', 'account', '1', 32, 32, 4, '10010', 'Goodwill', 'Goodwill', '" . $created_by . "', '" . $updated_by . "','active'),
//   (34, 0, 0, 1, " . $company_id . ", '', 'account', '1', 32, 32, 4, '10011', 'IPR', 'Intellectual Property Rights ', '" . $created_by . "', '" . $updated_by . "','active'),
//   (35, 0, 0, 1, " . $company_id . ", '', 'account', '1', 32, 32, 4, '10012', 'Computer Software', 'Computer Software', '" . $created_by . "', '" . $updated_by . "','active'),
//   (42, 0, 0, 0, " . $company_id . ", '', 'group', '1', 6, 6, 3, '', 'Capital work-in-progress', 'Capital work-in-progress', '" . $created_by . "', '" . $updated_by . "','active'),
//   (43, 0, 0, 1, " . $company_id . ", '', 'account', '1', 42, 42, 4, '10013', 'Tangible Assets Under Development', 'Tangible Assets Under Development', '" . $created_by . "', '" . $updated_by . "','active'),
//   (45, 0, 0, 0, " . $company_id . ", '', 'account', '1', 42, 42, 4, '10014', 'Intangible assets under development', 'Intangible assets under development', '" . $created_by . "', '" . $updated_by . "','active'),
//   (46, 0, 0, 0, " . $company_id . ", '', 'group', '1', 5, 5, 2, '', 'Non-current investments', 'Non-current investments', '" . $created_by . "', '" . $updated_by . "','active'),
//   (47, 0, 0, 0, " . $company_id . ", '', 'account', '1', 46, 46, 3, '10015', 'Investment in property', 'Investment in property', '" . $created_by . "', '" . $updated_by . "','active'),
//   (48, 0, 0, 0, " . $company_id . ", '', 'account', '1', 46, 46, 3, '10016', 'Investments in Shares and Securities', 'Investments in Equity Instruments', '" . $created_by . "', '" . $updated_by . "','active'),
//   (50, 0, 0, 0, " . $company_id . ", '', 'account', '1', 46, 46, 3, '10017', 'Investments in Government and Gilt securities', 'Investments in Government securities', '" . $created_by . "', '" . $updated_by . "','active'),
//   (52, 0, 0, 0, " . $company_id . ", '', 'account', '1', 46, 46, 3, '10018', 'Investments in Debentures and Bonds', 'Investments in debentures', '" . $created_by . "', '" . $updated_by . "','active'),
//   (55, 0, 0, 0, " . $company_id . ", '', 'account', '1', 46, 46, 3, '10019', 'Investments in Body corporates & Others', 'Investments in partnership firms', '" . $created_by . "', '" . $updated_by . "','active'),
//   (57, 0, 0, 0, " . $company_id . ", '', 'account', '1', 46, 46, 3, '10020', 'Other Investments', 'Other Investments', '" . $created_by . "', '" . $updated_by . "','active'),
//   (58, 0, 0, 0, " . $company_id . ", '', 'account', '1', 5, 5, 2, '10021', 'Deferred tax assets', 'Deferred tax assets', '" . $created_by . "', '" . $updated_by . "','active'),
//   (59, 0, 0, 0, " . $company_id . ", '', 'account', '1', 60, 60, 3, '10022', 'Capital Advances', 'Capital Advances', '" . $created_by . "', '" . $updated_by . "','active'),
//   (60, 0, 0, 0, " . $company_id . ", '', 'group', '1', 5, 5, 2, '', 'Long-term loans and advances', 'Long-term loans and advances', '" . $created_by . "', '" . $updated_by . "','active'),
//   (61, 0, 0, 0, " . $company_id . ", '', 'account', '1', 60, 60, 3, '10023', 'Loans & Advances to related parties', 'Loans & Advances to related parties', '" . $created_by . "', '" . $updated_by . "','active'),
//   (62, 0, 0, 1, " . $company_id . ", '', 'account', '1', 60, 60, 3, '10024', 'Security Deposit', 'Security Deposit', '" . $created_by . "', '" . $updated_by . "','active'),
//   (63, 0, 0, 0, " . $company_id . ", '', 'account', '1', 60, 60, 3, '10025', 'EMD', 'EMD', '" . $created_by . "', '" . $updated_by . "','active'),
//   (64, 0, 0, 0, " . $company_id . ", '', 'account', '1', 60, 60, 3, '10026', 'Other Long Term Loan & Advances', 'Other Long Term Loan & Advances', '" . $created_by . "', '" . $updated_by . "','active'),
//   (65, 0, 0, 0, " . $company_id . ", '', 'group', '1', 5, 5, 2, '', 'Other non-current assets', 'Other non-current assets', '" . $created_by . "', '" . $updated_by . "','active'),
//   (66, 0, 0, 0, " . $company_id . ", '', 'account', '1', 65, 65, 3, '10027', 'Long - Term Trade Receivables', 'Long - Term Trade Receivables', '" . $created_by . "', '" . $updated_by . "','active'),
//   (67, 0, 0, 0, " . $company_id . ", '', 'account', '1', 65, 65, 3, '10028', 'Other Non-Current Assets', 'Other Non-Current Assets', '" . $created_by . "', '" . $updated_by . "','active'),
//   (68, 0, 0, 0, " . $company_id . ", '', 'group', '1', 1, 1, 1, '', 'Current assets', 'Current assets', '" . $created_by . "', '" . $updated_by . "','active'),
//   (69, 0, 0, 0, " . $company_id . ", '', 'group', '1', 68, 68, 2, '', 'Current investments', 'Current investments', '" . $created_by . "', '" . $updated_by . "','active'),
//   (70, 0, 0, 0, " . $company_id . ", '', 'account', '1', 69, 69, 3, '10029', 'Investment in property', 'Investment in property', '" . $created_by . "', '" . $updated_by . "','active'),
//   (71, 0, 0, 0, " . $company_id . ", '', 'account', '1', 69, 69, 3, '10030', 'Investments in Shares and Securities', 'Investments in Shares and Securities', '" . $created_by . "', '" . $updated_by . "','active'),
//   (73, 0, 0, 0, " . $company_id . ", '', 'account', '1', 69, 69, 3, '10031', 'Investments in Government and Gilt securities', 'Investments in Government and Gilt securities', '" . $created_by . "', '" . $updated_by . "','active'),
//   (74, 0, 0, 0, " . $company_id . ", '', 'account', '1', 69, 69, 3, '10032', 'Investments in Debentures and Bonds', 'Investments in Debentures and Bonds', '" . $created_by . "', '" . $updated_by . "','active'),
//   (75, 0, 0, 0, " . $company_id . ", '', 'account', '1', 69, 69, 3, '10033', 'Investments in Body corporates & Others', 'Investments in Body corporates & Others', '" . $created_by . "', '" . $updated_by . "','active'),
//   (80, 0, 0, 0, " . $company_id . ", '', 'account', '1', 69, 69, 3, '10034', 'Other Investments', 'Other Investments', '" . $created_by . "', '" . $updated_by . "','active'),
//   (81, 0, 0, 0, " . $company_id . ", '', 'group', '1', 68, 68, 2, '', 'Inventories', 'Inventories', '" . $created_by . "', '" . $updated_by . "','active'),
//   (82, 0, 1, 0, " . $company_id . ", '', 'account', '1', 81, 81, 3, '10035', 'Raw Materials', 'Raw Materials', '" . $created_by . "', '" . $updated_by . "','active'),
//   (83, 0, 1, 0, " . $company_id . ", '', 'account', '1', 81, 81, 3, '10036', 'Semi Finished Goods (SFG)', 'Semi Finished Goods (SFG)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (84, 0, 1, 0, " . $company_id . ", '', 'account', '1', 81, 81, 3, '10037', 'Finished Goods (FG)', 'Finished Goods (FG)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (85, 0, 0, 0, " . $company_id . ", '', 'account', '1', 81, 81, 3, '10038', 'Stock-In-Trade (in respect of goods acquired for trading)', 'Stock-In-Trade (in respect of goods acquired for trading)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (86, 0, 0, 0, " . $company_id . ", '', 'account', '1', 81, 81, 3, '10039', 'Stores and Spares', 'Stores and Spares', '" . $created_by . "', '" . $updated_by . "','active'),
//   (87, 0, 0, 0, " . $company_id . ", '', 'group', '1', 68, 68, 2, '', 'Trade receivables', 'Trade receivables', '" . $created_by . "', '" . $updated_by . "','active'),
//   (88, 0, 1, 0, " . $company_id . ", '', 'account', '1', 87, 87, 3, '10040', 'Account Receivable', 'Account Receivable', '" . $created_by . "', '" . $updated_by . "','active'),
//   (89, 0, 0, 0, " . $company_id . ", '', 'group', '1', 68, 68, 2, '', 'Cash and cash equivalents', 'Cash and cash equivalents', '" . $created_by . "', '" . $updated_by . "','active'),
//   (90, 0, 1, 0, " . $company_id . ", '', 'account', '1', 89, 89, 3, '10041', 'Bank', 'Bank', '" . $created_by . "', '" . $updated_by . "','active'),
//   (91, 0, 1, 0, " . $company_id . ", '', 'account', '1', 89, 89, 3, '10042', 'Cash-in-Hand', 'Cash-in-Hand', '" . $created_by . "', '" . $updated_by . "','active'),
//   (92, 0, 0, 0, " . $company_id . ", '', 'account', '1', 89, 89, 3, '10043', 'Drafts on Hand', 'Drafts on Hand', '" . $created_by . "', '" . $updated_by . "','active'),
//   (93, 0, 0, 0, " . $company_id . ", '', 'account', '1', 89, 89, 3, '10044', 'Others cash and Cash Equivalents', 'Others cash and Cash Equivalents', '" . $created_by . "', '" . $updated_by . "','active'),
//   (94, 0, 0, 0, " . $company_id . ", '', 'group', '1', 68, 68, 2, '', 'Short-term loans and advances', 'Short-term loans and advances', '" . $created_by . "', '" . $updated_by . "','active'),
//   (95, 0, 0, 0, " . $company_id . ", '', 'account', '1', 94, 94, 3, '10045', 'Capital Advances', 'Capital Advances', '" . $created_by . "', '" . $updated_by . "','active'),
//   (96, 0, 0, 0, " . $company_id . ", '', 'account', '1', 94, 94, 3, '10046', 'Loans & Advances to related parties', 'Loans & Advances to related parties', '" . $created_by . "', '" . $updated_by . "','active'),
//   (97, 0, 0, 0, " . $company_id . ", '', 'account', '1', 94, 94, 3, '10047', 'Advance to Employees', 'Advance to Employees', '" . $created_by . "', '" . $updated_by . "','active'),
//   (98, 0, 0, 0, " . $company_id . ", '', 'account', '1', 94, 94, 3, '10048', 'Advance Salary', 'Advance Salary', '" . $created_by . "', '" . $updated_by . "','active'),
//   (99, 0, 0, 0, " . $company_id . ", '', 'account', '1', 94, 94, 3, '10049', 'Loan to Employees', 'Employees Loan', '" . $created_by . "', '" . $updated_by . "','active'),
//   (100, 0, 0, 0, " . $company_id . ", '', 'account', '1', 94, 94, 3, '10050', 'Other short Term Loan & Advances', 'Other short Term Loan & Advances', '" . $created_by . "', '" . $updated_by . "','active'),
//   (101, 0, 0, 0, " . $company_id . ", '', 'group', '1', 68, 68, 2, '', 'Other current assets', 'Other current assets', '" . $created_by . "', '" . $updated_by . "','active'),
//   (102, 0, 0, 0, " . $company_id . ", '', 'account', '1', 101, 101, 3, '10051', 'Prepaid Expenses', 'Prepaid Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
//   (103, 0, 0, 0, " . $company_id . ", '', 'account', '1', 101, 101, 3, '10052', 'Interest Receivables', 'Interest Receivables', '" . $created_by . "', '" . $updated_by . "','active'),
//   (104, 0, 0, 0, " . $company_id . ", '', 'account', '1', 101, 101, 3, '10053', 'Dividend Receivables', 'Dividend Receivables', '" . $created_by . "', '" . $updated_by . "','active'),
//   (105, 0, 0, 1, " . $company_id . ", '', 'account', '1', 101, 101, 3, '10054', 'Other Current Assets', 'Other Current Assets', '" . $created_by . "', '" . $updated_by . "','active'),
//   (107, 0, 0, 0, " . $company_id . ", '', 'group', '2', 2, 2, 2, '', 'Shareholder\'s funds', 'Shareholders funds', '" . $created_by . "', '" . $updated_by . "','active'),
//   (108, 0, 0, 0, " . $company_id . ", '', 'group', '2', 107, 107, 3, '', 'Share capital', 'Share capital', '" . $created_by . "', '" . $updated_by . "','active'),
//   (109, 0, 0, 0, " . $company_id . ", '', 'account', '2', 108, 108, 4, '20001', 'Equity Share Capital', 'Equity Share Capital', '" . $created_by . "', '" . $updated_by . "','active'),
//   (110, 0, 0, 0, " . $company_id . ", '', 'account', '2', 108, 108, 4, '20002', 'Preference Share Capital', 'Preference Share Capital', '" . $created_by . "', '" . $updated_by . "','active'),
//   (111, 0, 0, 0, " . $company_id . ", '', 'group', '2', 107, 107, 3, '', 'Money received against share warrants', 'Money received against share warrants', '" . $created_by . "', '" . $updated_by . "','active'),
//   (112, 0, 0, 0, " . $company_id . ", '', 'account', '2', 111, 111, 4, '20003', 'Money received against share warrants', 'Money received against share warrants', '" . $created_by . "', '" . $updated_by . "','active'),
//   (113, 0, 0, 0, " . $company_id . ", '', 'group', '2', 2, 2, 2, '', 'Share application money pending allotment', 'Share application money pending allotment', '" . $created_by . "', '" . $updated_by . "','active'),
//   (115, 0, 0, 0, " . $company_id . ", '', 'account', '2', 113, 113, 4, '20004', 'Share application money pending allotment', 'Share application money pending allotment', '" . $created_by . "', '" . $updated_by . "','active'),
//   (116, 0, 0, 0, " . $company_id . ", '', 'group', '2', 107, 107, 3, '', 'Reserves and surplus', 'Reserves and surplus', '" . $created_by . "', '" . $updated_by . "','active'),
//   (117, 0, 0, 0, " . $company_id . ", '', 'account', '2', 116, 116, 4, '20005', 'Capital Reserves', 'Capital Reserves', '" . $created_by . "', '" . $updated_by . "','active'),
//   (118, 0, 0, 0, " . $company_id . ", '', 'account', '2', 116, 116, 4, '20006', 'Capital Redemption Reserve', 'Capital Redemption Reserve', '" . $created_by . "', '" . $updated_by . "','active'),
//   (119, 0, 0, 0, " . $company_id . ", '', 'account', '2', 116, 116, 4, '20007', 'Securities Premium', 'Securities Premium', '" . $created_by . "', '" . $updated_by . "','active'),
//   (120, 0, 0, 0, " . $company_id . ", '', 'account', '2', 116, 116, 4, '20008', 'Debebtures Redemption Reserve', 'Debebtures Redemption Reserve', '" . $created_by . "', '" . $updated_by . "','active'),
//   (121, 0, 0, 0, " . $company_id . ", '', 'account', '2', 116, 116, 4, '20009', 'General Reserve', 'General Reserve', '" . $created_by . "', '" . $updated_by . "','active'),
//   (122, 0, 0, 0, " . $company_id . ", '', 'account', '2', 116, 116, 4, '20010', 'Revaluation Reserve', 'Revaluation Reserve', '" . $created_by . "', '" . $updated_by . "','active'),
//   (123, 0, 0, 0, " . $company_id . ", '', 'account', '2', 116, 116, 4, '20011', 'Shares Options O/s Account', 'Shares Options O/s Account', '" . $created_by . "', '" . $updated_by . "','active'),
//   (124, 0, 0, 0, " . $company_id . ", '', 'group', '2', 2, 2, 2, '', 'Non-current liabilities', 'Non-current liabilities', '" . $created_by . "', '" . $updated_by . "','active'),
//   (125, 0, 0, 0, " . $company_id . ", '', 'group', '2', 124, 124, 3, '', 'Long -Term Borrowings', 'Long -Term Borrowings', '" . $created_by . "', '" . $updated_by . "','active'),
//   (126, 0, 0, 0, " . $company_id . ", '', 'account', '2', 125, 125, 4, '20012', 'Bond', 'Bond', '" . $created_by . "', '" . $updated_by . "','active'),
//   (127, 0, 0, 0, " . $company_id . ", '', 'account', '2', 125, 125, 4, '20013', 'Debentures', 'Debentures', '" . $created_by . "', '" . $updated_by . "','active'),
//   (128, 0, 0, 0, " . $company_id . ", '', 'account', '2', 125, 125, 4, '20014', 'Term Loan from Bank', 'Term Loan from Bank', '" . $created_by . "', '" . $updated_by . "','active'),
//   (129, 0, 0, 0, " . $company_id . ", '', 'account', '2', 125, 125, 4, '20015', 'Term Loan from Others', 'Term Loan from Others', '" . $created_by . "', '" . $updated_by . "','active'),
//   (130, 0, 0, 0, " . $company_id . ", '', 'account', '2', 125, 125, 4, '20016', 'Deferred Payment Liabilities', 'Deferred Payment Liabilities', '" . $created_by . "', '" . $updated_by . "','active'),
//   (131, 0, 0, 0, " . $company_id . ", '', 'account', '2', 125, 125, 4, '20017', 'Deposits', 'Deposits', '" . $created_by . "', '" . $updated_by . "','active'),
//   (132, 0, 0, 0, " . $company_id . ", '', 'account', '2', 125, 125, 4, '20018', 'Loans & Advances from Related Parties', 'Loans & Advances from Related Parties', '" . $created_by . "', '" . $updated_by . "','active'),
//   (133, 0, 0, 0, " . $company_id . ", '', 'account', '2', 125, 125, 4, '20019', 'Long Term Maturities of Finance Lease Obligations', 'Long Term Maturities of Finance Lease Obligations', '" . $created_by . "', '" . $updated_by . "','active'),
//   (134, 0, 0, 0, " . $company_id . ", '', 'account', '2', 125, 125, 4, '20020', 'Other Loans & Advances', 'Other Loans & Advances', '" . $created_by . "', '" . $updated_by . "','active'),
//   (135, 0, 0, 0, " . $company_id . ", '', 'account', '2', 124, 124, 3, '20021', 'Deferred tax liabilities', 'Deferred tax liabilities', '" . $created_by . "', '" . $updated_by . "','active'),
//   (136, 0, 0, 0, " . $company_id . ", '', 'group', '2', 124, 124, 3, '', 'Other Long term liabilities', 'Other Long term liabilities', '" . $created_by . "', '" . $updated_by . "','active'),
//   (137, 0, 0, 0, " . $company_id . ", '', 'account', '2', 136, 136, 4, '20022', 'Financial Lease Obligation', 'Financial Lease Obligation', '" . $created_by . "', '" . $updated_by . "','active'),
//   (138, 0, 0, 0, " . $company_id . ", '', 'account', '2', 136, 136, 4, '20023', 'Others Long term liabilities', 'Others Long term liabilities', '" . $created_by . "', '" . $updated_by . "','active'),
//   (139, 0, 0, 0, " . $company_id . ", '', 'group', '2', 124, 124, 3, '', 'Long-term provisions', 'Long-term provisions', '" . $created_by . "', '" . $updated_by . "','active'),
//   (140, 0, 0, 0, " . $company_id . ", '', 'account', '2', 139, 139, 4, '20024', 'Provisions for Gratuity', 'Provisions for Gratuity', '" . $created_by . "', '" . $updated_by . "','active'),
//   (141, 0, 0, 0, " . $company_id . ", '', 'account', '2', 139, 139, 4, '20025', 'Other long term provision', 'Other long term provision', '" . $created_by . "', '" . $updated_by . "','active'),
//   (142, 0, 0, 0, " . $company_id . ", '', 'group', '2', 2, 2, 2, '', 'Current liabilities', 'Current liabilities', '" . $created_by . "', '" . $updated_by . "','active'),
//   (143, 0, 0, 0, " . $company_id . ", '', 'group', '2', 142, 142, 3, '', 'Short-term borrowings', 'Short-term borrowings', '" . $created_by . "', '" . $updated_by . "','active'),
//   (144, 0, 0, 0, " . $company_id . ", '', 'account', '2', 143, 143, 4, '20026', 'Loans from Banks', 'Loans from Banks', '" . $created_by . "', '" . $updated_by . "','active'),
//   (145, 0, 0, 0, " . $company_id . ", '', 'account', '2', 143, 143, 4, '20027', 'Loans & Advances from Related Parties', 'Loans & Advances from Related Parties', '" . $created_by . "', '" . $updated_by . "','active'),
//   (146, 0, 0, 0, " . $company_id . ", '', 'account', '2', 143, 143, 4, '20028', 'Loans from Others', 'Loans from Others', '" . $created_by . "', '" . $updated_by . "','active'),
//   (147, 0, 0, 0, " . $company_id . ", '', 'account', '2', 143, 143, 4, '20029', 'Deposits', 'Deposits', '" . $created_by . "', '" . $updated_by . "','active'),
//   (148, 0, 0, 0, " . $company_id . ", '', 'account', '2', 143, 143, 4, '20030', 'Other Loans & Advances', 'Other Loans & Advances', '" . $created_by . "', '" . $updated_by . "','active'),
//   (149, 0, 0, 0, " . $company_id . ", '', 'group', '2', 142, 142, 3, '', 'Trade payables', 'Trade payables', '" . $created_by . "', '" . $updated_by . "','active'),
//   (150, 0, 1, 0, " . $company_id . ", '', 'account', '2', 149, 149, 4, '20031', 'Trade payables', 'Trade payables', '" . $created_by . "', '" . $updated_by . "','active'),
//   (151, 0, 0, 0, " . $company_id . ", '', 'group', '2', 142, 142, 3, '', 'Other current liabilities', 'Other current liabilities', '" . $created_by . "', '" . $updated_by . "','active'),
//   (152, 0, 0, 0, " . $company_id . ", '', 'account', '2', 151, 151, 4, '20032', 'Finance Lease Obligation', 'Finance Lease Obligation', '" . $created_by . "', '" . $updated_by . "','active'),
//   (153, 0, 0, 0, " . $company_id . ", '', 'account', '2', 151, 151, 4, '20033', 'Interest accrued', 'Interest accrued', '" . $created_by . "', '" . $updated_by . "','active'),
//   (154, 0, 0, 0, " . $company_id . ", '', 'account', '2', 151, 151, 4, '20034', 'Director Remuneration Payable', 'Director Remuneration Payable', '" . $created_by . "', '" . $updated_by . "','active'),
//   (155, 0, 0, 0, " . $company_id . ", '', 'account', '2', 151, 151, 4, '20035', 'Advanced received from Customers', 'Advanced received from Customers', '" . $created_by . "', '" . $updated_by . "','active'),
//   (156, 0, 0, 0, " . $company_id . ", '', 'group', '2', 151, 151, 4, '', 'Duties & Taxes', 'Duties & Taxes', '" . $created_by . "', '" . $updated_by . "','active'),
//   (157, 0, 1, 0, " . $company_id . ", '', 'account', '2', 156, 156, 5, '20036', 'IGST', 'IGST', '" . $created_by . "', '" . $updated_by . "','active'),
//   (158, 0, 1, 0, " . $company_id . ", '', 'account', '2', 156, 156, 5, '20037', 'CGST', 'CGST', '" . $created_by . "', '" . $updated_by . "','active'),
//   (159, 0, 1, 0, " . $company_id . ", '', 'account', '2', 156, 156, 5, '20038', 'SGST', 'SGST', '" . $created_by . "', '" . $updated_by . "','active'),
//   (160, 0, 1, 0, " . $company_id . ", '', 'account', '2', 156, 156, 5, '20039', 'TDS Payable', 'TDS Payable', '" . $created_by . "', '" . $updated_by . "','active'),
//   (161, 0, 0, 0, " . $company_id . ", '', 'group', '2', 151, 151, 4, '', 'Statutory Payable', 'Statutory Payable', '" . $created_by . "', '" . $updated_by . "','active'),
//   (162, 0, 1, 0, " . $company_id . ", '', 'account', '2', 161, 161, 5, '20040', 'PF Payable', 'PF Payable', '" . $created_by . "', '" . $updated_by . "','active'),
//   (163, 0, 1, 0, " . $company_id . ", '', 'account', '2', 161, 161, 5, '20041', 'ESI Payable', 'ESI Payable', '" . $created_by . "', '" . $updated_by . "','active'),
//   (164, 0, 1, 0, " . $company_id . ", '', 'account', '2', 161, 161, 5, '20042', 'PT Payable', 'PT Payable', '" . $created_by . "', '" . $updated_by . "','active'),
//   (165, 0, 0, 0, " . $company_id . ", '', 'account', '2', 161, 161, 5, '20043', 'Other Statutory Payable', 'Other Statutory Payable', '" . $created_by . "', '" . $updated_by . "','active'),
//   (166, 0, 0, 0, " . $company_id . ", '', 'group', '2', 142, 142, 3, '', 'Short-term provisions', 'Short-term provisions', '" . $created_by . "', '" . $updated_by . "','active'),
//   (167, 0, 0, 0, " . $company_id . ", '', 'account', '2', 166, 166, 4, '20044', 'Provision for expenses', 'Provision for expenses', '" . $created_by . "', '" . $updated_by . "','active'),
//   (168, 0, 0, 0, " . $company_id . ", '', 'account', '2', 166, 166, 4, '20045', 'Others Provision', 'Others Provision', '" . $created_by . "', '" . $updated_by . "','active'),
//   (169, 0, 0, 0, " . $company_id . ", '', 'group', '3', 3, 3, 1, '', 'Revenue from Operation', 'Revenue from Operation', '" . $created_by . "', '" . $updated_by . "','active'),
//   (170, 0, 1, 0, " . $company_id . ", '', 'account', '3', 169, 169, 2, '30001', 'Sales Goods (Domestic)', 'Sales Goods (Domestic)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (171, 0, 1, 0, " . $company_id . ", '', 'account', '3', 169, 169, 2, '30002', 'Sales Goods (Export)', 'Sales Goods (Export)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (172, 0, 1, 0, " . $company_id . ", '', 'account', '3', 169, 169, 2, '30003', 'Sales Services (Domestic)', 'Sales Services (Domestic)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (173, 0, 1, 0, " . $company_id . ", '', 'account', '3', 169, 169, 2, '30004', 'Sales Services (Export)', 'Sales Services (Export)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (174, 0, 0, 0, " . $company_id . ", '', 'group', '3', 3, 3, 1, '', 'Non Operative Income', 'Non Operative Income', '" . $created_by . "', '" . $updated_by . "','active'),
//   (175, 0, 0, 0, " . $company_id . ", '', 'account', '3', 174, 174, 2, '30005', 'Interest Income', 'Interest Income', '" . $created_by . "', '" . $updated_by . "','active'),
//   (176, 0, 0, 0, " . $company_id . ", '', 'account', '3', 174, 174, 2, '30006', 'Dividend Income', 'Dividend Income', '" . $created_by . "', '" . $updated_by . "','active'),
//   (177, 0, 0, 0, " . $company_id . ", '', 'account', '3', 174, 174, 2, '30007', 'Gain/Loss on Sale of Mutual Fund', 'Gain/Loss on Sale of Mutual Fund', '" . $created_by . "', '" . $updated_by . "','active'),
//   (178, 0, 0, 0, " . $company_id . ", '', 'account', '3', 174, 174, 2, '30008', 'Gain/Loss on Sale of Fixed Assets', 'Gain/Loss on Sale of Fixed Assets', '" . $created_by . "', '" . $updated_by . "','active'),
//   (179, 0, 0, 0, " . $company_id . ", '', 'account', '3', 174, 174, 2, '30009', 'Miscellaneous Income', 'Miscellaneous Income', '" . $created_by . "', '" . $updated_by . "','active'),
//   (180, 0, 0, 0, " . $company_id . ", '', 'group', '4', 4, 4, 1, '', 'Direct Material Cost', 'Direct Material Cost', '" . $created_by . "', '" . $updated_by . "','active'),
//   (181, 0, 1, 0, " . $company_id . ", '', 'account', '4', 180, 180, 2, '40001', 'Cost of Goods Sold', 'Cost of Goods Sold', '" . $created_by . "', '" . $updated_by . "','active'),
//   (182, 0, 0, 0, " . $company_id . ", '', 'account', '4', 180, 180, 2, '40002', 'Cost of Goods Manufactured (Material)', 'Cost of Goods Manufactured (Material)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (183, 0, 0, 0, " . $company_id . ", '', 'account', '4', 180, 180, 2, '40003', 'Consumption of RM', 'Consumption of RM', '" . $created_by . "', '" . $updated_by . "','active'),
//   (184, 0, 1, 0, " . $company_id . ", '', 'account', '4', 180, 180, 2, '40004', 'Consumption of SFG', 'Consumption of SFG', '" . $created_by . "', '" . $updated_by . "','active'),
//   (185, 0, 0, 0, " . $company_id . ", '', 'group', '4', 4, 4, 1, '', 'Direct Expense', 'Direct Expense', '" . $created_by . "', '" . $updated_by . "','active'),
//   (186, 0, 0, 0, " . $company_id . ", '', 'account', '4', 185, 185, 2, '40005', 'Direct Labour', 'Direct Labour', '" . $created_by . "', '" . $updated_by . "','active'),
//   (187, 0, 0, 0, " . $company_id . ", '', 'group', '4', 4, 4, 1, '', 'Indirect Expense', 'Indirect Expense', '" . $created_by . "', '" . $updated_by . "','active'),
//   (188, 0, 0, 0, " . $company_id . ", '', 'group', '4', 187, 187, 2, '', 'Membership and Subscriptions', 'Membership and Subscriptions', '" . $created_by . "', '" . $updated_by . "','active'),
//   (189, 0, 0, 0, " . $company_id . ", '', 'account', '4', 188, 188, 3, '40006', 'Membership Fee', 'Membership Fee', '" . $created_by . "', '" . $updated_by . "','active'),
//   (190, 0, 0, 0, " . $company_id . ", '', 'group', '4', 187, 187, 2, '', 'Technical, Legal, Professional and Consultations', 'Technical, Legal, Professional and Consultations', '" . $created_by . "', '" . $updated_by . "','active'),
//   (191, 0, 0, 0, " . $company_id . ", '', 'account', '4', 190, 190, 3, '40007', 'Technical Consultancy', 'Technical Consultancy', '" . $created_by . "', '" . $updated_by . "','active'),
//   (192, 0, 0, 0, " . $company_id . ", '', 'account', '4', 190, 190, 3, '40008', 'Legal Consultancy', 'Legal Consultancy', '" . $created_by . "', '" . $updated_by . "','active'),
//   (193, 0, 0, 0, " . $company_id . ", '', 'account', '4', 190, 190, 3, '40009', 'Professional Advisory', 'Professional Advisory', '" . $created_by . "', '" . $updated_by . "','active'),
//   (194, 0, 0, 0, " . $company_id . ", '', 'account', '4', 190, 190, 3, '40010', 'Management Advisory', 'Management Advisory', '" . $created_by . "', '" . $updated_by . "','active'),
//   (195, 0, 0, 0, " . $company_id . ", '', 'group', '4', 187, 187, 2, '', 'Traveling and Conveyance', 'Traveling and Conveyance', '" . $created_by . "', '" . $updated_by . "','active'),
//   (196, 0, 0, 0, " . $company_id . ", '', 'account', '4', 195, 195, 3, '40011', 'Local Conveyance', 'Local Conveyance', '" . $created_by . "', '" . $updated_by . "','active'),
//   (197, 0, 0, 0, " . $company_id . ", '', 'account', '4', 195, 195, 3, '40012', 'Domestic Travel', 'Domestic Travel', '" . $created_by . "', '" . $updated_by . "','active'),
//   (198, 0, 0, 0, " . $company_id . ", '', 'account', '4', 195, 195, 3, '40013', 'International Travel', 'International Travel', '" . $created_by . "', '" . $updated_by . "','active'),
//   (199, 0, 0, 0, " . $company_id . ", '', 'group', '4', 187, 187, 2, '', 'Office and Establishment Expenses', 'Office and Establishment Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
//   (200, 0, 0, 0, " . $company_id . ", '', 'account', '4', 199, 199, 3, '40014', 'Office Rent', 'Office Rent', '" . $created_by . "', '" . $updated_by . "','active'),
//   (201, 0, 0, 0, " . $company_id . ", '', 'account', '4', 199, 199, 3, '40015', 'Office Maintenance Expenses', 'Office Maintenance Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
//   (202, 0, 0, 0, " . $company_id . ", '', 'account', '4', 199, 199, 3, '40016', 'Electricity Expenses', 'Electricity Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
//   (203, 0, 0, 0, " . $company_id . ", '', 'account', '4', 199, 199, 3, '40017', 'Internet Expenses', 'Internet Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
//   (204, 0, 0, 0, " . $company_id . ", '', 'account', '4', 199, 199, 3, '40018', 'Mobile and Telephone Expenses', 'Mobile and Telephone Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
//   (205, 0, 0, 0, " . $company_id . ", '', 'group', '4', 187, 187, 2, '', 'Repair & Maintenance', 'Repair & Maintenance', '" . $created_by . "', '" . $updated_by . "','active'),
//   (206, 0, 0, 0, " . $company_id . ", '', 'account', '4', 205, 205, 3, '40019', 'Repair & Maintenance', 'Repair & Maintenance', '" . $created_by . "', '" . $updated_by . "','active'),
//   (207, 0, 0, 0, " . $company_id . ", '', 'group', '4', 4, 4, 1, '', 'Indirect Material Cost', 'Indirect Material Cost', '" . $created_by . "', '" . $updated_by . "','active'),
//   (208, 0, 0, 0, " . $company_id . ", '', 'account', '4', 207, 207, 2, '40020', 'Consumption of Consumables', 'Consumption of Consumables', '" . $created_by . "', '" . $updated_by . "','active'),
//   (209, 0, 0, 0, " . $company_id . ", '', 'account', '4', 207, 207, 2, '40021', 'Consumption of Spares', 'Consumption of Spares', '" . $created_by . "', '" . $updated_by . "','active'),
//   (210, 0, 0, 0, " . $company_id . ", '', 'group', '4', 4, 4, 1, '', 'Employee Benefit Expenses', 'Employee Benefit Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
//   (211, 0, 0, 0, " . $company_id . ", '', 'group', '4', 210, 210, 2, '', 'Salary, Wages and Bonus', 'Salary, Wages and Bonus', '" . $created_by . "', '" . $updated_by . "','active'),
//   (212, 0, 0, 0, " . $company_id . ", '', 'account', '4', 211, 211, 3, '40022', 'Salary Account', 'Salary Account', '" . $created_by . "', '" . $updated_by . "','active'),
//   (213, 0, 0, 0, " . $company_id . ", '', 'account', '4', 211, 211, 3, '40023', 'Wages Account', 'Wages Account', '" . $created_by . "', '" . $updated_by . "','active'),
//   (214, 0, 0, 0, " . $company_id . ", '', 'account', '4', 211, 211, 3, '40024', 'Bonus Account', 'Bonus Account', '" . $created_by . "', '" . $updated_by . "','active'),
//   (215, 0, 0, 0, " . $company_id . ", '', 'account', '4', 211, 211, 3, '40025', 'Leave Encashment', 'Leave Encashment', '" . $created_by . "', '" . $updated_by . "','active'),
//   (216, 0, 0, 0, " . $company_id . ", '', 'account', '4', 211, 211, 3, '40026', 'Overtime Salary', 'Overtime Salary', '" . $created_by . "', '" . $updated_by . "','active'),
//   (217, 0, 0, 0, " . $company_id . ", '', 'group', '4', 210, 210, 2, '', 'Contribution to PF, ESI and Other Fund', 'Contribution to PF, ESI and Other Fund', '" . $created_by . "', '" . $updated_by . "','active'),
//   (218, 0, 0, 0, " . $company_id . ", '', 'account', '4', 217, 217, 3, '40027', 'Employer Contribution to PF', 'Employer Contribution to PF', '" . $created_by . "', '" . $updated_by . "','active'),
//   (219, 0, 0, 0, " . $company_id . ", '', 'account', '4', 217, 217, 3, '40028', 'Employer Contribution to ESI', 'Employer Contribution to ESI', '" . $created_by . "', '" . $updated_by . "','active'),
//   (220, 0, 0, 0, " . $company_id . ", '', 'account', '4', 217, 217, 3, '40029', 'Employer Contribution to Gratuity', 'Employer Contribution to Gratuity', '" . $created_by . "', '" . $updated_by . "','active'),
//   (221, 0, 0, 0, " . $company_id . ", '', 'group', '4', 210, 210, 2, '', 'Staff welfares', 'Staff welfares', '" . $created_by . "', '" . $updated_by . "','active'),
//   (222, 0, 0, 0, " . $company_id . ", '', 'account', '4', 221, 221, 3, '40030', 'Staff welfare Expenses', 'Staff welfare Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
//   (223, 0, 0, 0, " . $company_id . ", '', 'group', '4', 4, 4, 1, '', 'Depreciation and Amortisation', 'Depreciation and Amortisation', '" . $created_by . "', '" . $updated_by . "','active'),
//   (224, 0, 0, 0, " . $company_id . ", '', 'account', '4', 223, 223, 2, '40031', 'Depreciation Account', 'Depreciation Account', '" . $created_by . "', '" . $updated_by . "','active'),
//   (225, 0, 0, 0, " . $company_id . ", '', 'account', '4', 223, 223, 2, '40032', 'Amortisation Account', 'Amortisation Account', '" . $created_by . "', '" . $updated_by . "','active'),
//   (226, 0, 0, 0, " . $company_id . ", '', 'group', '4', 4, 4, 1, '', 'Finance Cost', 'Finance Cost', '" . $created_by . "', '" . $updated_by . "','active'),
//   (227, 0, 0, 0, " . $company_id . ", '', 'account', '4', 226, 226, 2, '40033', 'Interest on Working Capital', 'Interest on Working Capital', '" . $created_by . "', '" . $updated_by . "','active'),
//   (228, 0, 0, 0, " . $company_id . ", '', 'account', '4', 226, 226, 2, '40034', 'Interest on Term Loan', 'Interest on Term Loan', '" . $created_by . "', '" . $updated_by . "','active'),
//   (229, 0, 0, 0, " . $company_id . ", '', 'account', '4', 226, 226, 2, '40035', 'Interest - Others', 'Interest - Others', '" . $created_by . "', '" . $updated_by . "','active'),
//   (230, 0, 1, 0, " . $company_id . ", '', 'account', '2', 149, 149, 4, '20046', 'GR/IR', 'GR/IR', '" . $created_by . "', '" . $updated_by . "','active'),
//   (231, 0, 1, 0, " . $company_id . ", '', 'account', '2', 149, 149, 4, '20047', 'SR/IR', 'SR/IR', '" . $created_by . "', '" . $updated_by . "','active'),
//   (232, 0, 1, 0, " . $company_id . ", '', 'account', '2', 116, 116, 4, '20048', 'TCS', 'TCS', '" . $created_by . "', '" . $updated_by . "','active'),
//   (234, 0, 0, 0, " . $company_id . ", '', 'account', '2', 166, 166, 4, '20049', 'Salary Payables', 'To create liabilities towards employe\'s salary', '" . $created_by . "', '" . $updated_by . "','active'),
//   (235, 0, 0, 0, " . $company_id . ", '', 'account', '1', 94, 94, 3, '10055', 'Legal Expenses Reimbursement - UIIC', 'Reimbursement of Legal expenses from UIIC towards payment of Legal fees to advocate', '" . $created_by . "', '" . $updated_by . "','active'),
//   (236, 0, 0, 0, " . $company_id . ", '', 'group', '1', 101, 101, 3, '', 'TDS/MAT/Adv Income Tax Receivables', 'Receivables towards Direct Tax', '" . $created_by . "', '" . $updated_by . "','active'),
//   (237, 0, 0, 0, " . $company_id . ", '', 'account', '1', 236, 236, 3, '10056', 'TDS Receivable', 'TDS Receivable deducted by Customers', '" . $created_by . "', '" . $updated_by . "','active'),
//   (239, 0, 0, 0, " . $company_id . ", '', 'account', '1', 236, 236, 3, '10057', 'MAT Credit Receivable ', 'MAT Credit yet to adjust against Income Tax', '" . $created_by . "', '" . $updated_by . "','active'),
//   (240, 0, 0, 0, " . $company_id . ", '', 'account', '1', 94, 94, 3, '10058', 'Legal Expenses Reimbursement Others', 'Reimbursement of Legal expenses towards payment of Legal fees to advocate', '" . $created_by . "', '" . $updated_by . "','active'),
//   (241, 0, 0, 1, " . $company_id . ", '', 'account', '4', 180, 180, 2, '40036', 'Stock Difference', 'Stock Difference', '" . $created_by . "', '" . $updated_by . "','active'),
//   (242, 0, 0, 1, " . $company_id . ", '', 'account', '4', 180, 180, 2, '40037', 'Price Difference', 'Price Difference', '" . $created_by . "', '" . $updated_by . "','active'),
//   (243, 0, 0, 1, " . $company_id . ", '', 'account', '2', 156, 156, 5, '20050', 'RCM GST Payable', 'RCM GST Payable', '" . $created_by . "', '" . $updated_by . "','active'),
//   (244, 0, 0, 1, " . $company_id . ", '', 'group', '1', 68, 68, 2, '', 'Billable Project', 'Billable Project', '" . $created_by . "', '" . $updated_by . "','active'),
//   (245, 0, 0, 1, " . $company_id . ", '', 'account', '1', 244, 244, 3, '10059', 'Billable Project', 'Billable Project', '" . $created_by . "', '" . $updated_by . "','active'),
//   (246, 0, 0, 1, " . $company_id . ", '', 'account', '4', 180, 180, 2, '40038', 'Cost of Goods Manufactured (Activity)', 'Cost of Goods Manufactured (Activity)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (247, 0, 0, 1, " . $company_id . ", '', 'account', '4', 180, 180, 2, '40039', 'Cost of Service Process (Material)', 'Cost of Service Process (Material)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (248, 0, 0, 1, " . $company_id . ", '', 'account', '4', 180, 180, 2, '40040', 'Cost of Service Process (Activity)', 'Cost of Service Process (Activity)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (249, 0, 0, 1, " . $company_id . ", '', 'account', '4', 180, 180, 2, '40041', 'Cost of Service Process (Incoming Service)', 'Cost of Service Process (Incoming Service)', '" . $created_by . "', '" . $updated_by . "','active'),
//   (250, 0, 0, 1, " . $company_id . ", '', 'account', '4', 199, 199, 6, '40042', 'Round Off', 'Round- Off', '" . $created_by . "', '" . $updated_by . "','active'),
//   (251, 0, 0, 1, " . $company_id . ", '', 'account', '4', 180, 180, 2, '40043', 'Cost of Service Sells', 'Cost of Service Sells', '" . $created_by . "', '" . $updated_by . "','active')";

//   $sqlQuery = mysqli_query($dbCon, $instcosdata);

//   $funcMappingsql = "INSERT INTO `erp_acc_functional_mapping` (`company_id`, `function_name`, `slug`, `creditArray`, `debitArray`, `map_created_by`, `map_updated_by`, `map_status`) VALUES
//   (" . $company_id . ", 'GRN Posting', 'grn', 'a:1:{i:0;s:4:\" 230\";}', 'a:1:{i:0;s:2:\"82\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'GRN IV Posting', 'grniv', 'a:2:{i:0;s:4:\" 150\";i:1;s:4:\" 160\";}', 'a:4:{i:0;s:3:\"230\";i:1;s:3:\"157\";i:2;s:3:\"158\";i:3;s:3:\"159\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'SRN Posting', 'srn', 'a:1:{i:0;s:4:\" 231\";}', 'a:1:{i:0;s:2:\"83\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'SRN IV Posting', 'srniv', 'a:3:{i:0;s:4:\" 150\";i:1;s:4:\" 160\";i:2;s:4:\" 243\";}', 'a:4:{i:0;s:3:\"231\";i:1;s:3:\"157\";i:2;s:3:\"158\";i:3;s:3:\"159\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'Payment', 'Payment', 'a:2:{i:0;s:3:\" 90\";i:1;s:3:\" 91\";}', 'a:1:{i:0;s:3:\"150\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'PGI', 'PGI', 'a:1:{i:0;s:3:\" 84\";}', 'a:1:{i:0;s:3:\"181\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'SO Invoicing', 'SOInvoicing', 'a:5:{i:0;s:4:\" 170\";i:1;s:4:\" 157\";i:2;s:4:\" 158\";i:3;s:4:\" 159\";i:4;s:4:\" 232\";}', 'a:1:{i:0;s:2:\"88\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'Collection', 'Collection', 'a:1:{i:0;s:3:\" 88\";}', 'a:2:{i:0;s:2:\"90\";i:1;s:2:\"91\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'Production declaration Inventory issuance', 'ProductiondeclarationInventoryissuance', 'a:2:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";}', 'a:2:{i:0;s:3:\"183\";i:1;s:3:\"184\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'FG/SFG declaration', 'FGSFGDeclaration', 'a:2:{i:0;s:4:\" 182\";i:1;s:4:\" 246\";}', 'a:2:{i:0;s:2:\"84\";i:1;s:2:\"83\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'Production declaration Projects issuance', 'ProductiondeclarationProjectsissuance', 'a:2:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";}', 'a:3:{i:0;s:3:\"183\";i:1;s:3:\"184\";i:2;s:3:\"249\";}', '" . $created_by . "', '" . $updated_by . "', 'active'),

//   (" . $company_id . ", 'Project declaration', 'ProjectDeclaration', 'a:3:{i:0;s:4:\" 247\";i:1;s:4:\" 248\";i:2;s:4:\" 249\";}', 'a:2:{i:0;s:2:\"84\";i:1;s:2:\"83\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'Stock transport without profit', 'Stocktransportwithoutprofit', 'a:3:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";i:2;s:3:\" 84\";}', 'a:3:{i:0;s:2:\"82\";i:1;s:2:\"83\";i:2;s:2:\"84\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'payroll', 'payroll', 'a:5:{i:0;s:4:\" 162\";i:1;s:4:\" 163\";i:2;s:4:\" 164\";i:3;s:4:\" 160\";i:4;s:4:\" 165\";}', 'a:1:{i:0;s:3:\"212\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'Stock Difference Book To Physical', 'stockDifferenceBookToPhysical', 'a:1:{i:0;s:2:\"82\";}', 'a:1:{i:0;s:3:\"241\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'Stock Posting Costcenter', 'stockPostingCostcenter', 'a:1:{i:0;s:3:\" 82\";}', 'a:1:{i:0;s:3:\"208\";}', '" . $created_by . "', '" . $updated_by . "', 'active'),
//   (" . $company_id . ", 'Stock Posting Production Order', 'stockPostingProductionOrder', 'a:2:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";}', 'a:2:{i:0;s:3:\"183\";i:1;s:3:\"184\";}', '" . $created_by . "', '" . $updated_by . "', 'active')";


//   $sqlQuery = mysqli_query($dbCon, $funcMappingsql);



//   $queryGetNumRows = queryGetNumRows("SELECT * FROM `" . ERP_ACC_MAPPING . "` WHERE company_id=$company_id");
//   if ($queryGetNumRows['numRows'] == 0) {
//     $Mappingsql = "INSERT INTO `" . ERP_ACC_MAPPING . "` (`company_id`, `vendor_gl`, `customer_gl`, `itemsRM_gl`, `itemsSFG_gl`, `itemsFG_gl`, `billable_project_gl`, `bank_gl`, `cash_gl`,  `roundoff_gl`, `sales_goods_domestic`, `sales_goods_export`, `sales_services_domestic`, `sales_services_export`, `stock_difference_gl`, `price_difference_gl`,`map_created_by`, `map_updated_by`, `map_status`) VALUES (" . $company_id . ", '150', '88', '82', '83', '84', '245', '90', '91', '250', 170, 171, 172, 173, 241, 242, '" . $created_by . "', '" . $updated_by . "', 'active')";
//     $sqlQuery = mysqli_query($dbCon, $Mappingsql);
//   }

//   if ($sqlQuery) {
//     $returnData['status'] = "success";
//     $returnData['message'] = "Query run successfully";
//     $returnData['instcosdata'] = $instcosdata;
//     $returnData['funcMappingsql'] = $funcMappingsql;
//     $returnData['Mappingsql'] = $Mappingsql;
//   } else {
//     $returnData['status'] = "warning";
//     $returnData['message'] = "Somthing went wrong";
//     // $returnData['queryGetNumRows'] = $queryGetNumRows;
//     $returnData['instcosdata'] = $instcosdata;
//     $returnData['funcMappingsql'] = $funcMappingsql;
//     $returnData['Mappingsql'] = $Mappingsql;
//   }

//   return $returnData;
// }



function importDefaltChartOfAccounts()
{
  global $dbCon;
  global $company_id;
  global $created_by;
  global $companyCountry;
  global $updated_by;
  $returnData = [];


  if ($companyCountry == '103') {
    $fetch_default_coa = queryGet("SELECT * FROM `erp_acc_coa_default`", true);
  } else {
    $fetch_default_coa = queryGet("SELECT * FROM `erp_acc_coa_default_aus`", true);
  }

  foreach ($fetch_default_coa['data'] as $data) {
    // console($data);
    // $returnData[] = $data;
    $glStType = $data['glStType'] ?? NULL;
    $ordering = $data['ordering'];
    $lock_status = $data['lock_status'];
    $txn_status = $data['txn_status'];
    $glSt = $data['glSt'];
    $typeAcc = $data['typeAcc'];
    $p_id = $data['p_id'];
    $sp_id = $data['sp_id'];
    $lvl = $data['lvl'];
    $gl_code = $data['gl_code'];
    $gl_label = addslashes($data['gl_label']);
    $remark = $data['remark'];
    $id = $data['id'];
    $lock_key = $data['lock_with_key'];

    $instcosdata = "INSERT INTO `" . ERP_ACC_CHART_OF_ACCOUNTS . "` ( `id`,
    `ordering`, `lock_status`, `txn_status`, `company_id`, `glSt`, `glStType`, `typeAcc`, `p_id`, `sp_id`, `lvl`, `gl_code`, `gl_label`, `remark`, `created_by`, `updated_by`, `status`) VALUES
    ($id,$ordering,$lock_status,$txn_status, " . $company_id . ",'$glSt', '$glStType', $typeAcc, $p_id, $sp_id, '$lvl', '$gl_code', '$gl_label', '$remark', '" . $created_by . "', '" . $updated_by . "','active');";
    $sqlQuery = queryInsert($instcosdata);
  }
  // exit();

  // return $returnData;

  if ($companyCountry == '103') {
    $funcMappingsql = "INSERT INTO `erp_acc_functional_mapping` (`company_id`, `function_name`, `slug`, `creditArray`, `debitArray`, `map_created_by`, `map_updated_by`, `map_status`) VALUES
  (" . $company_id . ", 'GRN Posting', 'grn', 'a:1:{i:0;s:4:\" 230\";}', 'a:1:{i:0;s:2:\"82\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'GRN IV Posting', 'grniv', 'a:2:{i:0;s:4:\" 150\";i:1;s:4:\" 160\";}', 'a:4:{i:0;s:3:\"230\";i:1;s:3:\"157\";i:2;s:3:\"158\";i:3;s:3:\"159\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'SRN Posting', 'srn', 'a:1:{i:0;s:4:\" 231\";}', 'a:1:{i:0;s:2:\"83\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'SRN IV Posting', 'srniv', 'a:3:{i:0;s:4:\" 150\";i:1;s:4:\" 160\";i:2;s:4:\" 243\";}', 'a:4:{i:0;s:3:\"231\";i:1;s:3:\"157\";i:2;s:3:\"158\";i:3;s:3:\"159\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'Payment', 'Payment', 'a:2:{i:0;s:3:\" 90\";i:1;s:3:\" 91\";}', 'a:1:{i:0;s:3:\"150\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'PGI', 'PGI', 'a:1:{i:0;s:3:\" 84\";}', 'a:1:{i:0;s:3:\"181\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'SO Invoicing', 'SOInvoicing', 'a:5:{i:0;s:4:\" 170\";i:1;s:4:\" 157\";i:2;s:4:\" 158\";i:3;s:4:\" 159\";i:4;s:4:\" 232\";}', 'a:1:{i:0;s:2:\"88\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'Collection', 'Collection', 'a:1:{i:0;s:3:\" 88\";}', 'a:2:{i:0;s:2:\"90\";i:1;s:2:\"91\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'Production declaration Inventory issuance', 'ProductiondeclarationInventoryissuance', 'a:2:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";}', 'a:2:{i:0;s:3:\"183\";i:1;s:3:\"184\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'FG/SFG declaration', 'FGSFGDeclaration', 'a:2:{i:0;s:4:\" 182\";i:1;s:4:\" 246\";}', 'a:2:{i:0;s:2:\"84\";i:1;s:2:\"83\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'Production declaration Projects issuance', 'ProductiondeclarationProjectsissuance', 'a:2:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";}', 'a:3:{i:0;s:3:\"183\";i:1;s:3:\"184\";i:2;s:3:\"249\";}', '" . $created_by . "', '" . $updated_by . "', 'active'),
  
  (" . $company_id . ", 'Project declaration', 'ProjectDeclaration', 'a:3:{i:0;s:4:\" 247\";i:1;s:4:\" 248\";i:2;s:4:\" 249\";}', 'a:2:{i:0;s:2:\"84\";i:1;s:2:\"83\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'Stock transport without profit', 'Stocktransportwithoutprofit', 'a:3:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";i:2;s:3:\" 84\";}', 'a:3:{i:0;s:2:\"82\";i:1;s:2:\"83\";i:2;s:2:\"84\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'payroll', 'payroll', 'a:5:{i:0;s:4:\" 162\";i:1;s:4:\" 163\";i:2;s:4:\" 164\";i:3;s:4:\" 160\";i:4;s:4:\" 165\";}', 'a:1:{i:0;s:3:\"212\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'Stock Difference Book To Physical', 'stockDifferenceBookToPhysical', 'a:1:{i:0;s:2:\"82\";}', 'a:1:{i:0;s:3:\"241\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'Stock Posting Costcenter', 'stockPostingCostcenter', 'a:1:{i:0;s:3:\" 82\";}', 'a:1:{i:0;s:3:\"208\";}', '" . $created_by . "', '" . $updated_by . "', 'active'),
  (" . $company_id . ", 'Stock Posting Production Order', 'stockPostingProductionOrder', 'a:2:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";}', 'a:2:{i:0;s:3:\"183\";i:1;s:3:\"184\";}', '" . $created_by . "', '" . $updated_by . "', 'active')";
  } else {

    $funcMappingsql = "INSERT INTO `erp_acc_functional_mapping` (`company_id`, `function_name`, `slug`, `creditArray`, `debitArray`, `map_created_by`, `map_updated_by`, `map_status`) VALUES
    (" . $company_id . ", 'GRN Posting', 'grn', 'a:1:{i:0;s:4:\" 230\";}', 'a:1:{i:0;s:2:\"82\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'GRN IV Posting', 'grniv', 'a:2:{i:0;s:4:\" 150\";i:1;s:4:\" 160\";}', 'a:3:{i:0;s:4:\" 230\";i:1;s:4:\" 157\";i:2;s:4:\" 252\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'SRN Posting', 'srn', 'a:1:{i:0;s:4:\" 231\";}', 'a:1:{i:0;s:2:\"83\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'SRN IV Posting', 'srniv', 'a:2:{i:0;s:4:\" 150\";i:1;s:4:\" 160\";}', 'a:2:{i:0;s:4:\" 231\";i:1;s:4:\" 157\";s:4:\" 252\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'Payment', 'Payment', 'a:2:{i:0;s:3:\" 90\";i:1;s:3:\" 91\";}', 'a:1:{i:0;s:3:\"150\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'PGI', 'PGI', 'a:1:{i:0;s:3:\" 84\";}', 'a:1:{i:0;s:3:\"181\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'SO Invoicing', 'SOInvoicing', 'a:2:{i:0;s:4:\" 170\";i:1;s:4:\" 157\";}', 'a:1:{i:0;s:2:\"88\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'Collection', 'Collection', 'a:1:{i:0;s:3:\" 88\";}', 'a:2:{i:0;s:2:\"90\";i:1;s:2:\"91\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'Production declaration Inventory issuance', 'ProductiondeclarationInventoryissuance', 'a:2:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";}', 'a:2:{i:0;s:3:\"183\";i:1;s:3:\"184\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'FG/SFG declaration', 'FGSFGDeclaration', 'a:2:{i:0;s:4:\" 182\";i:1;s:4:\" 246\";}', 'a:2:{i:0;s:2:\"84\";i:1;s:2:\"83\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'Production declaration Projects issuance', 'ProductiondeclarationProjectsissuance', 'a:2:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";}', 'a:3:{i:0;s:3:\"183\";i:1;s:3:\"184\";i:2;s:3:\"249\";}', '" . $created_by . "', '" . $updated_by . "', 'active'),
    
    (" . $company_id . ", 'Project declaration', 'ProjectDeclaration', 'a:3:{i:0;s:4:\" 247\";i:1;s:4:\" 248\";i:2;s:4:\" 249\";}', 'a:2:{i:0;s:2:\"84\";i:1;s:2:\"83\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'Stock transport without profit', 'Stocktransportwithoutprofit', 'a:3:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";i:2;s:3:\" 84\";}', 'a:3:{i:0;s:2:\"82\";i:1;s:2:\"83\";i:2;s:2:\"84\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'payroll', 'payroll', 'a:5:{i:0;s:4:\" 162\";i:1;s:4:\" 163\";i:2;s:4:\" 164\";i:3;s:4:\" 160\";i:4;s:4:\" 165\";}', 'a:1:{i:0;s:3:\"212\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'Stock Difference Book To Physical', 'stockDifferenceBookToPhysical', 'a:1:{i:0;s:2:\"82\";}', 'a:1:{i:0;s:3:\"241\";}','" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'Stock Posting Costcenter', 'stockPostingCostcenter', 'a:1:{i:0;s:3:\" 82\";}', 'a:1:{i:0;s:3:\"208\";}', '" . $created_by . "', '" . $updated_by . "', 'active'),
    (" . $company_id . ", 'Stock Posting Production Order', 'stockPostingProductionOrder', 'a:2:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";}', 'a:2:{i:0;s:3:\"183\";i:1;s:3:\"184\";}', '" . $created_by . "', '" . $updated_by . "', 'active')";
  }

  $sqlQuery = mysqli_query($dbCon, $funcMappingsql);



  $queryGetNumRows = queryGetNumRows("SELECT * FROM `" . ERP_ACC_MAPPING . "` WHERE company_id=$company_id");
  if ($queryGetNumRows['numRows'] == 0) {
    $Mappingsql = "INSERT INTO `" . ERP_ACC_MAPPING . "` (`company_id`, `vendor_gl`, `customer_gl`, `itemsRM_gl`, `itemsSFG_gl`, `itemsFG_gl`, `billable_project_gl`, `bank_gl`, `cash_gl`,  `roundoff_gl`, `sales_goods_domestic`, `sales_goods_export`, `sales_services_domestic`, `sales_services_export`, `stock_difference_gl`, `price_difference_gl`,`map_created_by`, `map_updated_by`, `map_status`) VALUES (" . $company_id . ", '150', '88', '82', '83', '84', '245', '90', '91', '250', 170, 171, 172, 173, 241, 242, '" . $created_by . "', '" . $updated_by . "', 'active')";
    $sqlQuery = mysqli_query($dbCon, $Mappingsql);
  }

  if ($sqlQuery) {
    $returnData['status'] = "success";
    $returnData['message'] = "Query run successfully";
    $returnData['instcosdata'] = $instcosdata;
    $returnData['funcMappingsql'] = $funcMappingsql;
    $returnData['Mappingsql'] = $Mappingsql;
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    // $returnData['queryGetNumRows'] = $queryGetNumRows;
    $returnData['instcosdata'] = $instcosdata;
    $returnData['funcMappingsql'] = $funcMappingsql;
    $returnData['Mappingsql'] = $Mappingsql;
  }

  return $returnData;
}
//*************************************/INSERT/******************************************//
function createDataChartOfAccounts($POST = [])
{
  global $dbCon;
  global $created_by;
  global $updated_by;
  $returnData = [];
  $isValidate = validate($POST, [
    "parent" => "required",
    "personal_glcode_lvl" => "required",
    "typeAcc" => "required",
    "gl_label" => "required"
  ], [
    "parent" => "Enter name",
    "personal_glcode_lvl" => "Enter Lvl",
    "typeAcc" => "Enter Type",
    "gl_label" => "Enter GL Label"
  ]);

  if ($isValidate["status"] == "success") {
    $customer_status = 'active';

    $company_id = $POST["company_id"];
    $parent = $POST["parent"];
    $coatype = $POST["coatype"];
    $personal_glcode_lvl = $POST["personal_glcode_lvl"] ?? 0;
    $gl_label = addslashes($POST["gl_label"]);
    $remark = addslashes($POST["remark"]) ?? "";
    $glSt = $POST["glSt"] ?? "";
    $typeAcc = $POST["typeAcc"] ?? "";
    $glStType = $POST["createdata"] ?? "";

    $gl_code = '';
    if ($glStType == 'account') {
      $lastAccsql = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `company_id`='" . $company_id . "' AND typeAcc='" . $typeAcc . "' AND `glStType`='account' ORDER BY `id` DESC LIMIT 1");
      $newAccCode = getCOASerialNumber($lastAccsql['data']['gl_code'], $typeAcc);
      $gl_code = $newAccCode;
    }


    //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

    $sqladd = '';
    $colmnsql = "SELECT COLUMN_NAME, COLUMN_COMMENT
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_NAME = '" . ERP_ACC_CHART_OF_ACCOUNTS . "'
                    AND ORDINAL_POSITION > (
                        SELECT ORDINAL_POSITION 
                        FROM INFORMATION_SCHEMA.COLUMNS
                        WHERE TABLE_NAME = '" . ERP_ACC_CHART_OF_ACCOUNTS . "'
                        AND COLUMN_NAME = 'typeAcc'
                    )
                    AND ORDINAL_POSITION < (
                        SELECT ORDINAL_POSITION 
                        FROM INFORMATION_SCHEMA.COLUMNS
                        WHERE TABLE_NAME = '" . ERP_ACC_CHART_OF_ACCOUNTS . "'
                        AND COLUMN_NAME = 'lvl'
                    )";
    $colmnResponce = queryGet($colmnsql, true)['data'];
    // console($colmnResponce);
    foreach ($colmnResponce as $colmn) {
      $col = $colmn['COLUMN_NAME'];
      $sqladd .= " `$col`='" . $parent . "', ";
    }

    $ins = "INSERT INTO `" . ERP_ACC_CHART_OF_ACCOUNTS . "` 
                            SET
                                `company_id`='" . $company_id . "',
                                " . $sqladd . "
                                `lvl`='" . $personal_glcode_lvl . "',
                                `gl_code`='" . $gl_code . "',
                                `gl_label`='" . $gl_label . "',
                                `remark`='" . $remark . "',
                                `glSt`='" . $glSt . "',
                                `glStType`='" . $glStType . "',
                                `typeAcc`='" . $typeAcc . "',
                                `created_by`='" . $created_by . "',
                                `updated_by`='" . $updated_by . "',
                                `status`='" . $customer_status . "'";

    $returnData = queryInsert($ins);
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Invalid form inputes";
    $returnData['errors'] = $isValidate["errors"];
  }
  return $returnData;
}
//*************************************/UPDATE/******************************************//
function updateDataChartOfAccounts($POST)
{
  global $dbCon;
  global $company_id;
  global $updated_by;
  $returnData = [];
  $isValidate = validate($POST, [
    "p_id" => "required",
    "personal_glcode_lvl" => "required",
    "typeAcc" => "required",
    "gl_label" => "required"
  ], [
    "p_id" => "Select PID",
    "personal_glcode_lvl" => "Enter Lvl",
    "typeAcc" => "Enter Type",
    "gl_label" => "Enter GL Label"
  ]);
  if ($isValidate["status"] == "success" && $POST['editdata'] != 'group') {

    $isValidate = validate($POST, [
      "gl_code" => "required"
    ], [
      "gl_code" => "Enter valid GL Code"
    ]);
  }

  if ($isValidate["status"] == "success") {
    $id = $POST["id"];
    $company_id = $POST["company_id"];
    $p_id = $POST["p_id"];
    $personal_glcode_lvl = $POST["personal_glcode_lvl"] ?? 0;
    $gl_code = $POST["gl_code"];
    $gl_label = addslashes($POST["gl_label"]);
    $remark = addslashes($POST["remark"]) ?? "";
    $glSt = $POST["glSt"] ?? "";
    $typeAcc = $POST["typeAcc"] ?? "";
    $glStType = $POST["editdata"] ?? "";

    // $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `company_id`='" . $company_id . "' AND `p_id`='" . $p_id . "' AND `gl_code`='" . $gl_code . "' AND `status`!='deleted'";
    // if ($res = mysqli_query($dbCon, $sql)) {
    //   if (mysqli_num_rows($res) == 0) {
    $ins = "UPDATE `" . ERP_ACC_CHART_OF_ACCOUNTS . "` 
                            SET
                              `company_id`='" . $company_id . "',
                              `p_id`='" . $p_id . "',
                              `lvl`='" . $personal_glcode_lvl . "',
                              `gl_code`='" . $gl_code . "',
                              `gl_label`='" . $gl_label . "',
                              `remark`='" . $remark . "',
                              `glSt`='" . $glSt . "',
                              `glStType`='" . $glStType . "',
                              `typeAcc`='" . $typeAcc . "',
                              `updated_by`='" . $updated_by . "'
                             WHERE `id`='" . $id . "'";

    if (mysqli_query($dbCon, $ins)) {
      $returnData['status'] = "success";
      $returnData['message'] = "Modification success";
      $returnData['sql'] = $ins;
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Modification failed";
      $returnData['sql'] = $ins;
    }
    //   } else {
    //     $returnData['status'] = "warning";
    //     $returnData['message'] = "Admin not exist";
    //   }
    // } else {
    //   $returnData['status'] = "warning";
    //   $returnData['message'] = "Somthing went wrong";
    // }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Invalid form inputes";
    $returnData['errors'] = $isValidate["errors"];
  }
  return $returnData;
}

//*************************************/UPDATE G/L Mapping/******************************************//
function updateDataGLMapping($POST, $company_id)
{
  global $dbCon;
  $returnData = [];
  $isValidate = validate($POST, [
    "vendor_gl" => "required",
    "itemsRM_gl" => "required",
    "itemsFG_gl" => "required",
    "itemsSFG_gl" => "required",
    "billable_project_gl" => "required",
    "customer_gl" => "required",
    "bank_gl" => "required",
    "cash_gl" => "required",
    "roundoff_gl" => "required",
    "foreignexchange_gl" => "required",
    "writtenback_gl" => "required",
    "bankcharges_gl" => "required",
    "sales_goods_domestic" => "required",
    "sales_goods_export" => "required",
    "sales_services_domestic" => "required",
    "sales_services_export" => "required",
    "stock_difference_gl" => "required",
    "price_difference_gl" => "required",
  ], [
    "vendor_gl" => "Required",
    "itemsRM_gl" => "Required",
    "itemsFG_gl" => "Required",
    "itemsSFG_gl" => "Required",
    "billable_project_gl" => "Required",
    "customer_gl" => "Required",
    "bank_gl" => "Required",
    "cash_gl" => "Required",
    "roundoff_gl" => "Required",
    "foreignexchange_gl" => "required",
    "writtenback_gl" => "required",
    "bankcharges_gl" => "required",
    "sales_goods_domestic" => "Required",
    "sales_goods_export" => "Required",
    "sales_services_domestic" => "Required",
    "sales_services_export" => "Required",
    "stock_difference_gl" => "Required",
    "price_difference_gl" => "Required",
  ]);

  if ($isValidate["status"] == "success") {
    $vendor_gl = $POST["vendor_gl"];
    $itemsRM_gl = $POST["itemsRM_gl"];
    $itemsFG_gl = $POST["itemsFG_gl"];
    $itemsSFG_gl = $POST["itemsSFG_gl"];
    $customer_gl = $POST["customer_gl"];
    $bank_gl = $POST["bank_gl"];
    $cash_gl = $POST["cash_gl"];
    $roundoff_gl = $POST["roundoff_gl"];
    $bankcharges_gl = $POST["bankcharges_gl"];
    $writtenback_gl = $POST["writtenback_gl"];
    $foreignexchange_gl = $POST["foreignexchange_gl"];
    $sales_goods_domestic = $POST["sales_goods_domestic"];
    $sales_goods_export = $POST["sales_goods_export"];
    $sales_services_domestic = $POST["sales_services_domestic"];
    $sales_services_export = $POST["sales_services_export"];
    $billable_project_gl = $POST["billable_project_gl"];
    $price_difference_gl = $POST["price_difference_gl"];
    $stock_difference_gl = $POST["stock_difference_gl"];

    $sql = "SELECT * FROM `" . ERP_ACC_MAPPING . "` WHERE `company_id`='" . $company_id . "'";
    if ($res = mysqli_query($dbCon, $sql)) {
      if (mysqli_num_rows($res) > 0) {
        $ins = "UPDATE `" . ERP_ACC_MAPPING . "` 
                            SET
                                `vendor_gl`='" . $vendor_gl . "',
                                `itemsRM_gl`='" . $itemsRM_gl . "',
                                `itemsFG_gl`='" . $itemsFG_gl . "',
                                `itemsSFG_gl`='" . $itemsSFG_gl . "',
                                `customer_gl`='" . $customer_gl . "',
                                `bank_gl`='" . $bank_gl . "',
                                `cash_gl`='" . $cash_gl . "',
                                `roundoff_gl`='" . $roundoff_gl . "', 
                                `foreignexchange_gl`='" . $foreignexchange_gl . "', 
                                `bankcharges_gl`='" . $bankcharges_gl . "', 
                                `writtenback_gl`='" . $writtenback_gl . "', 
                                `sales_goods_domestic` ='" . $sales_goods_domestic . "',
                                `sales_goods_export` = '" . $sales_goods_export . "',
                                `sales_services_domestic` = '" . $sales_services_domestic . "',
                                `sales_services_export` = '" . $sales_services_export . "',
                                `billable_project_gl` = '" . $billable_project_gl . "',
                                `stock_difference_gl` = '" . $stock_difference_gl . "',
                                `price_difference_gl` = '" . $price_difference_gl . "'
                                WHERE `company_id`='" . $company_id . "'";

        if (mysqli_query($dbCon, $ins)) {
          $returnData['status'] = "success";
          $returnData['message'] = "Data modified success";
        } else {
          $returnData['status'] = "warning";
          $returnData['message'] = "Data modified failed";
        }
      } else {
        // $returnData['status'] = "warning";
        // $returnData['message'] = "Data not exist";
        $ins = "INSERT INTO `" . ERP_ACC_MAPPING . "` 
                            SET
                                `vendor_gl`='" . $vendor_gl . "',
                                `itemsRM_gl`='" . $itemsRM_gl . "',
                                `itemsFG_gl`='" . $itemsFG_gl . "',
                                `itemsSFG_gl`='" . $itemsSFG_gl . "',
                                `customer_gl`='" . $customer_gl . "',
                                `company_id`='" . $company_id . "',
                                `bank_gl`='" . $bank_gl . "',
                                `cash_gl`='" . $cash_gl . "',
                                `roundoff_gl`='" . $roundoff_gl . "',
                                `foreignexchange_gl`='" . $foreignexchange_gl . "', 
                                `bankcharges_gl`='" . $bankcharges_gl . "', 
                                `writtenback_gl`='" . $writtenback_gl . "', 
                                `sales_goods_domestic` ='" . $sales_goods_domestic . "',
                                `sales_goods_export` => '" . $sales_goods_export . "',
                                `sales_services_domestic` => '" . $sales_services_domestic . "',
                                `sales_services_export` => '" . $sales_services_export . "',
                                `billable_project_gl` => '" . $billable_project_gl . "',
                                `stock_difference_gl` = '" . $stock_difference_gl . "',
                                `price_difference_gl` = '" . $price_difference_gl . "'";

        if (mysqli_query($dbCon, $ins)) {
          $returnData['status'] = "success";
          $returnData['message'] = "Data Added success";
        } else {
          $returnData['status'] = "warning";
          $returnData['message'] = "Data Added failed";
        }
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Somthing went wrong";
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Invalid form inputes";
    $returnData['errors'] = $isValidate["errors"];
  }
  return $returnData;
}

function getAllfetchAccountingMappingTbl($company_id)
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_MAPPING . "` WHERE `company_id`='" . $company_id . "' and map_status='active' ORDER BY `map_id` DESC limit 1";
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

function getAllfetchAccountingMappingArray($company_id)
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_MAPPING . "` WHERE `company_id`='" . $company_id . "' and map_status='active' ORDER BY `map_id` DESC limit 1";
  $query = queryGet($sql);
  if ($query['status'] = "success") {
    $datas = $query['data'];
    $rData = array('vendor_gl' => $datas['vendor_gl'], 'itemsRM_gl' => $datas['itemsRM_gl'], 'itemsSFG_gl' => $datas['itemsSFG_gl'], 'itemsFG_gl' => $datas['itemsFG_gl'], 'customer_gl' => $datas['customer_gl'], 'bank_gl' => $datas['bank_gl'], 'cash_gl' => $datas['cash_gl']);


    $sqlExtra = "SELECT parentGlId FROM `erp_extra_sub_ledger` WHERE `company_id`='" . $company_id . "' AND status='active' GROUP BY `parentGlId` ";
    $queryExtra = queryGet($sqlExtra, true);
    if ($queryExtra['status'] = "success") {
      foreach ($queryExtra['data'] as $keyss => $row) {
        $keee = $keyss + 1;
        $rData['extraSl'][$keee] = $row['parentGlId'];
      }
    }

    $returnData['status'] = "success";
    $returnData['message'] = "Data found";
    $returnData['data'] = $rData;
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return json_encode($returnData);
}

function getAllDataChartOfAccountsgroup($company_id)
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND p_id=0  AND `status`!='deleted'";
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

function get_acc_bank_cash_accounts()
{
  global $dbCon;
  global $company_id;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id=" . $company_id . " AND `status`!='deleted'";
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

function get_acc_bank_cash_accounts_details($acc_id)
{
  global $dbCon;
  global $company_id;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id=" . $company_id . " AND id=" . $acc_id . "";
  $returnData = queryGet($sql)['data'];
  return $returnData;
}

function getAllDataChartOfAccounts($company_id, $p_id)
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND p_id=$p_id  AND `status`!='deleted'";
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

function getAllChartOfAccounts_list($company_id, $last = false)
{
  global $dbCon;
  $returnData = [];
  if ($last == true) {
    $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND `status`!='deleted' ORDER BY gl_code";
  } else {
    $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted' ORDER BY gl_code";
  }
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}


function getAllChartOfAccounts_listGroup($company_id)
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND glStType='group' AND `status`!='deleted' ORDER BY gl_code";

  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

function getAllChartOfAccounts_list_Account($company_id)
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND glStType='account' ORDER BY gl_code";

  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

function getAllSubGl_list($company_id)
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT customer_code AS code, trade_name AS name, parentGlId, 'Customer' AS type
    FROM erp_customer WHERE company_id =$company_id
    UNION ALL
    SELECT vendor_code AS code, trade_name AS name, parentGlId, 'Vendor' AS type
    FROM erp_vendor_details WHERE company_id =$company_id
    UNION ALL
    SELECT itemCode AS code, itemName AS name, parentGlId, 'Item' AS type
    FROM erp_inventory_items WHERE company_id =$company_id
    UNION ALL
    SELECT acc_code AS code, bank_name AS name, parent_gl AS parentGlId, 'Bank' AS type
    FROM erp_acc_bank_cash_accounts WHERE company_id =$company_id
    UNION ALL
    SELECT sl_code AS code, sl_name AS name, parentGlId, 'SubGL' AS type
    FROM erp_extra_sub_ledger WHERE company_id =$company_id";

  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}



function getAllChartOfAccounts_list_by_p($company_id, $typeAcc)
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND typeAcc=$typeAcc AND glStType='account'  AND `status`!='deleted' ORDER BY gl_code";

  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

function getAllChartOfAccountsByconditionForMapping($company_id, $last = false)
{
  global $dbCon;
  $returnData = [];
  if ($last == true) {
    $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND p_id!='0' AND glStType='account' AND `status`!='deleted' AND (`lock_status` = 0 OR lock_with_key = '1') ORDER BY gl_code";
    // $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND p_id!='0' AND glStType='account' AND `status`!='deleted' AND (`lock_status` = 0) ORDER BY gl_code";
  } else {
    $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND p_id!='0' AND glStType='account' AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted' AND (`lock_status` = 0 OR lock_with_key = '1') ORDER BY gl_code"; 
    // $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND p_id!='0' AND glStType='account' AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted' AND (`lock_status` = 0) ORDER BY gl_code";
 
  }
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

function getAllChartOfAccountsByconditionForFunctionalMapping($company_id, $last = false)
{
  global $dbCon;
  $returnData = [];
  if ($last == true) {
    $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND p_id!='0' AND glStType='account' AND `status`!='deleted' AND (`lock_status` = 0 OR lock_with_key = '1') ORDER BY gl_code";
  } else {
    $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND p_id!='0' AND glStType='account' AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted' ORDER BY gl_code";
    // $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND p_id!='0' AND glStType='account' AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted' AND (`lock_status` = 0 OR lock_with_key = '1') ORDER BY gl_code";

  }
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

//for ledger view

function getAllChartOfAccountsByconditionForMappingLedger($company_id, $last = false)
{
  global $dbCon;
  $returnData = [];
  if ($last == true) {
    $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND p_id!='0' AND glStType='account' AND `status`!='deleted' ORDER BY gl_code";
  } else {
    $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND p_id!='0' AND glStType='account' AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted' ORDER BY gl_code";
  }
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

//end 
//*************************************/SELECT SINGLE/******************************************//
function getChartOfAccountsDataDetails($key = null)
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `id`=" . $key . "";
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_assoc($res);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

function getChartOfAccountsDataDetails_byparent($pkey = null, $company_id = null)
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `status`!='deleted' AND `p_id`=" . $pkey . " AND `company_id`=" . $company_id . " order by `id`  DESC LIMIT 1";
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_assoc($res);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}


function getChartOfAccountsDataDetailsCOA($key = null)
{
  global $dbCon;
  global $company_id;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `status`!='deleted' AND (`glSt` IS NULL OR `glSt`='') AND `company_id`=" . $company_id . " AND `id`=" . $key . "";
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_assoc($res);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

function getChartOfAccountsDataDetails_byparentCOA($pkey = null, $company_id = null)
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `status`!='deleted' AND (`glSt` IS NULL OR `glSt`='') AND `p_id`=" . $pkey . " AND `company_id`=" . $company_id . " order by `gl_code`  DESC LIMIT 1";
  if ($res = mysqli_query($dbCon, $sql)) {
    if (mysqli_num_rows($res) > 0) {
      $returnData['status'] = "success";
      $returnData['message'] = "Data found";
      $returnData['data'] = mysqli_fetch_assoc($res);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Data not found";
      $returnData['data'] = [];
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['data'] = [];
  }
  return $returnData;
}

//*************************************/UPDATE STATUS/******************************************//
function ChangeStatusChartOfAccounts($data = [], $tableKeyField = "", $tableStatusField = "status")
{
  global $dbCon;
  $tableName = ERP_ACC_CHART_OF_ACCOUNTS;
  $returnData["status"] = null;
  $returnData["message"] = null;
  if (!empty($data)) {
    $id = isset($data["id"]) ? $data["id"] : 0;
    $prevSql = "SELECT * FROM `" . $tableName . "` WHERE `" . $tableKeyField . "`='" . $id . "'";
    $prevExeQuery = mysqli_query($dbCon, $prevSql);
    $prevNumRecords = mysqli_num_rows($prevExeQuery);
    if ($prevNumRecords > 0) {
      $prevData = mysqli_fetch_assoc($prevExeQuery);
      $newStatus = "deleted";
      if ($data["changeStatus"] == "active_inactive") {
        $newStatus = ($prevData[$tableStatusField] == "active") ? "inactive" : "active";
      }
      $changeStatusSql = "UPDATE `" . $tableName . "` SET `" . $tableStatusField . "`='" . $newStatus . "' WHERE `" . $tableKeyField . "`=" . $id;
      if (mysqli_query($dbCon, $changeStatusSql)) {
        $returnData["status"] = "success";
        $returnData["message"] = "Status has been changed to " . strtoupper($newStatus);
      } else {
        $returnData["status"] = "error";
        $returnData["message"] = "Something went wrong, Try again...!";
      }
      $returnData["changeStatusSql"] = $changeStatusSql;
    } else {
      $returnData["status"] = "warning";
      $returnData["message"] = "Something went wrong, Try again...!";
    }
  } else {
    $returnData["status"] = "warning";
    $returnData["message"] = "Please provide all valid data...!";
  }
  return $returnData;
}



function createGlTreeCheckChield($p_id = 0)
{
  $queryObj = queryGet("SELECT * FROM  `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `p_id`=" . $p_id . " AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted'", true);
  if ($queryObj["status"] == "success") {
    return true;
  } else {
    return false;
  }
}

function createGlTree($p_id = 0)
{
  global $company_id;
  $queryObj = queryGet("SELECT * FROM  `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND `p_id`=" . $p_id . " AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted'", true);
  if ($queryObj["status"] == "success") {

?>
    <ul <?= ($p_id == 0) ? "class='tree'" : ""; ?>>
      <?php
      foreach ($queryObj["data"] as $oneGlRow) {
        $labelId = rand(111111, 999999);
        $company_data = getCompanyDataDetails($oneGlRow['company_id']);
        $gl_account_length = $company_data['data']['gl_account_length'];
      ?>
        <li>
          <?php
          if (createGlTreeCheckChield($oneGlRow['id'])) {
          ?>
            <input type="checkbox" id="c<?= $labelId ?>" />
            <label class="tree_label" for="c<?= $labelId ?>"><?= $oneGlRow["gl_label"] ?><?php if ($oneGlRow["p_id"] != 0) { ?>&nbsp;&nbsp; <button type="button" style="cursor: pointer;" class="btn btn-sm edit-gst" title="Edit" data-bs-toggle="modal" data-glid="<?= $oneGlRow['id']; ?>" data-bs-target="#GLedit"><i class="fa fa-edit po-list-icon"></i></button><?php } ?>
            <?php if ($oneGlRow['glStType'] == 'account') { ?> &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $oneGlRow['gl_code'];
                                                                                                                } ?>

            </label>
          <?php
          } else {
          ?>
            <span class="tree_label">
              <p class="gl-type">
                <?= $oneGlRow["gl_label"] ?><?php if ($oneGlRow['glStType'] == 'account') { ?>
                <?php if ($oneGlRow["p_id"] != 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" style="cursor: pointer;" class="btn btn-sm edit-gst" title="Edit" data-glid="<?= $oneGlRow['id']; ?>" data-bs-toggle="modal" data-bs-target="#GLedit"><i class="fa fa-edit po-list-icon"></i></button><?php } ?>

              </p>
              <p class="gl-code">
                || <?php echo $oneGlRow['gl_code'];
                                            } ?>
              </p>
            </span>



          <?php
          }
          ?>
          <?php
          createGlTree($oneGlRow["id"]);
          ?>
        </li>
      <?php
      }
      ?>
    </ul>
  <?php
  }
  //return $queryObj;
}


function previewGlTreeCheckChieldDefult($p_id = 0)
{
  $queryObj = queryGet("SELECT * FROM  `erp_acc_coa_default` WHERE `p_id`=" . $p_id . " AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted'", true);
  if ($queryObj["status"] == "success") {
    return true;
  } else {
    return false;
  }
}

function previewGlTreeDefult($p_id = 0)
{
  global $company_id;
  global $companyCountry;

  if ($companyCountry == 103) {

    $queryObj = queryGet("SELECT * FROM  `erp_acc_coa_default` WHERE company_id=0 AND `p_id`=" . $p_id . " AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted'", true);
  } else {

    $queryObj = queryGet("SELECT * FROM  `erp_acc_coa_default_aus` WHERE company_id=0 AND `p_id`=" . $p_id . " AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted'", true);
  }

  if ($queryObj["status"] == "success") {

  ?>
    <ul <?= ($p_id == 0) ? "class='tree'" : ""; ?>>
      <?php
      foreach ($queryObj["data"] as $oneGlRow) {
        $labelId = rand(111111, 999999);
        $company_data = getCompanyDataDetails($oneGlRow['company_id']);
        $gl_account_length = $company_data['data']['gl_account_length'];
      ?>
        <li>
          <?php
          if (previewGlTreeCheckChieldDefult($oneGlRow['id'])) {
          ?>
            <input type="checkbox" id="c<?= $labelId ?>" checked />
            <label class="tree_label" for="c<?= $labelId ?>"><?= $oneGlRow["gl_label"] ?><?php if ($oneGlRow['glStType'] == 'account') { ?> &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $oneGlRow['gl_code'];
                                                                                                                                                                                              } ?>

            </label>
          <?php
          } else {
          ?>
            <span class="tree_label">
              <p class="gl-type">
                <?= $oneGlRow["gl_label"] ?><?php if ($oneGlRow['glStType'] == 'account') { ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
              </p>
              <p class="gl-code">
                || <?php echo $oneGlRow['gl_code'];
                                            } ?>
              </p>
            </span>



          <?php
          }
          ?>
          <?php
          previewGlTreeDefult($oneGlRow["id"]);
          ?>
        </li>
      <?php
      }
      ?>
    </ul>
  <?php
  }
  //return $queryObj;
}


function createGlTreeArray($p_id = 0)
{
  global $company_id;
  $queryObj = queryGet("SELECT * FROM  `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND `p_id`=" . $p_id . "  AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted'", true);
  $tree = [];
  foreach ($queryObj["data"] as $row) {
    $children = createGlTreeArray($row['id']);
    $tree[] = array(
      'id' => $row['id'],
      'gl_label' => $row['gl_label'],
      'gl_code' => $row['gl_code'],
      'glStType' => $row['glStType'],
      'typeAcc' => $row['typeAcc'],
      'data' => $children
    );
  }
  return $tree;
}


function createGlTreeNewArray($p_id = 0, $id = null)
{
  global $company_id;
  $idcond = '';
  if (!empty($id)) {
    $idcond = " AND id=$id ";
  }
  $queryObj = queryGet("SELECT * FROM  `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id  AND `p_id`=" . $p_id . " " . $idcond . "  AND (`glSt` IS NULL OR `glSt`='') AND `status`='active'", true);
  $tree = [];
  foreach ($queryObj["data"] as $row) {
    $children = createGlTreeNewArray($row['id']);
    $tree[] = array(
      'id' => $row['id'],
      'gl_label' => $row['gl_label'],
      'gl_code' => $row['gl_code'],
      'glStType' => $row['glStType'],
      'typeAcc' => $row['typeAcc'],
      'data' => $children
    );
  }
  return $tree;
}




function createGlTreeCheckChieldAddform($p_id = 0)
{
  $queryObj = queryGet("SELECT * FROM  `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `p_id`=" . $p_id . " AND (`glSt` IS NULL OR `glSt`='') AND `glStType`='group' AND `status`!='deleted'", true);
  if ($queryObj["status"] == "success") {
    return true;
  } else {
    return false;
  }
}
function createGlTreeAddform($p_id = 0)
{
  global $company_id;
  $queryObj = queryGet("SELECT * FROM  `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND `p_id`=" . $p_id . " AND (`glSt` IS NULL OR `glSt`='') AND `glStType`='group' AND `status`!='deleted'", true);
  if ($queryObj["status"] == "success") {

  ?>
    <ul <?= ($p_id == 0) ? "class='tree p-2 m-2'" : ""; ?>>
      <?php
      foreach ($queryObj["data"] as $oneGlRow) {
        $labelId = rand(111111, 999999);
        $company_data = getCompanyDataDetails($oneGlRow['company_id']);
        $gl_account_length = $company_data['data']['gl_account_length'];
      ?>
        <li>
          <?php
          if (createGlTreeCheckChieldAddform($oneGlRow['id'])) {
          ?>
            <input type="checkbox" id="c<?= $labelId ?>" />
            <label class="tree_label" for="c<?= $labelId ?>"><?= $oneGlRow["gl_label"] ?><?php if ($oneGlRow['glStType'] == 'account') { ?> &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $oneGlRow['gl_code'];
                                                                                                                                                                                              }  ?>

            </label>
          <?php
          } else {
          ?>
            <span class="tree_label"><?= $oneGlRow["gl_label"] ?><?php if ($oneGlRow['glStType'] == 'account') { ?> &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $oneGlRow['gl_code'];
                                                                                                                                                                      } ?>

          <?php
          }
          ?>
          <?php
          createGlTreeAddform($oneGlRow["id"]);
          ?>
        </li>
      <?php
      }
      ?>
    </ul>
<?php
  }
  //return $queryObj;
}


function length_calculater($pdata, $typeAcc, $ac_length, $length_bkup, $lvl, $lastrow_personal_glcode)
{
  $lengthGL = $ac_length; //company Lavel GL bkup
  if ($lvl === null) {
    $lastrow_personal_lvl = 0;
  } else {
    $lastrow_personal_lvl = $lvl + 1; //This lavel bkup lvl by parent
  }
  $length_bkup = explode('-', $length_bkup); //company Lavel GL bkup to array
  $length_bkup_count = count($length_bkup); //company Lavel GL bkup array Count
  if ($length_bkup_count > $lastrow_personal_lvl) {
    $total_length = 0; // parent and this lavel total lvl length
    $last_length = 0;
    for ($i = 0; $i <= $lastrow_personal_lvl; $i++) {
      $last_length = $length_bkup[$i];
      $total_length += $last_length;
    }
    $last_leng_frm = $total_length - $last_length;

    if (!empty($lastrow_personal_glcode)) {
      $last_id = substr($lastrow_personal_glcode, $last_leng_frm, $last_length) + 1;
    } else {
      $last_id = substr($pdata, $last_leng_frm, $last_length) + 1;
    }

    $last_leng_frm = $total_length - $last_length;
    if (!empty($pdata)) {
      $length_wise_start_end = length_wise_start_end($last_length);
      $new_personal_glcode = str_pad($last_id, $last_length, 0, STR_PAD_LEFT);
    } else {
      $length_wise_start_end = length_wise_start_end_right($last_length);
      $new_personal_glcode = str_pad($last_id, $last_length, 0, STR_PAD_RIGHT);
      $typeAcc = $new_personal_glcode;
    }
    $start = $length_wise_start_end['start'];
    $end = intval($length_wise_start_end['end']);


    if ($new_personal_glcode > $end) {
      $new_personal_glcode_status['status'] = 'warning';
      $new_personal_glcode_status['message'] = 'Not Possible to create This G/L code';
    } else {
      $prefix = substr($pdata, 0, $last_leng_frm);
      $nmn = $lengthGL - $total_length;
      $safix = '';
      if ($nmn != 0) {
        $safix = str_pad(0, $nmn, 0, STR_PAD_LEFT);
      }
      $fullgl = ($prefix . $new_personal_glcode . $safix);

      $new_personal_glcode_status['status'] = 'success';
      $new_personal_glcode_status['message'] = 'Available This G/L code';
      $new_personal_glcode_status['parent_full_gl_code'] = $pdata;
      $new_personal_glcode_status['personal_typeAcc'] = $typeAcc;
      $new_personal_glcode_status['personal_full_gl_code'] = $fullgl;
      $new_personal_glcode_status['personal_glcode_lvl'] = $lastrow_personal_lvl;
    }
  } else {
    $new_personal_glcode_status['status'] = 'warning';
    $new_personal_glcode_status['message'] = 'Not Possible to create a child G/L code';
  }
  return $new_personal_glcode_status;
}


function get_full_gl_code($new_personal_glcode)
{
  $full_account_Number_preview = $new_personal_glcode;
  return $full_account_Number_preview;
}

function length_wise_start_end($length)
{
  $start = str_pad(1, $length, 0, STR_PAD_LEFT);
  $end = str_pad(9, $length, 9, STR_PAD_LEFT);

  $data['start'] = $start;
  $data['end'] = $end;

  return $data;
}
function length_wise_start_end_right($length)
{
  $start = str_pad(1, $length, 0, STR_PAD_RIGHT);
  $end = str_pad(9, $length, 9, STR_PAD_LEFT);

  $data['start'] = $start;
  $data['end'] = $end;

  return $data;
}
//$hhhh= length_wise_start_end(9);
//echo '<br>'.$hhhh['start'] . '<br> ' . $hhhh['end'] ;

function zeroview($num)
{
  $d = str_pad(0, $num, 0, STR_PAD_LEFT);
  return $d;
}

// =========================================================================================

//---------------------COA Acc Number Start------------------
function getCOASerialNumber($lastsl, $typeAcc)
{
  $now_prefix = $typeAcc;
  $count = strlen($lastsl);
  if ($count == 5) {
    $old_prefix = substr($lastsl, 0, 1);

    if ($now_prefix == $old_prefix) {
      $sl = substr($lastsl, -4);
    } else {
      $sl = 0;
    }
  } else {
    $sl = 0;
  }
  $id = $sl + 1;
  return $invoice_no = $typeAcc . str_pad($id, 4, 0, STR_PAD_LEFT);
}
//--------------------------COAEnd---------------------------


function length_new_coa($typeAcc, $lvl)
{
  global $company_id;
  if ($lvl === null) {
    $lastrow_personal_lvl = 0;
  } else {
    $lastrow_personal_lvl = $lvl + 1; //This lavel bkup lvl by parent
  }

  $lastAccsql = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `company_id`='" . $company_id . "' AND typeAcc='" . $typeAcc . "' AND `glStType`='account' ORDER BY `id` DESC");
  $newAccCode = getCOASerialNumber($lastAccsql['data']['gl_code'], $typeAcc);

  $new_personal_glcode_status['status'] = 'success';
  $new_personal_glcode_status['message'] = 'Available This G/L code';
  $new_personal_glcode_status['personal_typeAcc'] = $typeAcc;
  $new_personal_glcode_status['gl_code_preview'] = $newAccCode;
  $new_personal_glcode_status['personal_glcode_lvl'] = $lastrow_personal_lvl;

  return $new_personal_glcode_status;
}

function updateGlTreeLabel($id, $pid, $level, $coatype)
{
  global $company_id;
  $changeStatusSql = queryUpdate("UPDATE `" . ERP_ACC_CHART_OF_ACCOUNTS . "` SET `lvl`='$level', `$coatype`=" . $pid . " WHERE `id`=" . $id . "");

  $queryObj = queryGet("SELECT * FROM  `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `$coatype`=" . $id . " ", true);
  if ($queryObj['status'] == 'success') {
    $level = $level + 1;
    foreach ($queryObj["data"] as $row) {

      $children = updateGlTreeLabel($row['id'], $row[$coatype], $coatype, $level);
    }
  }
  return $changeStatusSql;
}

function orderArray($array, $parent = 0, $level = 1)
{
  $result = [];

  foreach ($array as $key => $item) {
    if ($item['parent_id'] == $parent) {
      $item['level'] = $level;
      $result[] = $item;
      unset($array[$key]);
      $children = orderArray($array, $item['id'], $level + 1);
      $result = array_merge($result, $children);
    }
  }

  return $result;
}

function makeGlTreeLabel($id, $typeAcc, $coatype)
{
  global $company_id;
  $queryObj1 = queryGet("SELECT id,$coatype as parent_id, CONCAT(gl_label, ' ', gl_code,'') AS title, (lvl+1) as level, ordering, lock_status, 0 AS txn_status, glStType as glsttype, gl_code   FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND typeAcc=$typeAcc AND id=$id AND `status`='active'");
  $responce[] = $queryObj1['data'];

  $queryObj = queryGet("SELECT id,$coatype as parent_id, CONCAT(gl_label, ' ', gl_code,'') AS title, (lvl+1) as level, ordering, lock_status, 0 AS txn_status, glStType as glsttype, gl_code   FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND `$coatype`=" . $id . " AND typeAcc=$typeAcc AND `status`='active'  ORDER BY id,$coatype,lvl ASC", true);

  if ($queryObj['status'] == 'success') {
    foreach ($queryObj["data"] as $row) {

      //   $responce[] = $row;
      $childr = makeGlTreeLabel($row['id'], $typeAcc, $coatype);
    }
  }
  return $childr;
}


//*************************************/END/******************************************//