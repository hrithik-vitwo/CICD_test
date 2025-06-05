<?php
// --------------------Main Table 
$tablePrefix="tbl_";

if(!defined("TBL_WEBMASTER")) 						define("TBL_WEBMASTER",$tablePrefix."webmaster_login");
if(!defined("TBL_WEBMASTER_TYPES")) 				define("TBL_WEBMASTER_TYPES",$tablePrefix."webmaster_types");
if(!defined("TBL_SITEINFO")) 						define("TBL_SITEINFO",$tablePrefix."webmaster_site_info");

if(!defined("TBL_ADMIN_TABLESETTINGS")) 			define("TBL_ADMIN_TABLESETTINGS",$tablePrefix."admin_tablesettings");
if(!defined("TBL_COMPANY_ADMIN_TABLESETTINGS")) 	define("TBL_COMPANY_ADMIN_TABLESETTINGS",$tablePrefix."company_admin_tablesettings");
if(!defined("TBL_BRANCH_ADMIN_TABLESETTINGS")) 		define("TBL_BRANCH_ADMIN_TABLESETTINGS",$tablePrefix."branch_admin_tablesettings");
if(!defined("TBL_VENDOR_ADMIN_TABLESETTINGS")) 		define("TBL_VENDOR_ADMIN_TABLESETTINGS",$tablePrefix."vendor_admin_tablesettings");
if(!defined("TBL_CUSTOMER_ADMIN_TABLESETTINGS")) 	define("TBL_CUSTOMER_ADMIN_TABLESETTINGS",$tablePrefix."customer_admin_tablesettings");
if(!defined("TBL_BRANCH_ADMIN_DETAILS")) 	define("TBL_BRANCH_ADMIN_DETAILS",$tablePrefix."branch_admin_details");

// --------------------Others Table 
$tablePrefix="erp_";

if(!defined("ERP_DASH_COMPONENT_MASTER")) 			define("ERP_DASH_COMPONENT_MASTER",$tablePrefix."dash_component_master");
if(!defined("ERP_USER_DASHBOARD")) 					define("ERP_USER_DASHBOARD",$tablePrefix."user_dashboard");
if(!defined("ERP_DASH_COMPONENT")) 					define("ERP_DASH_COMPONENT",$tablePrefix."dashboard_component");

if(!defined("ERP_ACC_JOURNAL")) 					define("ERP_ACC_JOURNAL",$tablePrefix."acc_journal");
if(!defined("ERP_ACC_CREDIT")) 					    define("ERP_ACC_CREDIT",$tablePrefix."acc_credit");
if(!defined("ERP_ACC_DEBIT")) 					    define("ERP_ACC_DEBIT",$tablePrefix."acc_debit");
// It is on tables2.php if(!defined("ERP_ACC_CHART_OF_ACCOUNTS")) 	        define("ERP_ACC_CHART_OF_ACCOUNTS",$tablePrefix."acc_chart_of_accounts");
if(!defined("ERP_ACC_MAPPING")) 	                define("ERP_ACC_MAPPING",$tablePrefix."acc_gl_mapping");
if(!defined("ERP_ACC_FUNCTIONAL_MAPPING"))          define("ERP_ACC_FUNCTIONAL_MAPPING",$tablePrefix."acc_functional_mapping");

