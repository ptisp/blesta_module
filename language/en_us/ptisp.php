<?php
// Basics
$lang['PTisp.name'] = "LogicBoxes";
$lang['PTisp.module_row'] = "Account";
$lang['PTisp.module_row_plural'] = "Accounts";

// Module management
$lang['PTisp.add_module_row'] = "Add Account";
$lang['PTisp.manage.module_rows_title'] = "Accounts";
$lang['PTisp.manage.module_rows_heading.registrar'] = "Registrar Name";
$lang['PTisp.manage.module_rows_heading.reseller_id'] = "Reseller ID";
$lang['PTisp.manage.module_rows_heading.key'] = "API Key";
$lang['PTisp.manage.module_rows_heading.sandbox'] = "Sandbox";
$lang['PTisp.manage.module_rows_heading.options'] = "Options";
$lang['PTisp.manage.module_rows.edit'] = "Edit";
$lang['PTisp.manage.module_rows.delete'] = "Delete";
$lang['PTisp.manage.module_rows.confirm_delete'] = "Are you sure you want to delete this account?";
$lang['PTisp.manage.module_rows_no_results'] = "There are no accounts.";

// Row Meta
$lang['PTisp.row_meta.registrar'] = "Registrar Name";
$lang['PTisp.row_meta.reseller_id'] = "Reseller ID";
$lang['PTisp.row_meta.key'] = "Key";
$lang['PTisp.row_meta.sandbox'] = "Sandbox";
$lang['PTisp.row_meta.sandbox_true'] = "Yes";
$lang['PTisp.row_meta.sandbox_false'] = "No";

// Add row
$lang['PTisp.add_row.box_title'] = "Add LogicBoxes Account";
$lang['PTisp.add_row.basic_title'] = "Basic Settings";
$lang['PTisp.add_row.add_btn'] = "Add Account";

// Edit row
$lang['PTisp.edit_row.box_title'] = "Edit LogicBoxes Account";
$lang['PTisp.edit_row.basic_title'] = "Basic Settings";
$lang['PTisp.edit_row.add_btn'] = "Update Account";

// Package fields
$lang['PTisp.package_fields.type'] = "Type";
$lang['PTisp.package_fields.type_domain'] = "Domain Registration";
$lang['PTisp.package_fields.type_ssl'] = "SSL Certificate";
$lang['PTisp.package_fields.tld_options'] = "TLDs";
$lang['PTisp.package_fields.ns1'] = "Name Server 1";
$lang['PTisp.package_fields.ns2'] = "Name Server 2";
$lang['PTisp.package_fields.ns3'] = "Name Server 3";
$lang['PTisp.package_fields.ns4'] = "Name Server 4";
$lang['PTisp.package_fields.ns5'] = "Name Server 5";

// Service management
$lang['PTisp.tab_unavailable.message'] = "This information is not yet available.";

$lang['PTisp.tab_whois.title'] = "Whois";
$lang['PTisp.tab_whois.section_registrantcontact'] = "Registrant";
$lang['PTisp.tab_whois.section_admincontact'] = "Administrative";
$lang['PTisp.tab_whois.section_techcontact'] = "Technical";
$lang['PTisp.tab_whois.section_billingcontact'] = "Billing";
$lang['PTisp.tab_whois.field_submit'] = "Update Whois";

$lang['PTisp.tab_nameservers.title'] = "Name Servers";
$lang['PTisp.tab_nameserver.field_ns'] = "Name Server %1\$s"; // %1$s is the name server number
$lang['PTisp.tab_nameservers.field_submit'] = "Update Name Servers";

$lang['PTisp.tab_settings.title'] = "Settings";
$lang['PTisp.tab_settings.field_registrar_lock'] = "Registrar Lock";
$lang['PTisp.tab_settings.field_registrar_lock_yes'] = "Set the registrar lock. Recommended to prevent unauthorized transfer.";
$lang['PTisp.tab_settings.field_registrar_lock_no'] = "Release the registrar lock so the domain can be transferred.";
$lang['PTisp.tab_settings.field_request_epp'] = "Request EPP Code/Transfer Key";
$lang['PTisp.tab_settings.field_submit'] = "Update Settings";

