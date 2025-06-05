<?php
//*************************************/INSERT/******************************************//
function importDefaltChartOfAccounts()
{
  global $dbCon;
  global $company_id;
  global $created_by;
  global $updated_by;
  $returnData = [];


  $instcosdata = "INSERT INTO `" . ERP_ACC_CHART_OF_ACCOUNTS . "` (`id`, `company_id`, `glSt`, `glStType`, `typeAcc`, `p_id`, `sp_id`, `lvl`, `gl_code`, `gl_label`, `remark`, `created_by`, `updated_by`, `status`) VALUES
  (5, " . $company_id . ", '', 'group', '1', 1, NULL, 1, '1010000000', 'Non Current Assets', 'Non Current Assets', '" . $created_by . "', '" . $updated_by . "','active'),
  (6, " . $company_id . ", '', 'group', '1', 5, NULL, 2, '1010100000', 'Tangible Asset', 'Tangible Asset', '" . $created_by . "', '" . $updated_by . "','active'),
  (7, " . $company_id . ", '', 'group', '1', 5, NULL, 2, '1010200000', 'Intangible Asset', 'Intangible Asset', '" . $created_by . "', '" . $updated_by . "','active'),
  (8, " . $company_id . ", '', 'group', '1', 5, NULL, 2, '1010300000', 'Capital Work in progress', 'Capital Work in progress', '" . $created_by . "', '" . $updated_by . "','active'),
  (9, " . $company_id . ", '', 'group', '1', 5, NULL, 2, '1010400000', 'Loan & Advances', 'Loan & Advances', '" . $created_by . "', '" . $updated_by . "','active'),
  (10, " . $company_id . ", '', 'group', '1', 5, NULL, 2, '1010500000', 'Others Non-Current Assets', 'Others Non-Current Assets', '" . $created_by . "', '" . $updated_by . "','active'),
  (11, " . $company_id . ", '', 'group', '1', 1, NULL, 1, '1020000000', 'Current Assets', 'Current Assets', '" . $created_by . "', '" . $updated_by . "','active'),
  (12, " . $company_id . ", '', 'group', '1', 11, NULL, 2, '1020100000', 'Inventories', 'Inventories', '" . $created_by . "', '" . $updated_by . "','active'),
  (13, " . $company_id . ", '', 'account', '1', 12, NULL, 3, '1020101000', 'RM Inventory', '', '" . $created_by . "', '" . $updated_by . "','active'),
  (14, " . $company_id . ", '', 'account', '1', 12, NULL, 3, '1020102000', 'FG Inventory', '', '" . $created_by . "', '" . $updated_by . "','active'),
  (15, " . $company_id . ", '', 'account', '1', 12, NULL, 3, '1020103000', 'SFG Inventory', '', '" . $created_by . "', '" . $updated_by . "','active'),
  (16, " . $company_id . ", '', 'group', '1', 11, NULL, 2, '1020200000', 'Other Current Assets', 'Other Current Assets', '" . $created_by . "', '" . $updated_by . "','active'),
  (17, " . $company_id . ", '', 'account', '1', 16, NULL, 3, '1020201000', 'TDS Receivable', 'TDS Receivable', '" . $created_by . "', '" . $updated_by . "','active'),
  (18, " . $company_id . ", '', 'group', '1', 11, NULL, 3, '1020300000', 'Cash and Cash Equivalent', 'Cash and Cash Equivalent', '" . $created_by . "', '" . $updated_by . "','active'),
  (19, " . $company_id . ", '', 'account', '1', 18, NULL, 4, '1020301000', 'Bank Account', 'Bank Account', '" . $created_by . "', '" . $updated_by . "','active'),
  (20, " . $company_id . ", '', 'account', '1', 18, NULL, 4, '1020302000', 'Cash Account', 'Cash Account', '" . $created_by . "', '" . $updated_by . "','active'),
  (21, " . $company_id . ", '', 'group', '1', 11, NULL, 2, '1020400000', 'Trade Receivable', 'The owned to a business by its customer following the sale of goods and service', '" . $created_by . "', '" . $updated_by . "','active'),
  (22, " . $company_id . ", '', 'account', '1', 21, NULL, 3, '1020401000', 'Sundry Debtors', 'All Customers Willbe Map here', '" . $created_by . "', '" . $updated_by . "','active'),
  (23, " . $company_id . ", '', 'group', '1', 11, NULL, 2, '1020500000', 'Short Term Loan & Advances', 'Short Term Loan & Advances', '" . $created_by . "', '" . $updated_by . "','active'),
  (24, " . $company_id . ", '', 'group', '2', 2, NULL, 1, '2010000000', 'Shareholder\'s Fund', 'Shareholder\'s Fund', '" . $created_by . "', '" . $updated_by . "','active'),
  (25, " . $company_id . ", '', 'group', '2', 24, NULL, 2, '2010100000', 'Share Capital', '', '" . $created_by . "', '" . $updated_by . "','active'),
  (26, " . $company_id . ", '', 'group', '2', 24, NULL, 2, '2010200000', 'Reserve & Surplus', '', '" . $created_by . "', '" . $updated_by . "','active'),
  (27, " . $company_id . ", '', 'account', '2', 26, NULL, 3, '2010201000', 'TCS', '', '" . $created_by . "', '" . $updated_by . "','active'),
  (28, " . $company_id . ", '', 'group', '2', 2, NULL, 1, '2020000000', 'Non-Current Liabilities', '', '" . $created_by . "', '" . $updated_by . "','active'),
  (29, " . $company_id . ", '', 'group', '2', 28, NULL, 2, '2020100000', 'Deferred Tax Liabilities', '', '" . $created_by . "', '" . $updated_by . "','active'),
  (30, " . $company_id . ", '', 'group', '2', 28, NULL, 2, '2020200000', 'Long Term Borrowings', '', '" . $created_by . "', '" . $updated_by . "','active'),
  (31, " . $company_id . ", '', 'group', '2', 2, NULL, 1, '2030000000', 'Current Liabilities', '', '" . $created_by . "', '" . $updated_by . "','active'),
  (32, " . $company_id . ", '', 'group', '2', 31, NULL, 2, '2030100000', 'Trade Payables', '', '" . $created_by . "', '" . $updated_by . "','active'),
  (33, " . $company_id . ", '', 'account', '2', 32, NULL, 3, '2030101000', 'GR/IR', 'GR/IR', '" . $created_by . "', '" . $updated_by . "','active'),
  (34, " . $company_id . ", '', 'account', '2', 32, NULL, 3, '2030102000', 'SR/IR', 'SR/IR', '" . $created_by . "', '" . $updated_by . "','active'),
  (35, " . $company_id . ", '', 'account', '2', 32, NULL, 3, '2030103000', ' Vendor A/C', 'Vendor A/C', '" . $created_by . "', '" . $updated_by . "','active'),
  (36, " . $company_id . ", '', 'group', '2', 31, NULL, 2, '2030200000', 'Short Term Borrowings', 'Short Term Borrowings', '" . $created_by . "', '" . $updated_by . "','active'),
  (37, " . $company_id . ", '', 'group', '2', 31, NULL, 2, '2030300000', 'Short Term Provisions', 'Short Term Provisions', '" . $created_by . "', '" . $updated_by . "','active'),
  (38, " . $company_id . ", '', 'group', '2', 31, NULL, 2, '2030400000', 'Others Current Liabilities', 'Others Current Liabilities', '" . $created_by . "', '" . $updated_by . "','active'),
  (39, " . $company_id . ", '', 'account', '2', 38, NULL, 3, '2030401000', 'Input CGST', 'Both for Input & Output', '" . $created_by . "', '" . $updated_by . "','active'),
  (40, " . $company_id . ", '', 'account', '2', 38, NULL, 3, '2030402000', 'Input SGST', 'Input SGST', '" . $created_by . "', '" . $updated_by . "','active'),
  (41, " . $company_id . ", '', 'account', '2', 38, NULL, 3, '2030403000', 'Input IGST', 'Input IGST', '" . $created_by . "', '" . $updated_by . "','active'),
  (42, " . $company_id . ", '', 'account', '2', 38, NULL, 3, '2030404000', 'TDS Payable', 'TDS Payable', '" . $created_by . "', '" . $updated_by . "','active'),
  (43, " . $company_id . ", '', 'account', '2', 38, NULL, 3, '2030405000', 'Output IGST', 'Output IGST', '" . $created_by . "', '" . $updated_by . "','active'),
  (44, " . $company_id . ", '', 'account', '2', 38, NULL, 3, '2030406000', 'Output CGST', 'Output CGST', '" . $created_by . "', '" . $updated_by . "','active'),
  (45, " . $company_id . ", '', 'account', '2', 38, NULL, 3, '2030407000', 'Output SGST', 'Output SGST', '" . $created_by . "', '" . $updated_by . "','active'),
  (46, " . $company_id . ", '', 'group', '3', 3, NULL, 1, '3010000000', 'Revenue from Operation', 'Revenue from Operation', '" . $created_by . "', '" . $updated_by . "','active'),
  (47, " . $company_id . ", '', 'account', '3', 46, NULL, 2, '3010100000', 'Sales Goods (Domestic)', 'Sales Goods (Domestic)', '" . $created_by . "', '" . $updated_by . "','active'),
  (48, " . $company_id . ", '', 'account', '3', 46, NULL, 2, '3010200000', 'Sales Goods (Export)', 'To account for International sales of Goods ', '" . $created_by . "', '" . $updated_by . "','active'),
  (49, " . $company_id . ", '', 'account', '3', 46, NULL, 2, '3010300000', 'Sales Services (Domestic)', 'To account for Domestic sale of Services', '" . $created_by . "', '" . $updated_by . "','active'),
  (50, " . $company_id . ", '', 'account', '3', 46, NULL, 2, '3010400000', 'Sales Services (Export)', 'To account for International sale of services', '" . $created_by . "', '" . $updated_by . "','active'),
  (51, " . $company_id . ", '', 'group', '3', 3, NULL, 1, '3020000000', 'Non Operative Income', 'Non Operative Income', '" . $created_by . "', '" . $updated_by . "','active'),
  (52, " . $company_id . ", '', 'account', '3', 51, NULL, 2, '3020100000', 'Interest Income', 'Interest Income', '" . $created_by . "', '" . $updated_by . "','active'),
  (53, " . $company_id . ", '', 'account', '3', 51, NULL, 2, '3020200000', 'Dividend Income', 'Dividend Income', '" . $created_by . "', '" . $updated_by . "','active'),
  (54, " . $company_id . ", '', 'account', '3', 51, NULL, 2, '3020300000', 'Gain/Loss on Sale of Mutual Fund', 'Gain/Loss on Sale of Mutual Fund', '" . $created_by . "', '" . $updated_by . "','active'),
  (55, " . $company_id . ", '', 'account', '3', 51, NULL, 2, '3020400000', 'Gain/Loss on Sale of Fixed Assets', 'Gain/Loss on Sale of Fixed Assets', '" . $created_by . "', '" . $updated_by . "','active'),
  (56, " . $company_id . ", '', 'account', '3', 51, NULL, 2, '3020500000', 'Miscellaneous Income', 'Miscellaneous Income', '" . $created_by . "', '" . $updated_by . "','active'),
  (57, " . $company_id . ", '', 'group', '4', 4, NULL, 1, '4010000000', 'Direct Material Cost', 'Direct Material Cost', '" . $created_by . "', '" . $updated_by . "','active'),
  (58, " . $company_id . ", '', 'account', '4', 57, NULL, 2, '4010100000', 'Cost of Goods Sold', 'Cost of Goods Sold', '" . $created_by . "', '" . $updated_by . "','active'),
  (59, " . $company_id . ", '', 'account', '4', 57, NULL, 2, '4010200000', 'Cost of Goods Manufactured', 'Cost of Goods Manufactured', '" . $created_by . "', '" . $updated_by . "','active'),
  (60, " . $company_id . ", '', 'account', '4', 57, NULL, 2, '4010300000', 'Consumption of RM', 'Consumption of RM', '" . $created_by . "', '" . $updated_by . "','active'),
  (61, " . $company_id . ", '', 'account', '4', 57, NULL, 2, '4010400000', 'Consumption of SFG', 'Consumption of SFG', '" . $created_by . "', '" . $updated_by . "','active'),
  (62, " . $company_id . ", '', 'group', '4', 4, NULL, 1, '4020000000', 'Direct Expense', 'Direct Expense', '" . $created_by . "', '" . $updated_by . "','active'),
  (63, " . $company_id . ", '', 'account', '4', 62, NULL, 2, '4020100000', 'Direct Labour', 'Direct Labour', '" . $created_by . "', '" . $updated_by . "','active'),
  (64, " . $company_id . ", '', 'group', '4', 4, NULL, 1, '4030000000', 'Indirect Expense', 'Indirect Expense', '" . $created_by . "', '" . $updated_by . "','active'),
  (65, " . $company_id . ", '', 'group', '4', 64, NULL, 2, '4030100000', 'Membership and Subscriptions', 'Membership and Subscriptions', '" . $created_by . "', '" . $updated_by . "','active'),
  (66, " . $company_id . ", '', 'account', '4', 65, NULL, 3, '4030101000', 'Membership Fee', 'Membership Fee', '" . $created_by . "', '" . $updated_by . "','active'),
  (67, " . $company_id . ", '', 'group', '4', 64, NULL, 2, '4030200000', 'Technical, Legal, Professional and Consultations', 'All technical, legal, professional, consultations expenses to be accounted', '" . $created_by . "', '" . $updated_by . "','active'),
  (68, " . $company_id . ", '', 'account', '4', 67, NULL, 3, '4030201000', 'Technical Consultancy ', 'Technical Consultancy ', '" . $created_by . "', '" . $updated_by . "','active'),
  (69, " . $company_id . ", '', 'account', '4', 67, NULL, 3, '4030202000', 'Legal Consultancy', 'Legal Consultancy', '" . $created_by . "', '" . $updated_by . "','active'),
  (70, " . $company_id . ", '', 'account', '4', 67, NULL, 3, '4030203000', 'Professional Advisory', 'Professional Advisory', '" . $created_by . "', '" . $updated_by . "','active'),
  (71, " . $company_id . ", '', 'account', '4', 67, NULL, 3, '4030204000', 'Management Advisory', 'Management Advisory', '" . $created_by . "', '" . $updated_by . "','active'),
  (72, " . $company_id . ", '', 'group', '4', 64, NULL, 2, '4030300000', 'Traveling and Conveyance', 'Traveling and Conveyance', '" . $created_by . "', '" . $updated_by . "','active'),
  (73, " . $company_id . ", '', 'account', '4', 72, NULL, 3, '4030301000', 'Local Conveyance', 'Local Conveyance', '" . $created_by . "', '" . $updated_by . "','active'),
  (74, " . $company_id . ", '', 'account', '4', 72, NULL, 3, '4030302000', 'Domestic Travel', 'Domestic Travel', '" . $created_by . "', '" . $updated_by . "','active'),
  (75, " . $company_id . ", '', 'account', '4', 72, NULL, 3, '4030303000', 'International Travel', 'International Travel', '" . $created_by . "', '" . $updated_by . "','active'),
  (76, " . $company_id . ", '', 'group', '4', 64, NULL, 2, '4030400000', 'Office and Establishment Expenses', 'Office and Establishment Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
  (77, " . $company_id . ", '', 'account', '4', 76, NULL, 3, '4030401000', 'Office Rent', 'Office Rent', '" . $created_by . "', '" . $updated_by . "','active'),
  (78, " . $company_id . ", '', 'account', '4', 76, NULL, 3, '4030402000', 'Office Maintenance Expenses ', 'Office Maintenance Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
  (79, " . $company_id . ", '', 'account', '4', 76, NULL, 3, '4030403000', 'Electricity Expenses', 'Electricity Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
  (80, " . $company_id . ", '', 'account', '4', 76, NULL, 3, '4030404000', 'Internet Expenses', 'Internet Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
  (81, " . $company_id . ", '', 'account', '4', 76, NULL, 3, '4030405000', 'Mobile and Telephone Expenses', 'Mobile and Telephone Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
  (82, " . $company_id . ", '', 'group', '4', 64, NULL, 2, '4030500000', 'Repair & Maintenance ', 'Repair & Maintenance ', '" . $created_by . "', '" . $updated_by . "','active'),
  (83, " . $company_id . ", '', 'account', '4', 82, NULL, 3, '4030501000', 'Repair & Maintenance', 'Repair & Maintenance', '" . $created_by . "', '" . $updated_by . "','active'),
  (84, " . $company_id . ", '', 'group', '4', 4, NULL, 1, '4040000000', 'Indirect Material Cost', 'Store, Consumable, Spares and other indirect material those are not directly identified in the product\n', '" . $created_by . "', '" . $updated_by . "','active'),
  (85, " . $company_id . ", '', 'account', '4', 84, NULL, 2, '4040100000', 'Consumption of Consumables', 'Consumption of Consumables', '" . $created_by . "', '" . $updated_by . "','active'),
  (86, " . $company_id . ", '', 'account', '4', 84, NULL, 2, '4040200000', 'Consumption of Spares', 'Consumption of Spares', '" . $created_by . "', '" . $updated_by . "','active'),
  (87, " . $company_id . ", '', 'group', '4', 4, NULL, 1, '4050000000', 'Employee Benefit Expenses', 'Employee Benefit Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
  (88, " . $company_id . ", '', 'group', '4', 87, NULL, 2, '4050100000', 'Salary, Wages and Bonus', 'Salary, Wages and Bonus', '" . $created_by . "', '" . $updated_by . "','active'),
  (89, " . $company_id . ", '', 'account', '4', 88, NULL, 3, '4050101000', 'Salary Account', 'To account employee salary', '" . $created_by . "', '" . $updated_by . "','active'),
  (90, " . $company_id . ", '', 'account', '4', 88, NULL, 3, '4050102000', 'Wages Account', 'to account employee wages', '" . $created_by . "', '" . $updated_by . "','active'),
  (91, " . $company_id . ", '', 'account', '4', 88, NULL, 3, '4050103000', 'Bonus Account', 'To account employee bonus', '" . $created_by . "', '" . $updated_by . "','active'),
  (92, " . $company_id . ", '', 'account', '4', 88, NULL, 3, '4050104000', 'Leave Encashment', 'Leave Encashment', '" . $created_by . "', '" . $updated_by . "','active'),
  (93, " . $company_id . ", '', 'account', '4', 88, NULL, 3, '4050105000', 'Overtime Salary', 'Overtime Salary', '" . $created_by . "', '" . $updated_by . "','active'),
  (94, " . $company_id . ", '', 'group', '4', 87, NULL, 2, '4050200000', 'Contribution to PF, ESI and Other Fund', 'Contribution to PF, ESI and Other Fund', '" . $created_by . "', '" . $updated_by . "','active'),
  (95, " . $company_id . ", '', 'account', '4', 94, NULL, 3, '4050201000', 'Employer Contribution to PF', 'Employer Contribution to PF', '" . $created_by . "', '" . $updated_by . "','active'),
  (96, " . $company_id . ", '', 'account', '4', 94, NULL, 3, '4050202000', 'Employer Contribution to ESI', 'Employer Contribution to ESI', '" . $created_by . "', '" . $updated_by . "','active'),
  (97, " . $company_id . ", '', 'account', '4', 94, NULL, 3, '4050203000', 'Employer Contribution to Gratuity', 'Employer Contribution to Gratuity', '" . $created_by . "', '" . $updated_by . "','active'),
  (98, " . $company_id . ", '', 'group', '4', 87, NULL, 2, '4050300000', 'Staff welfares', 'Staff welfares', '" . $created_by . "', '" . $updated_by . "','active'),
  (99, " . $company_id . ", '', 'account', '4', 98, NULL, 3, '4050301000', 'Staff welfare Expenses', 'Staff welfare Expenses', '" . $created_by . "', '" . $updated_by . "','active'),
  (100, " . $company_id . ", '', 'group', '4', 4, NULL, 1, '4060000000', 'Depreciation and Amortisation ', 'Depreciation and Amortisation ', '" . $created_by . "', '" . $updated_by . "','active'),
  (101, " . $company_id . ", '', 'account', '4', 100, NULL, 2, '4060100000', 'Depreciation Account', 'Depreciation Account', '" . $created_by . "', '" . $updated_by . "','active'),
  (102, " . $company_id . ", '', 'account', '4', 100, NULL, 2, '4060200000', 'Amortisation Account', 'Amortisation Account', '" . $created_by . "', '" . $updated_by . "','active'),
  (103, " . $company_id . ", '', 'group', '4', 4, NULL, 1, '4070000000', 'Finance Cost', 'Finance Cost', '" . $created_by . "', '" . $updated_by . "','active'),
  (104, " . $company_id . ", '', 'account', '4', 103, NULL, 2, '4070100000', 'Interest on Working Capital', 'Interest on Working Capital', '" . $created_by . "', '" . $updated_by . "','active'),
  (105, " . $company_id . ", '', 'account', '4', 103, NULL, 2, '4070200000', 'Interest on Term Loan', 'Interest on Term Loan', '" . $created_by . "', '" . $updated_by . "','active'),
  (106, " . $company_id . ", '', 'account', '4', 103, NULL, 2, '4070300000', 'Interest - Others', 'Interest - Others', '" . $created_by . "', '" . $updated_by . "','active')";
  
  $sqlQuery = mysqli_query($dbCon, $instcosdata);

  $funcMappingsql = "INSERT INTO `erp_acc_functional_mapping` (`company_id`, `function_name`, `slug`, `creditArray`, `debitArray`, `map_created_by`, `map_updated_by`, `map_status`) VALUES 
    (" . $company_id . ", 'GRN Posting', 'grn', 'a:1:{i:0;s:3:\" 33\";}', 'a:1:{i:0;s:2:\"13\";}','" . $created_by . "', '" . $updated_by . "', 'active'), 
    (" . $company_id . ", 'GRN IV Posting', 'grniv', 'a:2:{i:0;s:3:\" 35\";i:1;s:3:\" 42\";}', 'a:4:{i:0;s:2:\"33\";i:1;s:2:\"41\";i:2;s:2:\"39\";i:3;s:2:\"40\";}','" . $created_by . "', '" . $updated_by . "', 'active'), 
    (" . $company_id . ", 'SRN Posting', 'srn', 'a:1:{i:0;s:3:\" 34\";}', 'a:1:{i:0;s:2:\"15\";}','" . $created_by . "', '" . $updated_by . "', 'active'), 
    (" . $company_id . ", 'SRN IV Posting', 'srniv', 'a:2:{i:0;s:3:\" 35\";i:1;s:3:\" 42\";}', 'a:4:{i:0;s:2:\"34\";i:1;s:2:\"41\";i:2;s:2:\"39\";i:3;s:2:\"40\";}','" . $created_by . "', '" . $updated_by . "', 'active'), 
    (" . $company_id . ", 'Payment', 'Payment', 'a:2:{i:0;s:3:\" 19\";i:1;s:3:\" 20\";}', 'a:1:{i:0;s:2:\"35\";}','" . $created_by . "', '" . $updated_by . "', 'active'), 
    (" . $company_id . ", 'PGI', 'PGI', 'a:1:{i:0;s:3:\" 14\";}', 'a:1:{i:0;s:2:\"58\";}','" . $created_by . "', '" . $updated_by . "', 'active'), 
    (" . $company_id . ", 'SO Invoicing', 'SOInvoicing', 'a:6:{i:0;s:3:\" 47\";i:1;s:3:\" 43\";i:2;s:3:\" 44\";i:3;s:3:\" 40\";i:4;s:3:\" 39\";i:5;s:3:\" 27\";}', 'a:1:{i:0;s:2:\"22\";}','" . $created_by . "', '" . $updated_by . "', 'active'), 
    (" . $company_id . ", 'Collection', 'Collection', 'a:1:{i:0;s:3:\" 22\";}', 'a:2:{i:0;s:2:\"19\";i:1;s:2:\"20\";}','" . $created_by . "', '" . $updated_by . "', 'active'), 
    (" . $company_id . ", 'Production declaration Inventory issuance', 'ProductiondeclarationInventoryissuance', 'a:1:{i:0;s:3:\" 13\";}', 'a:1:{i:0;s:2:\"60\";}','" . $created_by . "', '" . $updated_by . "', 'active'), 
    (" . $company_id . ", 'FG/SFG declaration', 'FGSFGDeclaration', 'a:2:{i:0;s:3:\" 14\";i:1;s:3:\" 15\";}', 'a:1:{i:0;s:2:\"59\";}','" . $created_by . "', '" . $updated_by . "', 'active'), 
    (" . $company_id . ", 'Stock transport without profit', 'Stocktransportwithoutprofit', 'a:1:{i:0;s:3:\" 59\";}', 'a:2:{i:0;s:2:\"14\";i:1;s:2:\"15\";}','" . $created_by . "', '" . $updated_by . "', 'active')";

  $sqlQuery = mysqli_query($dbCon, $funcMappingsql);



  $queryGetNumRows = queryGetNumRows("SELECT * FROM `" . ERP_ACC_MAPPING . "` WHERE company_id=$company_id");
  if ($queryGetNumRows['numRows'] == 0) {
    $Mappingsql = "INSERT INTO `" . ERP_ACC_MAPPING . "` (`company_id`, `vendor_gl`, `customer_gl`, `itemsRM_gl`, `itemsSFG_gl`, `itemsFG_gl`, `bank_gl`, `cash_gl`, `map_created_by`, `map_updated_by`, `map_status`) VALUES (" . $company_id . ", '35', '22', '13', '15', '14', '19', '20', '" . $created_by . "', '" . $updated_by . "', 'active')";
    $sqlQuery = mysqli_query($dbCon, $Mappingsql);
  }

  if ($sqlQuery) {
    $returnData['status'] = "success";
    $returnData['message'] = "Query run successfully";
    $returnData['queryGetNumRows'] = $queryGetNumRows;
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
    $returnData['queryGetNumRows'] = $queryGetNumRows;
  }

  return $returnData;
}

//*************************************/INSERT/******************************************//
function createDataChartOfAccounts($POST = [])
{
  global $dbCon;
  $returnData = [];
  $isValidate = validate($POST, [
    "p_id" => "required",
    "personal_glcode_lvl" => "required",
    "typeAcc" => "required",
    "gl_code" => "required",
    "gl_label" => "required"
  ], [
    "p_id" => "Enter name",
    "personal_glcode_lvl" => "Enter Lvl",
    "typeAcc" => "Enter Type",
    "gl_code" => "Enter valid GL",
    "gl_label" => "Enter GL Label"
  ]);

  if ($isValidate["status"] == "success") {
    $customer_status = 'active';

    $company_id = $POST["company_id"];
    $p_id = $POST["p_id"];
    $personal_glcode_lvl = $POST["personal_glcode_lvl"] ?? 0;
    $gl_code = $POST["gl_code"];
    $gl_label = addslashes($POST["gl_label"]);
    $remark = addslashes($POST["remark"]) ?? "";
    $glSt = $POST["glSt"] ?? "";
    $typeAcc = $POST["typeAcc"] ?? "";
    $glStType = $POST["createdata"] ?? "";
    $created_by = $POST["created_by"] ?? "";
    $updated_by = $POST["updated_by"] ?? "";

    //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

    $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `company_id`='" . $company_id . "' AND `p_id`='" . $p_id . "' AND `gl_code`='" . $gl_code . "' AND `status`!='deleted'";
    if ($res = mysqli_query($dbCon, $sql)) {
      if (mysqli_num_rows($res) == 0) {

        $ins = "INSERT INTO `" . ERP_ACC_CHART_OF_ACCOUNTS . "` 
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
                                `created_by`='" . $created_by . "',
                                `updated_by`='" . $updated_by . "',
                                `status`='" . $customer_status . "'";

        $returnData = queryInsert($ins);
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Admin already exist";
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
    "gl_code" => "required",
    "gl_label" => "required"
  ], [
    "p_id" => "Enter name",
    "personal_glcode_lvl" => "Enter Lvl",
    "typeAcc" => "Enter Type",
    "gl_code" => "Enter valid GL",
    "gl_label" => "Enter GL Label"
  ]);

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
    "customer_gl" => "required",
    "bank_gl" => "required",
    "cash_gl" => "required",
  ], [
    "vendor_gl" => "Required",
    "itemsRM_gl" => "Required",
    "itemsFG_gl" => "Required",
    "itemsSFG_gl" => "Required",
    "customer_gl" => "Required",
    "bank_gl" => "Required",
    "cash_gl" => "Required",
  ]);

  if ($isValidate["status"] == "success") {
    $vendor_gl = $POST["vendor_gl"];
    $itemsRM_gl = $POST["itemsRM_gl"];
    $itemsFG_gl = $POST["itemsFG_gl"];
    $itemsSFG_gl = $POST["itemsSFG_gl"];
    $customer_gl = $POST["customer_gl"];
    $bank_gl = $POST["bank_gl"];
    $cash_gl = $POST["cash_gl"];

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
                                `cash_gl`='" . $cash_gl . "'
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
                                `cash_gl`='" . $cash_gl . "'";

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
  $returnData=queryGet($sql)['data'];
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
            <label class="tree_label" for="c<?= $labelId ?>"><?= $oneGlRow["gl_label"] ?><?php if($oneGlRow["p_id"]!=0){?>&nbsp;&nbsp; <button type="button" style="cursor: pointer;" class="btn btn-sm edit-gst" title="Edit" data-bs-toggle="modal" data-glid="<?= $oneGlRow['id']; ?>" data-bs-target="#GLedit"><i class="fa fa-edit po-list-icon"></i></button><?php } ?>
            <?php if ($oneGlRow['glStType'] == 'account') { ?> &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $oneGlRow['gl_code'];
                                                                                                                                                                                              } ?>

            </label>
          <?php
          } else {
          ?>
            <span class="tree_label">
              <p class="gl-type">
                <?= $oneGlRow["gl_label"] ?><?php if ($oneGlRow['glStType'] == 'account') { ?>
                <?php if($oneGlRow["p_id"]!=0){?>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" style="cursor: pointer;" class="btn btn-sm edit-gst" title="Edit" data-glid="<?= $oneGlRow['id']; ?>" data-bs-toggle="modal" data-bs-target="#GLedit"><i class="fa fa-edit po-list-icon"></i></button><?php }?>
           
              </p>
              <p class="gl-code">
                || <?php echo $oneGlRow['gl_code'];
                                            } ?>
              </p> </span>



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
  $queryObj = queryGet("SELECT * FROM  `erp_acc_coa_default` WHERE company_id=0 AND `p_id`=" . $p_id . " AND (`glSt` IS NULL OR `glSt`='') AND `status`!='deleted'", true);
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
            <input type="checkbox" id="c<?= $labelId ?>"  checked/>
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


function createGlTreeNewArray($p_id = 0,$id=null)
{
  global $company_id;
  $idcond='';
  if(!empty($id)){
    $idcond=" AND id=$id ";
  }
  $queryObj = queryGet("SELECT * FROM  `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id  AND `p_id`=" . $p_id ." " .$idcond."  AND (`glSt` IS NULL OR `glSt`='') AND `status`='active'", true);
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
//*************************************/END/******************************************//