if(!defined("ERP_INVENTORY_ITEMS")) 				define("ERP_INVENTORY_ITEMS",$tablePrefix."inventory_items");
if(!defined("ERP_INVENTORY_MASTR_GOOD_GROUPS")) 	define("ERP_INVENTORY_MASTR_GOOD_GROUPS",$tablePrefix."inventory_mstr_good_groups");
if(!defined("ERP_INVENTORY_MASTR_GOOD_TYPES")) 		define("ERP_INVENTORY_MASTR_GOOD_TYPES",$tablePrefix."inventory_mstr_good_types");
if(!defined("ERP_INVENTORY_MASTR_TIME_UNITS")) 		define("ERP_INVENTORY_MASTR_TIME_UNITS",$tablePrefix."inventory_mstr_time_units");
if(!defined("ERP_INVENTORY_MASTR_UOM")) 			define("ERP_INVENTORY_MASTR_UOM",$tablePrefix."inventory_mstr_uom");
if(!defined("ERP_INVENTORY_PURCHESING_VALUES")) 	define("ERP_INVENTORY_PURCHESING_VALUES",$tablePrefix."inventory_purchesing_values");
if(!defined("ERP_INVENTORY_MASTR_PURCHASE_GROUPS")) define("ERP_INVENTORY_MASTR_PURCHASE_GROUPS",$tablePrefix."inventory_mstr_purchase_groups");
if(!defined("ERP_INVENTORY_ITEM_PRICE"))            define("ERP_INVENTORY_ITEM_PRICE",$tablePrefix."inventory_item_price");
if(!defined("ERP_INVENTORY_MASTER_UOM"))            define("ERP_INVENTORY_MASTER_UOM",$tablePrefix."inventory_mstr_uom");

if(!defined("ERP_VENDOR_BANK_DETAILS")) 		    define("ERP_VENDOR_BANK_DETAILS",$tablePrefix."vendor_bank_details");
if(!defined("ERP_VENDOR_BUSINESS_PLACES")) 		    define("ERP_VENDOR_BUSINESS_PLACES",$tablePrefix."vendor_bussiness_places");
if(!defined("ERP_COMPANIES")) 						define("ERP_COMPANIES",$tablePrefix."companies");
if(!defined("ERP_BRANCHES")) 						define("ERP_BRANCHES",$tablePrefix."branches");
if(!defined("ERP_CUSTOMER")) 						define("ERP_CUSTOMER",$tablePrefix."customer");
if(!defined("ERP_CUSTOMER_ADDRESS")) 				define("ERP_CUSTOMER_ADDRESS",$tablePrefix."customer_address");
if(!defined("ERP_VENDOR_DETAILS")) 					define("ERP_VENDOR_DETAILS",$tablePrefix."vendor_details");
if(!defined("ERP_CURRENCY_TYPE")) 					define("ERP_CURRENCY_TYPE",$tablePrefix."currency_type");
if(!defined("ERP_LANGUAGE")) 					    define("ERP_LANGUAGE",$tablePrefix."language");
if(!defined("ERP_CREDIT_TERMS")) 					define("ERP_CREDIT_TERMS",$tablePrefix."credit_terms");
if(!defined("ERP_COMPANY_FUNCTIONALITIES")) 	    define("ERP_COMPANY_FUNCTIONALITIES",$tablePrefix."company_functionalities");
if(!defined("ERP_BRANCH_OTHERSLOCATION")) 	        define("ERP_BRANCH_OTHERSLOCATION",$tablePrefix."branch_otherslocation");

if(!defined("ERP_PURCHASE_BILLS"))                  define("ERP_PURCHASE_BILLS",$tablePrefix."branch_bills");
if(!defined("ERP_PURCHASE_BILLS_ITEMS"))            define("ERP_PURCHASE_BILLS_ITEMS",$tablePrefix."branch_bills_items");
if(!defined("ERP_COST_CENTER"))                     define("ERP_COST_CENTER",$tablePrefix."cost_center");

if(!defined("ERP_BRANCH_SALES_ORDER"))                      define("ERP_BRANCH_SALES_ORDER",$tablePrefix."branch_sales_order");
if(!defined("ERP_BRANCH_SALES_ORDER_ITEMS"))                define("ERP_BRANCH_SALES_ORDER_ITEMS",$tablePrefix."branch_sales_order_items");
if(!defined("ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE"))    define("ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE",$tablePrefix."branch_sales_order_delivery_schedule");