// Errors
$lang['PTisp.!error.registrar.valid'] = "Please enter a registrar name.";
$lang['PTisp.!error.reseller_id.valid'] = "Please enter a reseller ID.";
$lang['PTisp.!error.key.valid'] = "Please enter a key.";
$lang['PTisp.!error.key.valid_connection'] = "The reseller ID and key combination appear to be invalid, or your LogicBoxes account may not be configured to allow API access.";


// Domain Transfer Fields
$lang['PTisp.transfer.domain-name'] = "Domain Name";
$lang['PTisp.transfer.auth-code'] = "EPP Code";

// Domain Fields
$lang['PTisp.domain.domain-name'] = "Domain Name";

// Nameserver Fields
$lang['PTisp.nameserver.ns1'] = "Name Server 1";
$lang['PTisp.nameserver.ns2'] = "Name Server 2";
$lang['PTisp.nameserver.ns3'] = "Name Server 3";
$lang['PTisp.nameserver.ns4'] = "Name Server 4";
$lang['PTisp.nameserver.ns5'] = "Name Server 5";

// Contact Fields
$lang['PTisp.contact.customer-id'] = "Customer ID";
$lang['PTisp.contact.type'] = "Type";
$lang['PTisp.contact.name'] = "Name";
$lang['PTisp.contact.company'] = "Company";
$lang['PTisp.contact.email'] = "Email";
$lang['PTisp.contact.address-line-1'] = "Address 1";
$lang['PTisp.contact.address-line-2'] = "Address 2";
$lang['PTisp.contact.city'] = "City";
$lang['PTisp.contact.state'] = "State";
$lang['PTisp.contact.zipcode'] = "Zip";
$lang['PTisp.contact.country'] = "Country";
$lang['PTisp.contact.phone-cc'] = "Phone Country Dialing Code";
$lang['PTisp.contact.phone'] = "Phone Number";
$lang['PTisp.contact.fax-cc'] = "Fax Country Dialing Code";
$lang['PTisp.contact.fax'] = "Fax Number";

// Customer Fields
$lang['PTisp.customer.username'] = "Username";
$lang['PTisp.customer.passwd'] = "Password";
$lang['PTisp.customer.lang-pref'] = "Language";


// .EU domain fields
$lang['PTisp.domain.EUAgreeWhoisPolicy'] = "Whois Policy";
$lang['PTisp.domain.EUAgreeWhoisPolicy.yes'] = "I hereby agree that the Registry is entitled to transfer the data contained in this application to third parties(i) if ordered to do so by a public authority, carrying out its legitimate tasks; and (ii) upon demand of an ADR Provider as mentioned in section 16 of the Terms and Conditions which are published at www.eurid.eu; and (iii) as provided in Section 2 (WHOIS look-up facility) of the .eu Domain Name WHOIS Policy which is published at www.eurid.eu.";
$lang['PTisp.domain.EUAgreeDeletePolicy'] = "Deleteion Rules";
$lang['PTisp.domain.EUAgreeDeletePolicy.yes'] = "I agree and acknowledge to the special renewal and expiration terms set forth below for this domain name, including those terms set forth in the Registration Agreement. I understand that unless I have set this domain for autorenewal, this domain name must be explicitly renewed by the expiration date or the 20th of the month of expiration, whichever is sooner. (e.g. If the name expires on Sept 4th, 2008, then a manual renewal must be received by Sept 4th, 2008. If name expires on Sep 27th, 2008, the renewal request must be received prior to Sep 20th, 2008). If the name is not manually renewed or previously set to autorenew, a delete request will be issued by PTisp. When a delete request is issued, the name will remain fully functional in my account until expiration, but will no longer be renewable nor will I be able to make any modifications to the name. These terms are subject to change.";

?>