if(!defined("ERP_BRANCH_SALES_ORDER_DELIVERY"))             define("ERP_BRANCH_SALES_ORDER_DELIVERY",$tablePrefix."branch_sales_order_delivery");
if(!defined("ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS"))       define("ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS",$tablePrefix."branch_sales_order_delivery_items");
if(!defined("ERP_BRANCH_SALES_ORDER_DELIVERY_PGI"))         define("ERP_BRANCH_SALES_ORDER_DELIVERY_PGI",$tablePrefix."branch_sales_order_delivery_pgi");
if(!defined("ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS_PGI"))   define("ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS_PGI",$tablePrefix."branch_sales_order_delivery_items_pgi");
if(!defined("ERP_HSN_CODE"))                                define("ERP_HSN_CODE",$tablePrefix."hsn_code");

if(!defined("ERP_BRANCH_PURCHASE_ORDER"))                      define("ERP_BRANCH_PURCHASE_ORDER",$tablePrefix."branch_purchase_order");
if(!defined("ERP_BRANCH_PURCHASE_ORDER_ITEMS"))                define("ERP_BRANCH_PURCHASE_ORDER_ITEMS",$tablePrefix."branch_purchase_order_items");
if(!defined("ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE"))    define("ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE",$tablePrefix."branch_purchase_order_delivery_schedule");

if(!defined("ERP_BRANCH_PURCHASE_REQUEST"))                    define("ERP_BRANCH_PURCHASE_REQUEST",$tablePrefix."branch_purchase_request");
if(!defined("ERP_BRANCH_PURCHASE_REQUEST_ITEMS"))                             define("ERP_BRANCH_PURCHASE_REQUEST_ITEMS",$tablePrefix."branch_purchase_request_items");
if(!defined("ERP_RFQ_LIST"))                                   define("ERP_RFQ_LIST",$tablePrefix."rfq_list");
if(!defined("ERP_RFQ_LIST_ITEM"))                              define("ERP_RFQ_LIST_ITEM",$tablePrefix."rfq_items");
if(!defined("ERP_RFQ_VENDOR_LIST"))                            define("ERP_RFQ_VENDOR_LIST",$tablePrefix."rfq_vendor_list");

if(!defined("ERP_SO_PURCHASE_REQUEST"))                        define("ERP_SO_PURCHASE_REQUEST",$tablePrefix."branch_so_purchase_request");
if(!defined("ERP_KAM"))                                        define("ERP_KAM",$tablePrefix."kam");
if(!defined("ERP_INVENTORY_STOCKS_FG"))                        define("ERP_INVENTORY_STOCKS_FG",$tablePrefix."inventory_stocks_fg");

if(!defined("ERP_WAREHOUSE"))                                  define("ERP_WAREHOUSE",$tablePrefix."storage_warehouse");
if(!defined("ERP_STORAGE_LOCATION"))                           define("ERP_STORAGE_LOCATION",$tablePrefix."storage_location");
if(!defined("ERP_BIN"))                                        define("ERP_BIN",$tablePrefix."storage_bin");

// imranali59059 ⤵️
if(!defined("ERP_BRANCH_SALES_ORDER_INVOICES"))    define("ERP_BRANCH_SALES_ORDER_INVOICES",$tablePrefix."branch_sales_order_invoices");
if(!defined("ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS"))    define("ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS",$tablePrefix."branch_sales_order_invoice_items");
// imranali59059 ⤴️🔚

//02-11-2022 GRN Tables ->Rachhel
if(!defined("ERP_GRN"))    define("ERP_GRN",$tablePrefix."grn");
if(!defined("ERP_GRN_GOODS"))    define("ERP_GRN_GOODS",$tablePrefix."grn_goods");

// imranali59059 ⤵️
if(!defined("ERP_GRN_PAYMENTS"))    define("ERP_GRN_PAYMENTS",$tablePrefix."grn_payments");
if(!defined("ERP_GRN_PAYMENTS_LOG"))    define("ERP_GRN_PAYMENTS_LOG",$tablePrefix."grn_payments_log");
// imranali59059 ⤴️🔚

if(!defined("ERP_VENDOR_ITEM_MAP"))    define("ERP_VENDOR_ITEM_MAP",$tablePrefix."vendor_item_map");
if(!defined("ERP_RM_STOCKS"))    define("ERP_RM_STOCKS",$tablePrefix."inventory_stocks_rm");

if(!defined("ERP_BOM"))    define("ERP_BOM",$tablePrefix."bom");
if(!defined("ERP_BOM_ITEMS"))    define("ERP_BOM_ITEMS",$tablePrefix."bom_items");
if(!defined("ERP_PRODUCTION_ORDER"))    define("ERP_PRODUCTION_ORDER",$tablePrefix."production_order");

// imranali59059 ⤵️
if(!defined("ERP_STATUS_MASTER"))    define("ERP_STATUS_MASTER",$tablePrefix."status_master");
if(!defined("ERP_BRANCH_SALES_ORDER_PAYMENTS"))    define("ERP_BRANCH_SALES_ORDER_PAYMENTS",$tablePrefix."branch_sales_order_payments");
if(!defined("ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG"))    define("ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG",$tablePrefix."branch_sales_order_payments_log");
if(!defined("ERP_INVOICE_MAIL_LOG"))    define("ERP_INVOICE_MAIL_LOG",$tablePrefix."invoice_mail_log");
if(!defined("ERP_BRANCH_SALES_ORDER_OTHER_COST"))    define("ERP_BRANCH_SALES_ORDER_OTHER_COST",$tablePrefix."branch_sales_order_other_cost");
if(!defined("ERP_INVENTORY_STOCKS_SUMMARY"))    define("ERP_INVENTORY_STOCKS_SUMMARY",$tablePrefix."inventory_stocks_summary");
if(!defined("ERP_INVENTORY_STOCKS_LOG"))    define("ERP_INVENTORY_STOCKS_LOG",$tablePrefix."inventory_stocks_log");
if(!defined("ERP_ACC_BANK_CASH_ACCOUNTS"))    define("ERP_ACC_BANK_CASH_ACCOUNTS",$tablePrefix."acc_bank_cash_accounts");
if(!defined("ERP_PACKAGE_MANAGEMENT"))    define("ERP_PACKAGE_MANAGEMENT",$tablePrefix."package_management");
if(!defined("ERP_PACKAGE_VARIANT"))    define("ERP_PACKAGE_VARIANT",$tablePrefix."package_variant");
if(!defined("ERP_PACKAGE_VARIANT"))    define("ERP_PACKAGE_VARIANT",$tablePrefix."package_variant");

if(!defined("ERP_DEBIT_NOTES"))    define("ERP_DEBIT_NOTES",$tablePrefix."debit_notes");
if(!defined("ERP_DEBIT_NOTE_DEBIT"))    define("ERP_DEBIT_NOTE_DEBIT",$tablePrefix."debit_note_debit");
if(!defined("ERP_DEBIT_NOTE_CREDIT"))    define("ERP_DEBIT_NOTE_CREDIT",$tablePrefix."debit_note_credit");
if(!defined("ERP_CREDIT_NOTES"))    define("ERP_CREDIT_NOTES",$tablePrefix."credit_notes");
if(!defined("ERP_CREDIT_NOTE_DEBIT"))    define("ERP_CREDIT_NOTE_DEBIT",$tablePrefix."credit_note_debit");
if(!defined("ERP_CREDIT_NOTE_CREDIT"))    define("ERP_CREDIT_NOTE_CREDIT",$tablePrefix."credit_note_credit");
if(!defined("ERP_GRNINVOICE"))    define("ERP_GRNINVOICE",$tablePrefix."grninvoice");
if(!defined("ERP_BRANCH_QUOTATIONS"))    define("ERP_BRANCH_QUOTATIONS",$tablePrefix."branch_quotations");
if(!defined("ERP_BRANCH_QUOTATION_ITEMS"))    define("ERP_BRANCH_QUOTATION_ITEMS",$tablePrefix."branch_quotation_items");
if(!defined("ERP_INVOICE_TYPE"))    define("ERP_INVOICE_TYPE",$tablePrefix."invoice_type");
if(!defined("ERP_BRANCH_SUBSCRIPTION_INVOICE"))    define("ERP_BRANCH_SUBSCRIPTION_INVOICE",$tablePrefix."branch_subscription_invoice");
if(!defined("ERP_SETTINGS_EMAIL_CUSTOMER_INVOICE"))    define("ERP_SETTINGS_EMAIL_CUSTOMER_INVOICE",$tablePrefix."settings_email_customer_invoice");
if(!defined("ERP_CUSTOMER_INVOICE_LOGS"))    define("ERP_CUSTOMER_INVOICE_LOGS",$tablePrefix."customer_invoice_logs");
if(!defined("ERP_INVENTORY_MSTR_GOOD_TYPES"))    define("ERP_INVENTORY_MSTR_GOOD_TYPES",$tablePrefix."inventory_mstr_good_types");
if(!defined("ERP_INVENTORY_MSTR_UOM"))    define("ERP_INVENTORY_MSTR_UOM",$tablePrefix."inventory_mstr_uom");
if(!defined("ERP_DOCUMENT_DECLARATION"))    define("ERP_DOCUMENT_DECLARATION",$tablePrefix."document_declaration");
if(!defined("ERP_BRANCH_JOB_ORDER_LOGS"))    define("ERP_BRANCH_JOB_ORDER_LOGS",$tablePrefix."branch_job_order_logs");
if(!defined("ERP_ATTACH_DOCUMENTS"))    define("ERP_ATTACH_DOCUMENTS",$tablePrefix."attach_documents");
if(!defined("ERP_PARTY_ORDER"))    define("ERP_PARTY_ORDER",$tablePrefix."party_order");
if(!defined("ERP_PARTY_ORDER_ITEM"))    define("ERP_PARTY_ORDER_ITEM",$tablePrefix."party_order_item");
if(!defined("ERP_PROFORMA_INVOICES"))    define("ERP_PROFORMA_INVOICES",$tablePrefix."proforma_invoices");
if(!defined("ERP_PROFORMA_INVOICE_ITEMS"))    define("ERP_PROFORMA_INVOICE_ITEMS",$tablePrefix."proforma_invoice_items");
if(!defined("ERP_WALK_IN_INVOICES"))    define("ERP_WALK_IN_INVOICES",$tablePrefix."walk_in_invoices");
if(!defined("ERP_CONFIG_INVOICES"))    define("ERP_CONFIG_INVOICES",$tablePrefix."config_invoices");
if(!defined("ERP_INVENTORY_ITEM_IMAGES"))    define("ERP_INVENTORY_ITEM_IMAGES",$tablePrefix."inventory_item_images");
if(!defined("ERP_ITEM_SPECIFICATION"))    define("ERP_ITEM_SPECIFICATION",$tablePrefix."item_specification");
// imranali59059 ⤴️🔚

if(!defined("ERP_YEAR_VARIANT"))    define("ERP_YEAR_VARIANT",$tablePrefix."year_variant");
if(!defined("ERP_MONTH_VARIANT"))    define("ERP_MONTH_VARIANT",$tablePrefix."month_variant");
if(!defined("ERP_INVENTORY_STORAGE"))    define("ERP_INVENTORY_STORAGE",$tablePrefix."inventory_item_storage_details");
if(!defined("ERP_SERVICES"))    define("ERP_SERVICES",$tablePrefix."services");

if(!defined("ERP_COMPANY_LICENCE"))                 define("ERP_COMPANY_LICENCE",$tablePrefix."company_licence");
if(!defined("ERP_COMPANY_LICENCE_RECHARGE_LOG"))    define("ERP_COMPANY_LICENCE_RECHARGE_LOG",$tablePrefix."company_licence_recharge_log");

?>